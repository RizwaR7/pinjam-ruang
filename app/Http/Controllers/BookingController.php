<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Equipment;
use App\Models\Room;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Booking::where('user_id', auth()->id())
            ->with(['room', 'equipment']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderByDesc('created_at')->paginate(10);

        $stats = [
            'total' => Booking::where('user_id', auth()->id())->count(),
            'pending' => Booking::where('user_id', auth()->id())->where('status', 'pending')->count(),
            'approved' => Booking::where('user_id', auth()->id())->where('status', 'approved')->count(),
            'rejected' => Booking::where('user_id', auth()->id())->where('status', 'rejected')->count(),
        ];

        return view('bookings.index', compact('bookings', 'stats'));
    }

    public function create()
    {
        $rooms = Room::where('is_active', true)
            ->where('status', 'tersedia')
            ->orderBy('building')
            ->orderBy('name')
            ->get();

        $equipment = Equipment::where('is_available', true)
            ->where('condition', '!=', 'rusak_berat')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('bookings.create', compact('rooms', 'equipment'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'purpose' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'participant_count' => 'nullable|integer|min:1',
            'contact_phone' => 'nullable|string|max:20',
            'permit_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'equipment' => 'nullable|array',
            'equipment.*.id' => 'exists:equipment,id',
            'equipment.*.quantity' => 'integer|min:1',
        ]);

        // Check for time conflicts
        $conflict = Booking::where('room_id', $validated['room_id'])
            ->where('booking_date', $validated['booking_date'])
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($validated) {
                $q->where(function ($q2) use ($validated) {
                    $q2->where('start_time', '<', $validated['end_time'])
                        ->where('end_time', '>', $validated['start_time']);
                });
            })
            ->exists();

        if ($conflict) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ruangan sudah di-booking pada tanggal dan jam tersebut. Silakan pilih waktu lain.');
        }

        // Handle file upload
        $permitPath = null;
        if ($request->hasFile('permit_file')) {
            $permitPath = $request->file('permit_file')->store('permits', 'public');
        }

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'room_id' => $validated['room_id'],
            'booking_date' => $validated['booking_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'purpose' => $validated['purpose'],
            'notes' => $validated['notes'] ?? null,
            'participant_count' => $validated['participant_count'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'permit_file' => $permitPath,
            'status' => 'pending',
        ]);

        // Attach equipment
        if ($request->filled('equipment')) {
            foreach ($request->equipment as $item) {
                if (!empty($item['id']) && !empty($item['quantity'])) {
                    $booking->equipment()->attach($item['id'], ['quantity' => $item['quantity']]);
                }
            }
        }

        return redirect()->route('bookings.index')
            ->with('success', 'Peminjaman berhasil diajukan! Menunggu persetujuan pengelola.');
    }

    public function show(Booking $booking)
    {
        // Ensure users can only see their own bookings
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $booking->load(['room', 'approver', 'equipment']);

        return view('bookings.show', compact('booking'));
    }
}
