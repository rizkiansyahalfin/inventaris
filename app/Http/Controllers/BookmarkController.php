<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookmarks = Auth::user()->bookmarks()->with('item')->latest()->paginate(12);
        return view('bookmarks.index', compact('bookmarks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'notes' => 'nullable|string|max:255',
        ]);

        // Cek apakah item sudah di-bookmark
        $exists = Bookmark::where('user_id', Auth::id())
            ->where('item_id', $request->item_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Item sudah ada dalam bookmark.');
        }

        Bookmark::create([
            'user_id' => Auth::id(),
            'item_id' => $request->item_id,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Item berhasil ditambahkan ke bookmark.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bookmark $bookmark)
    {
        // Pastikan hanya pemilik yang bisa update
        if ($bookmark->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'notes' => 'nullable|string|max:255',
        ]);

        $bookmark->update([
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Catatan bookmark berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bookmark $bookmark)
    {
        // Pastikan hanya pemilik yang bisa menghapus
        if ($bookmark->user_id !== Auth::id()) {
            abort(403);
        }

        $bookmark->delete();

        return back()->with('success', 'Item berhasil dihapus dari bookmark.');
    }

    /**
     * Toggle bookmark status
     */
    public function toggle(Item $item)
    {
        $bookmark = Bookmark::where('user_id', Auth::id())
            ->where('item_id', $item->id)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            $message = 'Item berhasil dihapus dari bookmark.';
        } else {
            Bookmark::create([
                'user_id' => Auth::id(),
                'item_id' => $item->id,
            ]);
            $message = 'Item berhasil ditambahkan ke bookmark.';
        }

        if (request()->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('success', $message);
    }
}
