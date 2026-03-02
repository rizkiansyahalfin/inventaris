<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use App\Models\Item;
use App\Models\ActivityLog;
use App\Services\BorrowService;
use App\Http\Requests\StoreBorrowRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowController extends Controller
{
    public function __construct(
        private BorrowService $borrowService
    ) {
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Borrow::with(['user', 'item', 'approvedBy']);

        if ($user->isUser()) {
            $query->where('user_id', $user->id);
        }

        $query->when($request->status, fn($query, $status) => $query->where('status', $status))
            ->when($request->approval_status, fn($query, $approval_status) => $query->where('approval_status', $approval_status))
            ->when($request->search, fn($query, $search) => $query->whereHas('item', fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%")));

        $borrows = $query->orderBy('created_at', 'desc')->paginate(10);

        $filters = [];
        if ($request->status)
            $filters[] = 'status: ' . $request->status;
        if ($request->approval_status)
            $filters[] = 'approval: ' . $request->approval_status;
        if ($request->search)
            $filters[] = 'pencarian: ' . $request->search;

        $filterDescription = !empty($filters) ? 'Lihat daftar peminjaman dengan filter: ' . implode(', ', $filters) : 'Lihat daftar peminjaman';
        ActivityLog::log('view', 'borrow', $filterDescription . ' (' . $borrows->total() . ' peminjaman)');

        return view('borrows.index', compact('borrows'));
    }

    public function create()
    {
        $items = Item::where('status', Item::STATUS_AVAILABLE)
            ->where('condition', '!=', 'Rusak Berat')
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        ActivityLog::log('view', 'borrow', 'Akses halaman buat peminjaman baru');

        return view('borrows.create', compact('items'));
    }

    public function store(StoreBorrowRequest $request)
    {
        try {
            $borrow = $this->borrowService->createBorrow($request->validated());

            return redirect()
                ->route('borrows.show', $borrow)
                ->with('success', 'Pengajuan peminjaman berhasil dibuat dan menunggu persetujuan');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Borrow $borrow)
    {
        $user = Auth::user();

        if ($user->isUser() && $borrow->user_id != $user->id) {
            return redirect()->route('borrows.index')
                ->with('error', 'Anda tidak memiliki akses untuk melihat peminjaman ini.');
        }

        $borrow->load(['user', 'item', 'attachments', 'approvedBy']);

        ActivityLog::log('view', 'borrow', 'Lihat detail peminjaman ID: ' . $borrow->id . ' - ' . ($borrow->item->name ?? 'Unknown'));

        return view('borrows.show', compact('borrow'));
    }

    public function approve(Request $request, Borrow $borrow)
    {
        if (Auth::user()->isUser()) {
            return redirect()->route('borrows.show', $borrow)
                ->with('error', 'Anda tidak memiliki izin untuk menyetujui peminjaman.');
        }

        if (!$borrow->canBeApproved()) {
            return back()->with('error', 'Peminjaman ini tidak dapat disetujui.');
        }

        try {
            $this->borrowService->approveBorrow($borrow);

            return redirect()
                ->route('borrows.show', $borrow)
                ->with('success', 'Peminjaman berhasil disetujui');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyetujui peminjaman: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Borrow $borrow)
    {
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
            $this->borrowService->rejectBorrow($borrow, $validated['rejection_reason']);

            return redirect()
                ->route('borrows.show', $borrow)
                ->with('success', 'Peminjaman berhasil ditolak');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menolak peminjaman: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Borrow $borrow)
    {
        if (Auth::user()->isUser()) {
            return redirect()->route('borrows.show', $borrow)
                ->with('error', 'Anda tidak memiliki izin untuk mengubah status peminjaman.');
        }

        $validated = $request->validate([
            'action' => 'required|string|in:returned,lost',
            'condition_on_return' => 'required_if:action,returned|string|in:Baik,Rusak Ringan,Rusak Berat',
        ]);

        if (in_array($borrow->status, ['returned', 'lost'])) {
            return back()->with('error', 'Status peminjaman ini sudah final.');
        }

        if ($borrow->approval_status !== 'approved') {
            return back()->with('error', 'Hanya peminjaman yang sudah disetujui yang dapat dikembalikan.');
        }

        try {
            $this->borrowService->updateBorrowStatus(
                $borrow,
                $validated['action'],
                $validated['condition_on_return'] ?? null
            );

            return redirect()
                ->route('borrows.show', $borrow)
                ->with('success', 'Status peminjaman berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memperbarui status: ' . $e->getMessage());
        }
    }

    public function destroy(Borrow $borrow)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('borrows.index')
                ->with('error', 'Anda tidak memiliki izin untuk menghapus data peminjaman.');
        }

        if ($borrow->status === 'borrowed') {
            return back()->with('error', 'Tidak dapat menghapus data peminjaman yang masih aktif');
        }

        $borrow->delete();
        ActivityLog::log('delete', 'peminjaman', 'Menghapus data peminjaman: ID ' . $borrow->id . ' (Barang: ' . ($borrow->item->name ?? '-') . ')');

        return redirect()
            ->route('borrows.index')
            ->with('success', 'Data peminjaman berhasil dihapus');
    }
}