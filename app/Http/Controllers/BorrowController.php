<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use App\Models\Item;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class BorrowController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Borrow::with(['user', 'item', 'approvedBy']);
        
        // Jika user biasa, hanya tampilkan peminjaman miliknya
        if ($user->isUser()) {
            $query->where('user_id', $user->id);
        }
        
        // Filter berdasarkan request
        $query->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->approval_status, function ($query, $approval_status) {
                return $query->where('approval_status', $approval_status);
            })
            ->when($request->search, function ($query, $search) {
                return $query->whereHas('item', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            });

        $borrows = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('borrows.index', compact('borrows'));
    }

    public function create()
    {
        $items = Item::where('status', Item::STATUS_AVAILABLE)
            ->where('condition', '!=', 'Rusak Berat')
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        return view('borrows.create', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => ['required', 'exists:items,id', function ($attribute, $value, $fail) {
                $item = Item::find($value);
                if (!$item) return;
                if ($item->status !== Item::STATUS_AVAILABLE) {
                    $fail('Barang ini tidak tersedia untuk dipinjam.');
                }
                if ($item->condition === 'Rusak Berat') {
                    $fail('Barang dengan kondisi "Rusak Berat" tidak dapat dipinjam.');
                }
                if ($item->stock < 1) {
                    $fail('Stok barang tidak mencukupi.');
                }
            }],
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:borrow_date',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $item = Item::findOrFail($validated['item_id']);

            $borrow = Borrow::create([
                'user_id' => auth()->id(),
                'item_id' => $validated['item_id'],
                'quantity' => 1, // Default quantity 1
                'borrow_date' => Carbon::parse($validated['borrow_date']),
                'due_date' => Carbon::parse($validated['due_date']),
                'status' => 'pending', // Status awal pending
                'approval_status' => 'pending', // Approval status pending
                'notes' => $validated['notes'],
                'condition_at_borrow' => $item->condition,
            ]);

            // Log aktivitas pengajuan peminjaman
            \App\Models\ActivityLog::log('create', 'peminjaman', 'Mengajukan peminjaman barang: ' . ($item->name ?? '-') . ' (ID: ' . $borrow->id . ')');

            // Buat notifikasi untuk admin/petugas
            $this->createApprovalNotification($borrow);

            DB::commit();

            return redirect()
                ->route('borrows.show', $borrow)
                ->with('success', 'Pengajuan peminjaman berhasil dibuat dan menunggu persetujuan');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Borrow $borrow)
    {
        $user = Auth::user();
        
        // Cek apakah user biasa mencoba melihat peminjaman orang lain
        if ($user->isUser() && $borrow->user_id != $user->id) {
            return redirect()->route('borrows.index')
                ->with('error', 'Anda tidak memiliki akses untuk melihat peminjaman ini.');
        }
        
        $borrow->load(['user', 'item', 'attachments', 'approvedBy']);
        return view('borrows.show', compact('borrow'));
    }

    public function approve(Request $request, Borrow $borrow)
    {
        // Hanya admin dan petugas yang boleh menyetujui
        if (Auth::user()->isUser()) {
            return redirect()->route('borrows.show', $borrow)
                ->with('error', 'Anda tidak memiliki izin untuk menyetujui peminjaman.');
        }

        if (!$borrow->canBeApproved()) {
            return back()->with('error', 'Peminjaman ini tidak dapat disetujui.');
        }

        try {
            DB::beginTransaction();

            $item = $borrow->item;

            // Cek ketersediaan stok
            if ($item->stock < $borrow->quantity) {
                return back()->with('error', 'Stok barang tidak mencukupi untuk peminjaman ini.');
            }

            // Update status peminjaman
            $borrow->update([
                'approval_status' => 'approved',
                'status' => 'borrowed',
                'approved_by' => Auth::id(),
                'approved_at' => Carbon::now(),
            ]);

            // Ubah status item menjadi 'Dipinjam' karena setiap item adalah unit tunggal
            $item->updateStatus(Item::STATUS_BORROWED);

            // Buat notifikasi untuk user
            $this->createApprovalNotification($borrow, 'approved');

            // Log aktivitas persetujuan
            \App\Models\ActivityLog::log('approve', 'peminjaman', 'Menyetujui peminjaman barang: ' . ($borrow->item->name ?? '-') . ' (ID: ' . $borrow->id . ')');

            // Tolak otomatis semua pengajuan lain yang pending untuk unit barang yang sama
            $otherPendings = Borrow::where('item_id', $item->id)
                ->where('id', '!=', $borrow->id)
                ->where('approval_status', 'pending')
                ->get();
            foreach ($otherPendings as $pending) {
                $pending->update([
                    'approval_status' => 'rejected',
                    'status' => 'rejected',
                    'approved_by' => Auth::id(),
                    'approved_at' => Carbon::now(),
                    'rejection_reason' => 'Barang sudah dipinjam oleh pengguna lain.'
                ]);
                // Kirim notifikasi ke user
                $this->createApprovalNotification($pending, 'rejected');
                // Log aktivitas penolakan otomatis
                \App\Models\ActivityLog::log('reject', 'peminjaman', 'Menolak otomatis pengajuan lain barang: ' . ($pending->item->name ?? '-') . ' (ID: ' . $pending->id . ') karena sudah dipinjam user lain.');
            }

            DB::commit();

            return redirect()
                ->route('borrows.show', $borrow)
                ->with('success', 'Peminjaman berhasil disetujui');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyetujui peminjaman: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Borrow $borrow)
    {
        // Hanya admin dan petugas yang boleh menolak
        if (Auth::user()->isUser()) {
            return redirect()->route('borrows.show', $borrow)
                ->with('error', 'Anda tidak memiliki izin untuk menolak peminjaman.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if (!$borrow->canBeRejected()) {
            return back()->with('error', 'Peminjaman ini tidak dapat ditolak.');
        }

        try {
            DB::beginTransaction();

            // Update status peminjaman
            $borrow->update([
                'approval_status' => 'rejected',
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => Carbon::now(),
                'rejection_reason' => $validated['rejection_reason'],
            ]);

            // Buat notifikasi untuk user
            $this->createApprovalNotification($borrow, 'rejected');

            // Log aktivitas penolakan
            \App\Models\ActivityLog::log('reject', 'peminjaman', 'Menolak peminjaman barang: ' . ($borrow->item->name ?? '-') . ' (ID: ' . $borrow->id . ')');

            DB::commit();

            return redirect()
                ->route('borrows.show', $borrow)
                ->with('success', 'Peminjaman berhasil ditolak');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menolak peminjaman: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Borrow $borrow)
    {
        // Hanya admin dan petugas yang boleh mengubah status
        if (Auth::user()->isUser()) {
            return redirect()->route('borrows.show', $borrow)
                ->with('error', 'Anda tidak memiliki izin untuk mengubah status peminjaman.');
        }
        
        $validated = $request->validate([
            'action' => 'required|string|in:returned,lost',
            'condition_on_return' => 'required_if:action,returned|string|in:Baik,Rusak Ringan,Rusak Berat'
        ]);

        if (in_array($borrow->status, ['returned', 'lost'])) {
            return back()->with('error', 'Status peminjaman ini sudah final.');
        }

        if ($borrow->approval_status !== 'approved') {
            return back()->with('error', 'Hanya peminjaman yang sudah disetujui yang dapat dikembalikan.');
        }

        try {
            DB::beginTransaction();

            $item = $borrow->item;

            if ($validated['action'] === 'lost') {
                // Update status peminjaman menjadi hilang
                $borrow->update([
                    'status' => 'lost',
                    'return_date' => Carbon::now()
                ]);
                
                // Update status item menjadi hilang
                $item->updateStatus(Item::STATUS_LOST);

                // Log aktivitas kehilangan
                \App\Models\ActivityLog::log('lost', 'peminjaman', 'Barang dinyatakan hilang: ' . ($borrow->item->name ?? '-') . ' (ID: ' . $borrow->id . ')');

            } else {
                // Kembalikan barang
                $borrow->update([
                    'status' => 'returned',
                    'return_date' => Carbon::now(),
                    'condition_on_return' => $validated['condition_on_return']
                ]);
                
                // Perbarui kondisi dan status item berdasarkan kondisi saat dikembalikan
                $item->updateCondition($validated['condition_on_return']);

                // Log aktivitas pengembalian
                \App\Models\ActivityLog::log('return', 'peminjaman', 'Mengembalikan barang: ' . ($borrow->item->name ?? '-') . ' (ID: ' . $borrow->id . ')');
            }

            DB::commit();

            return redirect()
                ->route('borrows.show', $borrow)
                ->with('success', 'Status peminjaman berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memperbarui status: ' . $e->getMessage());
        }
    }

    public function destroy(Borrow $borrow)
    {
        // Hanya admin yang boleh menghapus data peminjaman
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('borrows.index')
                ->with('error', 'Anda tidak memiliki izin untuk menghapus data peminjaman.');
        }
        
        if ($borrow->status === 'borrowed') {
            return back()->with('error', 'Tidak dapat menghapus data peminjaman yang masih aktif');
        }

        $borrow->delete();
        \App\Models\ActivityLog::log('delete', 'peminjaman', 'Menghapus data peminjaman: ID ' . $borrow->id . ' (Barang: ' . ($borrow->item->name ?? '-') . ')');

        return redirect()
            ->route('borrows.index')
            ->with('success', 'Data peminjaman berhasil dihapus');
    }

    private function createApprovalNotification(Borrow $borrow, $type = 'pending')
    {
        $adminUsers = User::whereIn('role', ['admin', 'petugas'])->get();
        
        foreach ($adminUsers as $admin) {
            if ($type === 'pending') {
                Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'Pengajuan Peminjaman Baru',
                    'message' => "Pengguna {$borrow->user->name} mengajukan peminjaman barang {$borrow->item->name}",
                    'type' => 'borrow_request',
                    'data' => json_encode(['borrow_id' => $borrow->id]),
                ]);
            } else {
                Notification::create([
                    'user_id' => $borrow->user_id,
                    'title' => $type === 'approved' ? 'Peminjaman Disetujui' : 'Peminjaman Ditolak',
                    'message' => $type === 'approved' 
                        ? "Pengajuan peminjaman barang {$borrow->item->name} telah disetujui"
                        : "Pengajuan peminjaman barang {$borrow->item->name} ditolak: {$borrow->rejection_reason}",
                    'type' => 'borrow_approval',
                    'data' => json_encode(['borrow_id' => $borrow->id, 'status' => $type]),
                ]);
            }
        }
    }
} 