<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'room', 'equipment']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', '%' . $request->search . '%'))
                    ->orWhereHas('room', fn($r) => $r->where('name', 'like', '%' . $request->search . '%'))
                    ->orWhere('purpose', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('building')) {
            $query->whereHas('room', fn($q) => $q->where('building', $request->building));
        }

        $bookings = $query->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'approved' THEN 1 WHEN 'rejected' THEN 2 WHEN 'finished' THEN 3 ELSE 4 END")
            ->orderByDesc('created_at')
            ->paginate(15)
            ->appends(request()->query());

        $stats = [
            'pending' => Booking::where('status', 'pending')->count(),
            'approved' => Booking::where('status', 'approved')->count(),
            'rejected' => Booking::where('status', 'rejected')->count(),
            'finished' => Booking::where('status', 'finished')->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user.role', 'room', 'approver', 'equipment']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function approve(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return redirect()->route('admin.bookings.show', $booking)
                ->with('error', 'Hanya peminjaman dengan status "Menunggu" yang dapat disetujui.');
        }

        // Check for conflicts
        $conflict = Booking::where('room_id', $booking->room_id)
            ->where('booking_date', $booking->booking_date)
            ->where('id', '!=', $booking->id)
            ->where('status', 'approved')
            ->where(function ($q) use ($booking) {
                $q->where(function ($q2) use ($booking) {
                    $q2->where('start_time', '<', $booking->end_time)
                        ->where('end_time', '>', $booking->start_time);
                });
            })
            ->exists();

        if ($conflict) {
            return redirect()->route('admin.bookings.show', $booking)
                ->with('error', 'Tidak bisa menyetujui: ruangan sudah di-booking pada waktu yang sama.');
        }

        $booking->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Send notification to user
        AppNotification::notify(
            $booking->user_id,
            'Peminjaman Disetujui ✅',
            'Peminjaman ruangan "' . $booking->room->name . '" pada ' . $booking->booking_date->format('d M Y') . ' (' . substr($booking->start_time, 0, 5) . ' - ' . substr($booking->end_time, 0, 5) . ') telah disetujui.',
            'success',
            'check-circle',
            ['booking_id' => $booking->id]
        );

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Peminjaman berhasil disetujui.');
    }

    public function reject(Request $request, Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return redirect()->route('admin.bookings.show', $booking)
                ->with('error', 'Hanya peminjaman dengan status "Menunggu" yang dapat ditolak.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $booking->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Send notification to user
        AppNotification::notify(
            $booking->user_id,
            'Peminjaman Ditolak ❌',
            'Peminjaman ruangan "' . $booking->room->name . '" pada ' . $booking->booking_date->format('d M Y') . ' ditolak. Alasan: ' . $request->rejection_reason,
            'danger',
            'x-circle',
            ['booking_id' => $booking->id]
        );

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Peminjaman berhasil ditolak.');
    }
}
