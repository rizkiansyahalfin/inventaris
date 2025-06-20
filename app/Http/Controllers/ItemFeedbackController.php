<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use App\Models\ItemFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemFeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin() || $user->isPetugas()) {
            $feedbacks = ItemFeedback::with(['item', 'user', 'borrow'])
                ->latest()
                ->paginate(15);
            return view('feedbacks.index', compact('feedbacks'));
        } else {
            $feedbacks = $user->feedback()
                ->with(['item', 'borrow'])
                ->latest()
                ->paginate(15);
            // Ambil daftar peminjaman yang sudah dikembalikan dan belum diberi feedback
            $borrowsTanpaFeedback = $user->borrows()
                ->where('status', 'returned')
                ->whereDoesntHave('feedback')
                ->with('item')
                ->orderBy('return_date', 'desc')
                ->get();
            return view('feedbacks.index', compact('feedbacks', 'borrowsTanpaFeedback'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Borrow $borrow)
    {
        // Pastikan user hanya bisa memberikan feedback untuk peminjaman miliknya
        if ($borrow->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Pastikan peminjaman sudah dikembalikan dan belum ada feedback
        if (!$borrow->canSubmitFeedback()) {
            return redirect()->route('borrows.show', $borrow)
                ->with('error', 'Anda tidak dapat memberikan feedback untuk peminjaman ini.');
        }
        
        return view('feedbacks.create', compact('borrow'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Borrow $borrow)
    {
        // Validasi
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);
        
        // Pastikan user hanya bisa memberikan feedback untuk peminjaman miliknya
        if ($borrow->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Pastikan peminjaman sudah dikembalikan dan belum ada feedback
        if (!$borrow->canSubmitFeedback()) {
            return redirect()->route('borrows.show', $borrow)
                ->with('error', 'Anda tidak dapat memberikan feedback untuk peminjaman ini.');
        }
        
        // Simpan feedback
        $feedback = ItemFeedback::create([
            'borrow_id' => $borrow->id,
            'user_id' => Auth::id(),
            'item_id' => $borrow->item_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        \App\Models\ActivityLog::log('create', 'item_feedback', 'Memberikan feedback untuk peminjaman ID: ' . $borrow->id . ' (Feedback ID: ' . $feedback->id . ')');
        
        return redirect()->route('borrows.show', $borrow)
            ->with('success', 'Terima kasih atas feedback Anda.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ItemFeedback $feedback)
    {
        $user = Auth::user();
        
        // Pastikan hanya user pemilik, admin, atau petugas yang dapat melihat feedback
        if (!$user->isAdmin() && !$user->isPetugas() && $feedback->user_id !== $user->id) {
            abort(403);
        }
        
        return view('feedbacks.show', compact('feedback'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ItemFeedback $feedback)
    {
        // Pastikan hanya user pemilik yang dapat edit feedback
        if ($feedback->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('feedbacks.edit', compact('feedback'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ItemFeedback $feedback)
    {
        // Validasi
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);
        
        // Pastikan hanya user pemilik yang dapat update feedback
        if ($feedback->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Update feedback
        $feedback->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        \App\Models\ActivityLog::log('update', 'item_feedback', 'Memperbarui feedback ID: ' . $feedback->id);
        
        return redirect()->route('feedbacks.show', $feedback)
            ->with('success', 'Feedback berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ItemFeedback $feedback)
    {
        // Pastikan hanya user pemilik atau admin yang dapat menghapus feedback
        $user = Auth::user();
        if (!$user->isAdmin() && $feedback->user_id !== $user->id) {
            abort(403);
        }
        
        $feedbackId = $feedback->id;
        $feedback->delete();
        \App\Models\ActivityLog::log('delete', 'item_feedback', 'Menghapus feedback ID: ' . $feedbackId);
        
        return redirect()->route('feedbacks.index')
            ->with('success', 'Feedback berhasil dihapus.');
    }
}
