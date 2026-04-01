<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $notifications = AppNotification::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(20);

        // Detect if admin route
        $isAdmin = request()->routeIs('admin.*');
        $view = $isAdmin ? 'admin.notifications.index' : 'notifications.index';

        return view($view, compact('notifications'));
    }

    public function markAsRead(AppNotification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        return back()->with('success', 'Notifikasi ditandai telah dibaca.');
    }

    public function markAllAsRead()
    {
        AppNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi ditandai telah dibaca.');
    }

    /**
     * JSON API: get unread count
     */
    public function unreadCount()
    {
        $count = AppNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * JSON API: get recent notifications for dropdown
     */
    public function recent()
    {
        $notifications = AppNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'title' => $notif->title,
                    'message' => $notif->message,
                    'type' => $notif->type,
                    'icon' => $notif->icon ?? 'bell',
                    'time' => $notif->created_at->diffForHumans(),
                ];
            });

        return response()->json(['notifications' => $notifications]);
    }
}
