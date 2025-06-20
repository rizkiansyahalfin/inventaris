<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BorrowApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = Borrow::with(['user', 'item', 'approvedBy']);

        // Filter berdasarkan request
        $query->when($request->approval_status, function ($query, $approval_status) {
                return $query->where('approval_status', $approval_status);
            })
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->search, function ($query, $search) {
                return $query->whereHas('item', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });

        $borrows = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistik untuk dashboard
        $stats = [
            'total_pending' => Borrow::where('approval_status', 'pending')->count(),
            'total_approved' => Borrow::where('approval_status', 'approved')->count(),
            'total_rejected' => Borrow::where('approval_status', 'rejected')->count(),
            'total_borrowed' => Borrow::where('status', 'borrowed')->count(),
            'total_returned' => Borrow::where('status', 'returned')->count(),
        ];

        return view('admin.borrow-approvals.index', compact('borrows', 'stats'));
    }

    public function pending()
    {
        $borrows = Borrow::with(['user', 'item'])
            ->where('approval_status', 'pending')
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('admin.borrow-approvals.pending', compact('borrows'));
    }

    public function approved()
    {
        $borrows = Borrow::with(['user', 'item', 'approvedBy'])
            ->where('approval_status', 'approved')
            ->orderBy('approved_at', 'desc')
            ->paginate(15);

        return view('admin.borrow-approvals.approved', compact('borrows'));
    }

    public function rejected()
    {
        $borrows = Borrow::with(['user', 'item', 'approvedBy'])
            ->where('approval_status', 'rejected')
            ->orderBy('approved_at', 'desc')
            ->paginate(15);

        return view('admin.borrow-approvals.rejected', compact('borrows'));
    }

    public function report(Request $request)
    {
        $query = Borrow::with(['user', 'item', 'approvedBy']);

        // Filter berdasarkan tanggal
        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter berdasarkan status
        if ($request->approval_status) {
            $query->where('approval_status', $request->approval_status);
        }

        $borrows = $query->orderBy('created_at', 'desc')->get();

        // Statistik laporan
        $reportStats = [
            'total_requests' => $borrows->count(),
            'approved_requests' => $borrows->where('approval_status', 'approved')->count(),
            'rejected_requests' => $borrows->where('approval_status', 'rejected')->count(),
            'pending_requests' => $borrows->where('approval_status', 'pending')->count(),
            'returned_items' => $borrows->where('status', 'returned')->count(),
            'active_borrows' => $borrows->where('status', 'borrowed')->count(),
        ];

        return view('admin.borrow-approvals.report', compact('borrows', 'reportStats'));
    }

    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'borrow_ids' => 'required|array',
            'borrow_ids.*' => 'exists:borrows,id'
        ]);

        $successCount = 0;
        $errorCount = 0;

        foreach ($validated['borrow_ids'] as $borrowId) {
            $borrow = null; // inisialisasi
            try {
                DB::beginTransaction();

                $borrow = Borrow::findOrFail($borrowId);
                
                if (!$borrow->canBeApproved()) {
                    $errorCount++;
                    continue;
                }

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

                // Kurangi jumlah stok
                $item->decrement('stock', $borrow->quantity);
                
                // Jika jumlah menjadi 0, update status menjadi 'Dipinjam'
                if ($item->stock === 0) {
                    $item->updateStatus(Item::STATUS_BORROWED);
                }

                DB::commit();
                \App\Models\ActivityLog::log('approve', 'borrow_request', 'Menyetujui peminjaman ID: ' . $borrow->id . ' untuk item: ' . $borrow->item->name);
                $successCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                if ($borrow) {
                    \App\Models\ActivityLog::log('approve_failed', 'borrow_request', 'Gagal menyetujui peminjaman ID: ' . $borrow->id . ' - ' . $e->getMessage());
                }
                $errorCount++;
            }
        }

        $message = "Berhasil menyetujui {$successCount} peminjaman";
        if ($errorCount > 0) {
            $message .= ", {$errorCount} gagal";
        }

        return back()->with('success', $message);
    }

    public function bulkReject(Request $request)
    {
        $validated = $request->validate([
            'borrow_ids' => 'required|array',
            'borrow_ids.*' => 'exists:borrows,id',
            'rejection_reason' => 'required|string|max:500'
        ]);

        $successCount = 0;
        $errorCount = 0;

        foreach ($validated['borrow_ids'] as $borrowId) {
            $borrow = null; // inisialisasi
            try {
                DB::beginTransaction();

                $borrow = Borrow::findOrFail($borrowId);
                
                if (!$borrow->canBeRejected()) {
                    $errorCount++;
                    continue;
                }

                // Update status peminjaman
                $borrow->update([
                    'approval_status' => 'rejected',
                    'status' => 'rejected',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'rejection_reason' => $validated['rejection_reason'],
                ]);

                DB::commit();
                \App\Models\ActivityLog::log('reject', 'borrow_request', 'Menolak peminjaman ID: ' . $borrow->id . ' untuk item: ' . $borrow->item->name);
                $successCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                if ($borrow) {
                    \App\Models\ActivityLog::log('reject_failed', 'borrow_request', 'Gagal menolak peminjaman ID: ' . $borrow->id . ' - ' . $e->getMessage());
                }
                $errorCount++;
            }
        }

        $message = "Berhasil menolak {$successCount} peminjaman";
        if ($errorCount > 0) {
            $message .= ", {$errorCount} gagal";
        }

        return back()->with('success', $message);
    }
}
