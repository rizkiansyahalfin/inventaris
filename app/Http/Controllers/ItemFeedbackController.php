<?php

namespace App\Http\Controllers;

use App\Models\Borrow;
use App\Models\ItemFeedback;
use App\Models\ActivityLog;
use App\Http\Requests\StoreItemFeedbackRequest;
use App\Http\Requests\UpdateItemFeedbackRequest;
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
        $query = ItemFeedback::with(['user', 'item']);
        $borrowsTanpaFeedback = null;

        // Jika user biasa, hanya tampilkan feedback miliknya
        if ($user->isUser()) {
            $query->where('user_id', $user->id);
            // Ambil daftar peminjaman yang sudah selesai, belum diberi feedback
            $borrowsTanpaFeedback = Borrow::where('user_id', $user->id)
                ->where('status', 'returned')
                ->whereDoesntHave('feedback')
                ->with('item')
                ->orderBy('borrow_date', 'desc')
                ->get();
        }

        $feedbacks = $query->orderBy('created_at', 'desc')->paginate(10);

        // Log activity
        $filterDescription = $user->isUser() ? 'Lihat daftar feedback sendiri' : 'Lihat daftar feedback semua user';
        ActivityLog::log('view', 'feedback', $filterDescription . ' (' . $feedbacks->total() . ' feedback)');

        return view('feedbacks.index', compact('feedbacks', 'borrowsTanpaFeedback'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Borrow $borrow)
    {
        // Pastikan hanya peminjam yang dapat memberikan feedback
        if ($borrow->user_id !== Auth::id()) {
            abort(403);
        }

        // Log activity
        ActivityLog::log('view', 'feedback', 'Akses halaman buat feedback untuk peminjaman ID: ' . $borrow->id);

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
    public function store(StoreItemFeedbackRequest $request, Borrow $borrow)
    {
        // Authorization handled by StoreItemFeedbackRequest

        // Simpan feedback
        $feedback = ItemFeedback::create([
            'borrow_id' => $borrow->id,
            'user_id' => Auth::id(),
            'item_id' => $borrow->item_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        ActivityLog::log('create', 'item_feedback', 'Memberikan feedback untuk peminjaman ID: ' . $borrow->id . ' (Feedback ID: ' . $feedback->id . ')');

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

        // Log activity
        ActivityLog::log('view', 'feedback', 'Lihat detail feedback ID: ' . $feedback->id);

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

        // Log activity
        ActivityLog::log('view', 'feedback', 'Akses halaman edit feedback ID: ' . $feedback->id);

        return view('feedbacks.edit', compact('feedback'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateItemFeedbackRequest $request, ItemFeedback $feedback)
    {
        // Authorization handled by UpdateItemFeedbackRequest

        // Update feedback
        $feedback->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        ActivityLog::log('update', 'item_feedback', 'Memperbarui feedback ID: ' . $feedback->id);

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
        ActivityLog::log('delete', 'item_feedback', 'Menghapus feedback ID: ' . $feedbackId);

        return redirect()->route('feedbacks.index')
            ->with('success', 'Feedback berhasil dihapus.');
    }
}
