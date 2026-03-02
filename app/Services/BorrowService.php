<?php

namespace App\Services;

use App\Models\Borrow;
use App\Models\Item;
use App\Models\Notification;
use App\Models\User;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BorrowService
{
    /**
     * Create a new borrow request.
     */
    public function createBorrow(array $validated): Borrow
    {
        return DB::transaction(function () use ($validated) {
            $item = Item::findOrFail($validated['item_id']);

            $borrow = Borrow::create([
                'user_id' => auth()->id(),
                'item_id' => $validated['item_id'],
                'quantity' => 1,
                'borrow_date' => Carbon::parse($validated['borrow_date']),
                'due_date' => Carbon::parse($validated['due_date']),
                'status' => 'pending',
                'approval_status' => 'pending',
                'notes' => $validated['notes'],
                'condition_at_borrow' => $item->condition,
            ]);

            ActivityLog::log('create', 'peminjaman', 'Mengajukan peminjaman barang: ' . ($item->name ?? '-') . ' (ID: ' . $borrow->id . ')');

            $this->notifyAdminsNewRequest($borrow);

            return $borrow;
        });
    }

    /**
     * Approve a borrow request.
     */
    public function approveBorrow(Borrow $borrow): void
    {
        DB::transaction(function () use ($borrow) {
            $item = $borrow->item;

            if ($item->stock < $borrow->quantity) {
                throw new \RuntimeException('Stok barang tidak mencukupi untuk peminjaman ini.');
            }

            $borrow->update([
                'approval_status' => 'approved',
                'status' => 'borrowed',
                'approved_by' => Auth::id(),
                'approved_at' => Carbon::now(),
            ]);

            $item->updateStatus(Item::STATUS_BORROWED);

            $this->notifyBorrowStatus($borrow, 'approved');

            ActivityLog::log('approve', 'peminjaman', 'Menyetujui peminjaman barang: ' . ($borrow->item->name ?? '-') . ' (ID: ' . $borrow->id . ')');

            $this->autoRejectOtherPending($borrow);
        });
    }

    /**
     * Reject a borrow request.
     */
    public function rejectBorrow(Borrow $borrow, string $reason): void
    {
        DB::transaction(function () use ($borrow, $reason) {
            $borrow->update([
                'approval_status' => 'rejected',
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => Carbon::now(),
                'rejection_reason' => $reason,
            ]);

            $this->notifyBorrowStatus($borrow, 'rejected');

            ActivityLog::log('reject', 'peminjaman', 'Menolak peminjaman barang: ' . ($borrow->item->name ?? '-') . ' (ID: ' . $borrow->id . ')');
        });
    }

    /**
     * Update borrow status (return or lost).
     */
    public function updateBorrowStatus(Borrow $borrow, string $action, ?string $conditionOnReturn = null): void
    {
        DB::transaction(function () use ($borrow, $action, $conditionOnReturn) {
            $item = $borrow->item;

            if ($action === 'lost') {
                $borrow->update([
                    'status' => 'lost',
                    'return_date' => Carbon::now(),
                ]);
                $item->updateStatus(Item::STATUS_LOST);
                ActivityLog::log('lost', 'peminjaman', 'Barang dinyatakan hilang: ' . ($item->name ?? '-') . ' (ID: ' . $borrow->id . ')');
            } else {
                $borrow->update([
                    'status' => 'returned',
                    'return_date' => Carbon::now(),
                    'condition_on_return' => $conditionOnReturn,
                ]);
                $item->updateCondition($conditionOnReturn);
                ActivityLog::log('return', 'peminjaman', 'Mengembalikan barang: ' . ($item->name ?? '-') . ' (ID: ' . $borrow->id . ')');
            }
        });
    }

    /**
     * Notify admin/petugas of a new borrow request.
     */
    private function notifyAdminsNewRequest(Borrow $borrow): void
    {
        $adminUsers = User::whereIn('role', ['admin', 'petugas'])->get();
        $notifications = [];

        foreach ($adminUsers as $admin) {
            $notifications[] = [
                'user_id' => $admin->id,
                'title' => 'Pengajuan Peminjaman Baru',
                'message' => "Pengguna {$borrow->user->name} mengajukan peminjaman barang {$borrow->item->name}",
                'type' => 'borrow_request',
                'data' => json_encode(['borrow_id' => $borrow->id, 'action_link' => route('borrows.show', $borrow->id)]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($notifications)) {
            Notification::insert($notifications);
        }
    }

    /**
     * Notify borrow owner about status change.
     */
    private function notifyBorrowStatus(Borrow $borrow, string $type): void
    {
        Notification::create([
            'user_id' => $borrow->user_id,
            'title' => $type === 'approved' ? 'Peminjaman Disetujui' : 'Peminjaman Ditolak',
            'message' => $type === 'approved'
                ? "Pengajuan peminjaman barang {$borrow->item->name} telah disetujui."
                : "Pengajuan peminjaman barang {$borrow->item->name} ditolak. Alasan: {$borrow->rejection_reason}",
            'type' => 'borrow_approval',
            'data' => json_encode(['borrow_id' => $borrow->id, 'status' => $type, 'action_link' => route('borrows.show', $borrow->id)]),
        ]);
    }

    /**
     * Auto-reject other pending borrows for the same item.
     */
    private function autoRejectOtherPending(Borrow $borrow): void
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, Borrow> $otherPendings */
        $otherPendings = Borrow::where('item_id', $borrow->item_id)
            ->where('id', '!=', $borrow->id)
            ->where('approval_status', 'pending')
            ->get();

        /** @var Borrow $pending */
        foreach ($otherPendings as $pending) {
            $pending->update([
                'approval_status' => 'rejected',
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => Carbon::now(),
                'rejection_reason' => 'Barang sudah dipinjam oleh pengguna lain.',
            ]);
            $this->notifyBorrowStatus($pending, 'rejected');
            ActivityLog::log('reject', 'peminjaman', 'Menolak otomatis pengajuan lain barang: ' . ($pending->item->name ?? '-') . ' (ID: ' . $pending->id . ') karena sudah dipinjam user lain.');
        }
    }
}
