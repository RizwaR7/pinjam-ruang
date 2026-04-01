<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function __construct()
    {
        // Only require auth for non-public routes
        $this->middleware('auth')->except(['publicIndex', 'events', 'publicEvents']);
    }

    public function index(Request $request)
    {
        $rooms = Room::where('is_active', true)->orderBy('building')->orderBy('name')->get();
        $buildings = Room::where('is_active', true)->whereNotNull('building')->distinct()->pluck('building')->sort()->values();

        $selectedRoom = $request->filled('room_id') ? Room::find($request->room_id) : null;

        return view('calendar.index', compact('rooms', 'buildings', 'selectedRoom'));
    }

    /**
     * Public calendar view (no auth required)
     */
    public function publicIndex(Request $request)
    {
        $rooms = Room::where('is_active', true)->orderBy('building')->orderBy('name')->get();
        $buildings = Room::where('is_active', true)->whereNotNull('building')->distinct()->pluck('building')->sort()->values();

        return view('calendar.public', compact('rooms', 'buildings'));
    }

    /**
     * JSON API: return booking events for calendar (public - no user info)
     */
    public function publicEvents(Request $request)
    {
        $query = Booking::with(['room'])
            ->whereIn('status', ['approved', 'in_use']);

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->filled('building')) {
            $query->whereHas('room', fn($q) => $q->where('building', $request->building));
        }

        if ($request->filled('start') && $request->filled('end')) {
            // Include bookings that overlap with the date range
            $query->where(function ($q) use ($request) {
                $q->whereBetween('booking_date', [$request->start, $request->end])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('booking_date', '<=', $request->end)
                         ->where('end_date', '>=', $request->start);
                  });
            });
        }

        $bookings = $query->orderBy('booking_date')->orderBy('start_time')->get();

        // Expand multi-day bookings into individual day events
        $events = collect();
        foreach ($bookings as $booking) {
            $startDate = $booking->booking_date;
            $endDate = $booking->end_date ?? $booking->booking_date;
            
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                // Only include if within the requested range
                if (!$request->filled('start') || !$request->filled('end') ||
                    ($currentDate->format('Y-m-d') >= $request->start && $currentDate->format('Y-m-d') <= $request->end)) {
                    $events->push([
                        'id' => $booking->id . '_' . $currentDate->format('Y-m-d'),
                        'booking_id' => $booking->id,
                        'title' => $booking->purpose,
                        'room' => $booking->room->name ?? 'Alat Saja',
                        'room_code' => $booking->room->code ?? '-',
                        'building' => $booking->room->building ?? '-',
                        'capacity' => $booking->room->capacity ?? 0,
                        'date' => $currentDate->format('Y-m-d'),
                        'start_time' => substr($booking->start_time, 0, 5),
                        'end_time' => substr($booking->end_time, 0, 5),
                        'status' => $booking->status,
                        'is_multiday' => !$startDate->eq($endDate),
                        'day_index' => $startDate->diffInDays($currentDate) + 1,
                        'total_days' => $startDate->diffInDays($endDate) + 1,
                    ]);
                }
                $currentDate->addDay();
            }
        }

        return response()->json($events->values());
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
            // Include bookings that overlap with the date range
            $query->where(function ($q) use ($request) {
                $q->whereBetween('booking_date', [$request->start, $request->end])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('booking_date', '<=', $request->end)
                         ->where('end_date', '>=', $request->start);
                  });
            });
        }

        $bookings = $query->orderBy('booking_date')->orderBy('start_time')->get();

        // Expand multi-day bookings into individual day events
        $events = collect();
        foreach ($bookings as $booking) {
            $startDate = $booking->booking_date;
            $endDate = $booking->end_date ?? $booking->booking_date;
            
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                // Only include if within the requested range
                if (!$request->filled('start') || !$request->filled('end') ||
                    ($currentDate->format('Y-m-d') >= $request->start && $currentDate->format('Y-m-d') <= $request->end)) {
                    $events->push([
                        'id' => $booking->id . '_' . $currentDate->format('Y-m-d'),
                        'booking_id' => $booking->id,
                        'title' => $booking->purpose,
                        'room' => $booking->room->name ?? 'Alat Saja',
                        'room_code' => $booking->room->code ?? '-',
                        'building' => $booking->room->building ?? '-',
                        'user' => $booking->user->name,
                        'date' => $currentDate->format('Y-m-d'),
                        'start_time' => substr($booking->start_time, 0, 5),
                        'end_time' => substr($booking->end_time, 0, 5),
                        'status' => $booking->status,
                        'status_label' => $booking->status_label,
                        'status_badge' => $booking->status_badge,
                        'is_multiday' => !$startDate->eq($endDate),
                        'day_index' => $startDate->diffInDays($currentDate) + 1,
                        'total_days' => $startDate->diffInDays($endDate) + 1,
                    ]);
                }
                $currentDate->addDay();
            }
        }

        return response()->json($events->values());
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
            'bookings' => $bookings->map(fn($booking) => [
                'start_time' => substr($booking->start_time, 0, 5),
                'end_time' => substr($booking->end_time, 0, 5),
                'purpose' => $booking->purpose,
                'status' => $booking->status,
            ]),
        ]);
    }
}
