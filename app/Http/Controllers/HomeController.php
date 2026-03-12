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
        $roomStats = Room::selectRaw('
            COUNT(*) as total_rooms,
            SUM(status = "tersedia") as rooms_available,
            SUM(status = "maintenance") as rooms_maintenance
        ')->first();

        $bookingStats = Booking::selectRaw('
            COUNT(*) as total_bookings,
            SUM(status = "pending") as pending_bookings,
            SUM(status = "approved" AND booking_date = ?) as approved_today
        ', [now()->toDateString()])->first();

        $stats = [
            'total_rooms'      => (int) ($roomStats->total_rooms ?? 0),
            'rooms_available'  => (int) ($roomStats->rooms_available ?? 0),
            'rooms_maintenance' => (int) ($roomStats->rooms_maintenance ?? 0),
            'pending_bookings' => (int) ($bookingStats->pending_bookings ?? 0),
            'approved_today'   => (int) ($bookingStats->approved_today ?? 0),
            'total_bookings'   => (int) ($bookingStats->total_bookings ?? 0),
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

        $rawStats = Booking::where('user_id', $user->id)
            ->selectRaw('
                COUNT(*) as total,
                SUM(status = "pending") as pending,
                SUM(status = "approved") as approved,
                SUM(status = "rejected") as rejected
            ')
            ->first();

        $stats = [
            'total'    => (int) ($rawStats->total ?? 0),
            'pending'  => (int) ($rawStats->pending ?? 0),
            'approved' => (int) ($rawStats->approved ?? 0),
            'rejected' => (int) ($rawStats->rejected ?? 0),
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
