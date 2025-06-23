<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Mark notification as read if coming from a link with notification_id
        if ($request->has('notification_id')) {
            $notification = Auth::user()->notifications()->where('id', $request->notification_id)->first();
            if ($notification) {
                $notification->markAsRead();
            }
        }

        $notifications = Auth::user()->notifications()->orderBy('created_at', 'desc')->paginate(20);
        
        // Log activity
        \App\Models\ActivityLog::log('view', 'notification', 'Lihat daftar notifikasi (' . $notifications->total() . ' notifikasi)');
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        // Mark notification as read when viewed
        if (!$notification->read_at) {
            $notification->markAsRead();
            
            // Log activity
            \App\Models\ActivityLog::log('view', 'notification', 'Lihat detail notifikasi (ID: ' . $notification->id . ')');
        }
        
        return view('notifications.show', compact('notification'));
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->markAsRead();
        
        // Log activity
        \App\Models\ActivityLog::log('mark_read', 'notification', 'Tandai notifikasi sebagai dibaca (ID: ' . $notification->id . ')');
        
        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);
        
        // Log activity
        \App\Models\ActivityLog::log('mark_all_read', 'notification', 'Tandai semua notifikasi sebagai dibaca');
        
        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->delete();
        
        // Log activity
        \App\Models\ActivityLog::log('delete', 'notification', 'Hapus notifikasi (ID: ' . $notification->id . ')');
        
        return back()->with('success', 'Notifikasi telah dihapus.');
    }

    /**
     * Delete all notifications for the authenticated user.
     */
    public function clearAll()
    {
        Auth::user()->notifications()->delete();

        // Log activity
        \App\Models\ActivityLog::log('delete', 'notification', 'Hapus semua notifikasi');

        return redirect()->route('notifications.index')->with('success', 'Semua notifikasi telah dihapus.');
    }
}
