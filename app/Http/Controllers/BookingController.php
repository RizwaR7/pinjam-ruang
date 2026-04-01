<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\Booking;
use App\Models\Equipment;
use App\Models\FinePayment;
use App\Models\Room;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $rawStats = Booking::where('user_id', auth()->id())
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
            'end_date' => 'nullable|date|after_or_equal:booking_date',
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

        // Set end_date to booking_date if not provided (single day booking)
        $endDate = $validated['end_date'] ?? $validated['booking_date'];

        if (empty($validated['room_id']) && empty($validated['equipment'])) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['equipment' => 'Jika tidak meminjam ruangan, Anda wajib memilih minimal satu alat/fasilitas.'])
                ->with('error', 'Peminjaman tidak valid.');
        }

        // Check for time conflicts only if a room is selected
        if (!empty($validated['room_id'])) {
            // For multi-day bookings, check conflicts for each day in the range
            $conflict = Booking::where('room_id', $validated['room_id'])
                ->whereIn('status', ['pending', 'approved'])
                ->where(function ($q) use ($validated, $endDate) {
                    // Check if any existing booking overlaps with our date range
                    $q->where(function ($q2) use ($validated, $endDate) {
                        $q2->where('booking_date', '<=', $endDate)
                           ->where(function ($q3) use ($validated) {
                               $q3->where('end_date', '>=', $validated['booking_date'])
                                  ->orWhereNull('end_date');
                           });
                    })
                    ->orWhere(function ($q2) use ($validated, $endDate) {
                        // Also check bookings without end_date (legacy single-day)
                        $q2->whereBetween('booking_date', [$validated['booking_date'], $endDate]);
                    });
                })
                ->where(function ($q) use ($validated) {
                    // Time overlap check
                    $q->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
                })
                ->exists();

            if ($conflict) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Ruangan sudah di-booking pada rentang tanggal dan jam tersebut. Silakan pilih waktu lain.');
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
            'end_date' => $endDate,
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
            $now = now();
            $pivotRows = [];
            foreach ($request->equipment as $item) {
                if (!empty($item['id']) && !empty($item['quantity'])) {
                    $pivotRows[] = [
                        'booking_id'   => $booking->id,
                        'equipment_id' => $item['id'],
                        'quantity'     => $item['quantity'],
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ];
                }
            }
            if (!empty($pivotRows)) {
                DB::table('booking_equipment')->insert($pivotRows);
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
        $adminIds = User::whereHas('role', function ($q) {
            $q->whereIn('slug', ['pengelola_sistem', 'pengelola_gedung']);
        })->pluck('id');

        AppNotification::notifyMany(
            $adminIds,
            'Permintaan Pengembalian 📦',
            'Peminjam ' . auth()->user()->name . ' mengajukan pengembalian.',
            'info',
            'package',
            ['booking_id' => $booking->id]
        );

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
