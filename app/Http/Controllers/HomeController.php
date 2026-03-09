<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Admin Dashboard
     */
    public function adminHome()
    {
        $stats = [
            'total_rooms' => Room::count(),
            'rooms_available' => Room::where('status', 'tersedia')->count(),
            'rooms_maintenance' => Room::where('status', 'maintenance')->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'approved_today' => Booking::where('status', 'approved')
                ->where('booking_date', now()->toDateString())->count(),
            'total_bookings' => Booking::count(),
        ];

        $recentBookings = Booking::with(['user', 'room'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $todayBookings = Booking::with(['user', 'room'])
            ->where('booking_date', now()->toDateString())
            ->where('status', 'approved')
            ->orderBy('start_time')
            ->get();

        return view('admin.home', compact('stats', 'recentBookings', 'todayBookings'));
    }

    /**
     * User Dashboard
     */
    public function userHome()
    {
        $user = auth()->user();

        $stats = [
            'total' => Booking::where('user_id', $user->id)->count(),
            'pending' => Booking::where('user_id', $user->id)->where('status', 'pending')->count(),
            'approved' => Booking::where('user_id', $user->id)->where('status', 'approved')->count(),
            'rejected' => Booking::where('user_id', $user->id)->where('status', 'rejected')->count(),
        ];

        $upcomingBookings = Booking::with('room')
            ->where('user_id', $user->id)
            ->where('booking_date', '>=', now()->toDateString())
            ->whereIn('status', ['approved', 'pending'])
            ->orderBy('booking_date')
            ->orderBy('start_time')
            ->limit(5)
            ->get();

        $recentNotifications = AppNotification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $unreadCount = AppNotification::where('user_id', $user->id)->where('is_read', false)->count();

        return view('user.home', compact('stats', 'upcomingBookings', 'recentNotifications', 'unreadCount'));
    }
}
