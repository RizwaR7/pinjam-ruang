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

        $rawStats = Booking::selectRaw('
            SUM(status = "pending") as pending,
            SUM(status = "approved") as approved,
            SUM(status = "return_requested") as return_requested,
            SUM(status = "rejected") as rejected,
            SUM(status = "finished") as finished
        ')->first();

        $stats = [
            'pending'          => (int) ($rawStats->pending ?? 0),
            'approved'         => (int) ($rawStats->approved ?? 0),
            'return_requested' => (int) ($rawStats->return_requested ?? 0),
            'rejected'         => (int) ($rawStats->rejected ?? 0),
            'finished'         => (int) ($rawStats->finished ?? 0),
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['user.role', 'room', 'approver', 'equipment', 'finePayments']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function approve(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return redirect()->route('admin.bookings.show', $booking)
                ->with('error', 'Hanya peminjaman dengan status "Menunggu" yang dapat disetujui.');
        }

        // Check for conflicts only if a room is assigned
        if (!$booking->isEquipmentOnly()) {
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
        }

        // Set return deadline to booking_date + end_time
        $returnDeadline = \Carbon\Carbon::parse($booking->booking_date)
            ->setTimeFromTimeString($booking->end_time);

        $booking->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'return_deadline' => $returnDeadline,
        ]);

        $roomName = $booking->room ? $booking->room->name : 'Fasilitas/Alat';

        // Send notification to user
        AppNotification::notify(
            $booking->user_id,
            'Peminjaman Disetujui ✅',
            'Peminjaman ' . $roomName . ' pada ' . \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') . ' (' . substr($booking->start_time, 0, 5) . ' - ' . substr($booking->end_time, 0, 5) . ') telah disetujui.',
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

        $roomName = $booking->room ? $booking->room->name : 'Fasilitas/Alat';

        // Send notification to user
        AppNotification::notify(
            $booking->user_id,
            'Peminjaman Ditolak ❌',
            'Peminjaman ' . $roomName . ' pada ' . \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') . ' ditolak. Alasan: ' . $request->rejection_reason,
            'danger',
            'x-circle',
            ['booking_id' => $booking->id]
        );

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Peminjaman berhasil ditolak.');
    }

    /**
     * Admin confirms that items/room have been returned.
     */
    public function confirmReturn(Booking $booking)
    {
        if ($booking->status !== 'return_requested') {
            return redirect()->route('admin.bookings.show', $booking)
                ->with('error', 'Peminjaman ini belum mengajukan pengembalian.');
        }

        $booking->update([
            'returned_at' => now(),
            'status' => 'finished',
        ]);

        // Calculate fine if overdue
        $fine = $booking->calculateFine();
        if ($fine > 0) {
            $booking->update([
                'fine_amount' => $fine,
                'fine_status' => 'unpaid',
            ]);

            AppNotification::notify(
                $booking->user_id,
                'Pengembalian Dikonfirmasi — Ada Denda ⚠️',
                'Pengembalian dikonfirmasi tetapi terlambat ' . $booking->days_late . ' hari. Denda: Rp ' . number_format($fine, 0, ',', '.') . '. Silakan lakukan pembayaran.',
                'warning',
                'alert-triangle',
                ['booking_id' => $booking->id]
            );
        } else {
            $booking->update(['fine_status' => 'none']);

            AppNotification::notify(
                $booking->user_id,
                'Pengembalian Dikonfirmasi ✅',
                'Peminjaman Anda telah dikonfirmasi selesai. Terima kasih!',
                'success',
                'check-circle',
                ['booking_id' => $booking->id]
            );
        }

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Pengembalian berhasil dikonfirmasi.' . ($fine > 0 ? ' Denda: Rp ' . number_format($fine, 0, ',', '.') : ''));
    }
}
