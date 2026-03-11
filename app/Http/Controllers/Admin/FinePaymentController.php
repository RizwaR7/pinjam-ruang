<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\FinePayment;
use Illuminate\Http\Request;

class FinePaymentController extends Controller
{
    public function index()
    {
        $payments = FinePayment::with(['booking.user', 'booking.room', 'verifier'])
            ->orderByRaw("CASE status WHEN 'pending' THEN 0 WHEN 'verified' THEN 1 WHEN 'rejected' THEN 2 END")
            ->orderByDesc('created_at')
            ->paginate(15);

        $stats = [
            'pending' => FinePayment::where('status', 'pending')->count(),
            'verified' => FinePayment::where('status', 'verified')->count(),
            'rejected' => FinePayment::where('status', 'rejected')->count(),
        ];

        return view('admin.fine-payments.index', compact('payments', 'stats'));
    }

    public function verify(FinePayment $finePayment)
    {
        if ($finePayment->status !== 'pending') {
            return back()->with('error', 'Pembayaran ini sudah diproses.');
        }

        $finePayment->update([
            'status' => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        // Update booking fine_status
        $finePayment->booking->update(['fine_status' => 'paid']);

        // Notify borrower
        AppNotification::notify(
            $finePayment->booking->user_id,
            'Pembayaran Denda Terverifikasi ✅',
            'Pembayaran denda sebesar Rp ' . number_format($finePayment->amount, 0, ',', '.') . ' telah diverifikasi.',
            'success',
            'check-circle',
            ['booking_id' => $finePayment->booking_id]
        );

        return back()->with('success', 'Pembayaran denda berhasil diverifikasi.');
    }

    public function reject(Request $request, FinePayment $finePayment)
    {
        if ($finePayment->status !== 'pending') {
            return back()->with('error', 'Pembayaran ini sudah diproses.');
        }

        $request->validate(['notes' => 'required|string|max:500']);

        $finePayment->update([
            'status' => 'rejected',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'notes' => $request->notes,
        ]);

        // Notify borrower
        AppNotification::notify(
            $finePayment->booking->user_id,
            'Pembayaran Denda Ditolak ❌',
            'Pembayaran denda Anda ditolak. Alasan: ' . $request->notes,
            'danger',
            'x-circle',
            ['booking_id' => $finePayment->booking_id]
        );

        return back()->with('success', 'Pembayaran denda ditolak.');
    }
}
