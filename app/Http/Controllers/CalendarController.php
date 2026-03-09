<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $rooms = Room::where('is_active', true)->orderBy('building')->orderBy('name')->get();
        $buildings = Room::where('is_active', true)->whereNotNull('building')->distinct()->pluck('building')->sort()->values();

        $selectedRoom = $request->filled('room_id') ? Room::find($request->room_id) : null;

        return view('calendar.index', compact('rooms', 'buildings', 'selectedRoom'));
    }

    /**
     * JSON API: return booking events for calendar
     */
    public function events(Request $request)
    {
        $query = Booking::with(['room', 'user'])
            ->whereIn('status', ['approved', 'pending']);

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->filled('building')) {
            $query->whereHas('room', fn($q) => $q->where('building', $request->building));
        }

        if ($request->filled('start') && $request->filled('end')) {
            $query->whereBetween('booking_date', [$request->start, $request->end]);
        }

        $bookings = $query->orderBy('booking_date')->orderBy('start_time')->get();

        $events = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'title' => $booking->purpose,
                'room' => $booking->room->name,
                'room_code' => $booking->room->code,
                'building' => $booking->room->building,
                'user' => $booking->user->name,
                'date' => $booking->booking_date->format('Y-m-d'),
                'start_time' => substr($booking->start_time, 0, 5),
                'end_time' => substr($booking->end_time, 0, 5),
                'status' => $booking->status,
                'status_label' => $booking->status_label,
                'status_badge' => $booking->status_badge,
            ];
        });

        return response()->json($events);
    }

    /**
     * JSON API: check room availability for a specific date
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date',
        ]);

        $bookings = Booking::where('room_id', $request->room_id)
            ->where('booking_date', $request->date)
            ->whereIn('status', ['pending', 'approved'])
            ->orderBy('start_time')
            ->get(['id', 'start_time', 'end_time', 'purpose', 'status']);

        $room = Room::find($request->room_id);

        return response()->json([
            'room' => [
                'name' => $room->name,
                'code' => $room->code,
                'status' => $room->status,
                'capacity' => $room->capacity,
            ],
            'date' => $request->date,
            'bookings' => $bookings->map(fn($b) => [
                'start_time' => substr($b->start_time, 0, 5),
                'end_time' => substr($b->end_time, 0, 5),
                'purpose' => $b->purpose,
                'status' => $b->status,
            ]),
        ]);
    }
}
