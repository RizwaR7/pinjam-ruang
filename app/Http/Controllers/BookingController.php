<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Equipment;
use App\Models\FinePayment;
use App\Models\Room;
use App\Models\Setting;
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

        $stats = Booking::getStatsByUser(auth()->id());

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
            'room_id' => 'nullable|exists:rooms,id',
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

        if (empty($validated['room_id']) && empty($validated['equipment'])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['equipment' => 'Jika tidak meminjam ruangan, Anda wajib memilih minimal satu alat/fasilitas.'])
                ->with('error', 'Peminjaman tidak valid.');
        }

        // Check for time conflicts only if a room is selected
        if (!empty($validated['room_id'])) {
            $conflict = Booking::where('room_id', $validated['room_id'])
                ->where('booking_date', $validated['booking_date'])
                ->whereIn('status', ['pending', 'approved'])
                ->where(function ($timeOverlapQuery) use ($validated) {
                    $timeOverlapQuery->where('start_time', '<', $validated['end_time'])
                        ->where('end_time', '>', $validated['start_time']);
                })
                ->exists();

            if ($conflict) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Ruangan sudah di-booking pada tanggal dan jam tersebut. Silakan pilih waktu lain.');
            }
        }

        // Handle file upload
        $permitPath = null;
        if ($request->hasFile('permit_file')) {
            $permitPath = $request->file('permit_file')->store('permits', 'public');
        }

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'room_id' => $validated['room_id'] ?: null,
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
            foreach ($request->equipment as $equipmentItem) {
                if (!empty($equipmentItem['id']) && !empty($equipmentItem['quantity'])) {
                    $booking->equipment()->attach($equipmentItem['id'], ['quantity' => $equipmentItem['quantity']]);
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

        $booking->load(['room', 'approver', 'equipment', 'finePayments']);

        // Get VA info for fine payment
        $vaInfo = null;
        if ($booking->fine_status === 'unpaid') {
            $vaInfo = [
                'bank' => Setting::get('va_bank_name', ''),
                'number' => Setting::get('va_account_number', ''),
                'holder' => Setting::get('va_account_holder', ''),
            ];
        }

        return view('bookings.show', compact('booking', 'vaInfo'));
    }

    /**
     * Borrower requests return of items/room.
     */
    public function requestReturn(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->status !== 'approved') {
            return back()->with('error', 'Hanya peminjaman yang sudah disetujui yang dapat diajukan pengembalian.');
        }

        $booking->update([
            'status' => 'return_requested',
            'return_requested_at' => now(),
        ]);

        // Notify admins
        $admins = \App\Models\User::whereHas('role', function ($q) {
            $q->whereIn('slug', ['pengelola_sistem', 'pengelola_gedung']);
        })->get();

        foreach ($admins as $admin) {
            \App\Models\AppNotification::notify(
                $admin->id,
                'Permintaan Pengembalian 📦',
                'Peminjam ' . auth()->user()->name . ' mengajukan pengembalian.',
                'info',
                'package',
                ['booking_id' => $booking->id]
            );
        }

        return back()->with('success', 'Permintaan pengembalian berhasil diajukan. Menunggu konfirmasi admin.');
    }

    /**
     * Borrower uploads fine payment proof.
     */
    public function uploadFinePayment(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->fine_status !== 'unpaid') {
            return back()->with('error', 'Tidak ada denda yang perlu dibayar.');
        }

        $request->validate([
            'proof_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $path = $request->file('proof_file')->store('fine-payments', 'public');

        FinePayment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->fine_amount,
            'proof_file' => $path,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');
    }
}
