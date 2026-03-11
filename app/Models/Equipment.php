<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment';

    protected $fillable = [
        'name',
        'code',
        'description',
        'category',
        'quantity',
        'is_available',
        'condition',
        'location',
        'assigned_room_id',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'quantity' => 'integer',
    ];

    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'booking_equipment')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function assignedRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'assigned_room_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function getConditionLabelAttribute(): string
    {
        return match ($this->condition) {
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            default => $this->condition,
        };
    }

    public function getConditionBadgeAttribute(): string
    {
        return match ($this->condition) {
            'baik' => 'success',
            'rusak_ringan' => 'warning',
            'rusak_berat' => 'danger',
            default => 'secondary',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'elektronik' => 'Elektronik',
            'furniture' => 'Furniture',
            'audio_visual' => 'Audio Visual',
            'lainnya' => 'Lainnya',
            default => $this->category,
        };
    }
}
