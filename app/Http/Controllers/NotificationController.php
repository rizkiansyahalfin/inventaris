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
    public function index()
    {
        $notifications = Auth::user()->notifications()->orderBy('created_at', 'desc')->paginate(20);
        
        // Log activity
        \App\Models\ActivityLog::log('view', 'notification', 'Lihat daftar notifikasi (' . $notifications->total() . ' notifikasi)');
        
        return view('notifications.index', compact('notifications'));
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
}
