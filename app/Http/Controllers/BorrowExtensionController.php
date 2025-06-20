<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use App\Models\BorrowExtension;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowExtensionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin() || $user->isPetugas()) {
            $extensions = BorrowExtension::with(['borrow.user', 'borrow.item'])
                ->latest()
                ->paginate(15);
        } else {
            $extensions = BorrowExtension::whereHas('borrow', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['borrow.item'])
            ->latest()
            ->paginate(15);
        }
        
        return view('extensions.index', compact('extensions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Borrow $borrow)
    {
        // Pastikan user hanya bisa memperpanjang peminjaman miliknya
        if ($borrow->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Pastikan peminjaman masih aktif dan belum ada permintaan perpanjangan
        if (!$borrow->canBeExtended()) {
            return redirect()->route('borrows.show', $borrow)
                ->with('error', 'Peminjaman tidak dapat diperpanjang.');
        }
        
        return view('extensions.create', compact('borrow'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Borrow $borrow)
    {
        // Validasi
        $request->validate([
            'requested_date' => 'required|date|after:' . $borrow->return_date,
            'reason' => 'required|string|min:10',
        ]);
        
        // Pastikan user hanya bisa memperpanjang peminjaman miliknya
        if ($borrow->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Pastikan peminjaman masih aktif dan belum ada permintaan perpanjangan
        if (!$borrow->canBeExtended()) {
            return redirect()->route('borrows.show', $borrow)
                ->with('error', 'Peminjaman tidak dapat diperpanjang.');
        }
        
        // Simpan permintaan perpanjangan
        $extension = BorrowExtension::create([
            'borrow_id' => $borrow->id,
            'requested_date' => $request->requested_date,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);
        
        // Notifikasi petugas
        $officers = User::whereIn('role', ['petugas', 'admin'])->get();
        foreach ($officers as $officer) {
            Notification::create([
                'user_id' => $officer->id,
                'type' => 'extension_request',
                'title' => 'Permintaan Perpanjangan Peminjaman',
                'message' => "User {$borrow->user->name} meminta perpanjangan untuk peminjaman item {$borrow->item->name}",
                'data' => json_encode(['extension_id' => $extension->id, 'borrow_id' => $borrow->id]),
            ]);
        }
        
        \App\Models\ActivityLog::log('create', 'borrow_extension', 'Mengajukan perpanjangan untuk peminjaman ID: ' . $borrow->id . ' (Ext ID: ' . $extension->id . ')');
        
        return redirect()->route('borrows.show', $borrow)
            ->with('success', 'Permintaan perpanjangan berhasil diajukan');
    }

    /**
     * Display the specified resource.
     */
    public function show(BorrowExtension $extension)
    {
        // Pastikan user hanya bisa melihat perpanjangan miliknya atau admin/petugas
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isPetugas() && $extension->borrow->user_id !== $user->id) {
            abort(403);
        }
        
        return view('extensions.show', compact('extension'));
    }

    /**
     * Update the extension status
     */
    public function updateStatus(Request $request, BorrowExtension $extension)
    {
        // Pastikan hanya admin/petugas yang dapat memperbarui status
        if (!Auth::user()->isAdmin() && !Auth::user()->isPetugas()) {
            abort(403);
        }
        
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'review_notes' => 'nullable|string',
        ]);
        
        $extension->update([
            'status' => $request->status,
            'review_notes' => $request->review_notes,
            'reviewed_by' => Auth::id(),
        ]);
        
        // Jika disetujui, perbarui tanggal pengembalian pada peminjaman
        if ($request->status === 'approved') {
            $extension->borrow->update(['return_date' => $extension->requested_date]);
        }
        
        // Kirim notifikasi ke peminjam
        Notification::create([
            'user_id' => $extension->borrow->user_id,
            'type' => 'extension_' . $request->status,
            'title' => 'Permintaan Perpanjangan ' . ($request->status === 'approved' ? 'Disetujui' : 'Ditolak'),
            'message' => 'Permintaan perpanjangan peminjaman ' . $extension->borrow->item->name . ' telah ' .
                ($request->status === 'approved' ? 'disetujui' : 'ditolak') . '.',
            'data' => json_encode(['extension_id' => $extension->id, 'borrow_id' => $extension->borrow_id]),
        ]);
        
        \App\Models\ActivityLog::log($request->status, 'borrow_extension', 'Status perpanjangan ' . $request->status . ' untuk peminjaman ID: ' . $extension->borrow->id . ' (Ext ID: ' . $extension->id . ')');
        
        return redirect()->route('extensions.show', $extension)
            ->with('success', 'Status perpanjangan berhasil diperbarui');
    }
}
