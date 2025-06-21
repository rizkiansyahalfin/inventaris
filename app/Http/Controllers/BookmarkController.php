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
        $bookmarks = Auth::user()->bookmarks()->with('item')->paginate(10);
        
        // Log activity
        \App\Models\ActivityLog::log('view', 'bookmark', 'Lihat daftar bookmark (' . $bookmarks->total() . ' bookmark)');
        
        return view('bookmarks.index', compact('bookmarks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'notes' => 'nullable|string',
        ]);

        $bookmark = Auth::user()->bookmarks()->create([
            'item_id' => $request->item_id,
            'notes' => $request->notes,
        ]);
        
        // Log activity
        \App\Models\ActivityLog::log('create', 'bookmark', 'Menambah bookmark untuk item ID: ' . $request->item_id);
        
        return back()->with('success', 'Item berhasil ditambahkan ke bookmark.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bookmark $bookmark)
    {
        if ($bookmark->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $bookmark->update([
            'notes' => $request->notes,
        ]);
        
        // Log activity
        \App\Models\ActivityLog::log('update', 'bookmark', 'Mengedit bookmark ID: ' . $bookmark->id);
        
        return back()->with('success', 'Bookmark berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bookmark $bookmark)
    {
        if ($bookmark->user_id !== Auth::id()) {
            abort(403);
        }

        $bookmark->delete();
        
        // Log activity
        \App\Models\ActivityLog::log('delete', 'bookmark', 'Menghapus bookmark ID: ' . $bookmark->id);
        
        return back()->with('success', 'Bookmark berhasil dihapus.');
    }

    /**
     * Toggle bookmark status
     */
    public function toggle(Request $request, Item $item)
    {
        $bookmark = Auth::user()->bookmarks()->where('item_id', $item->id)->first();

        if ($bookmark) {
            $bookmark->delete();
            $message = 'Item dihapus dari bookmark.';
            $action = 'delete';
        } else {
            Auth::user()->bookmarks()->create([
                'item_id' => $item->id,
            ]);
            $message = 'Item ditambahkan ke bookmark.';
            $action = 'create';
        }

        // Log activity
        \App\Models\ActivityLog::log($action, 'bookmark', ($action === 'create' ? 'Menambah' : 'Menghapus') . ' bookmark untuk item: ' . $item->name . ' (ID: ' . $item->id . ')');

        return back()->with('success', $message);
    }
}
