<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room_id',
        'booking_date',
        'start_time',
        'end_time',
        'purpose',
        'notes',
        'participant_count',
        'contact_phone',
        'permit_file',
        'status',
        'approved_by',
        'rejection_reason',
        'approved_at',
        'return_deadline',
        'returned_at',
        'return_requested_at',
        'fine_amount',
        'fine_status',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'approved_at' => 'datetime',
        'return_deadline' => 'datetime',
        'returned_at' => 'datetime',
        'return_requested_at' => 'datetime',
        'participant_count' => 'integer',
        'fine_amount' => 'decimal:2',
    ];

    // ── Relations ─────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function equipment(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, 'booking_equipment')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function finePayments(): HasMany
    {
        return $this->hasMany(FinePayment::class);
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('booking_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'approved']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'approved')
            ->whereNotNull('return_deadline')
            ->where('return_deadline', '<', now())
            ->whereNull('returned_at');
    }

    // ── Accessors ─────────────────────────────────────────

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'finished' => 'info',
            'cancelled' => 'secondary',
            'return_requested' => 'sky',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'finished' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'return_requested' => 'Menunggu Konfirmasi Pengembalian',
            default => $this->status,
        };
    }

    // ── Helpers ────────────────────────────────────────────

    /**
     * Determine if this booking is for equipment only (no room booked).
     */
    public function isEquipmentOnly(): bool
    {
        return is_null($this->room_id);
    }

    /**
     * Check if this booking is overdue (past return deadline and not yet returned).
     */
    public function isOverdue(): bool
    {
        return $this->return_deadline
            && now()->greaterThan($this->return_deadline)
            && is_null($this->returned_at);
    }

    /**
     * Check if the borrower has requested a return.
     */
    public function isReturnRequested(): bool
    {
        return $this->status === 'return_requested';
    }

    /**
     * Get the display name of the resource being borrowed
     * (room name, or a generic label for equipment-only bookings).
     */
    public function getResourceNameAttribute(): string
    {
        return $this->room ? $this->room->name : 'Fasilitas/Alat';
    }

    /**
     * Get booking status statistics for a specific user.
     *
     * @return array{total: int, pending: int, approved: int, rejected: int}
     */
    public static function getStatsByUser(int $userId): array
    {
        $baseQuery = self::where('user_id', $userId);

        return [
            'total'    => (clone $baseQuery)->count(),
            'pending'  => (clone $baseQuery)->where('status', 'pending')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];
    }

    /**
     * Calculate the late fee based on days overdue.
     */
    public function calculateFine(): float
    {
        if ($this->days_late <= 0) {
            return 0;
        }

        $finePerDay = (float) Setting::get('fine_per_day', 5000);

        return $this->days_late * $finePerDay;
    }

    /**
     * Get the number of days this booking is overdue.
     */
    public function getDaysLateAttribute(): int
    {
        if (!$this->return_deadline) {
            return 0;
        }

        $returnDate = $this->returned_at ?? now();
        return max(0, (int) $returnDate->startOfDay()->diffInDays($this->return_deadline->startOfDay(), false) * -1);
    }
}
