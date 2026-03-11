<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'scope',
        'faculty',
        'capacity',
        'facilities',
        'building',
        'floor',
        'location',
        'status',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'assigned_room_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'tersedia')->where('is_active', true);
    }

    public function scopeByScope($query, string $scope)
    {
        return $query->where('scope', $scope);
    }

    public function scopeByBuilding($query, string $building)
    {
        return $query->where('building', $building);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'tersedia' => 'Tersedia',
            'dipakai' => 'Sedang Dipakai',
            'maintenance' => 'Maintenance',
            default => $this->status,
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'tersedia' => 'success',
            'dipakai' => 'warning',
            'maintenance' => 'danger',
            default => 'secondary',
        };
    }

    public function getFullLocationAttribute(): string
    {
        $parts = array_filter([$this->building, $this->floor ? 'Lt. ' . $this->floor : null]);
        return implode(', ', $parts) ?: ($this->location ?? '-');
    }
}
