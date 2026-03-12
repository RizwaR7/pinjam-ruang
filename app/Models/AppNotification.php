<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class AppNotification extends Model
{
    use HasFactory;

    protected $table = 'app_notifications';

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'icon',
        'is_read',
        'data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Create a notification for a user
     */
    public static function notify(
        int $userId,
        string $title,
        string $message,
        string $type = 'info',
        ?string $icon = null,
        ?array $data = null
    ): self {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'icon' => $icon,
            'data' => $data,
        ]);
    }

    /**
     * Create notifications for multiple users in a single batch insert.
     *
     * @param  iterable<int>  $userIds
     */
    public static function notifyMany(
        iterable $userIds,
        string $title,
        string $message,
        string $type = 'info',
        ?string $icon = null,
        ?array $data = null
    ): void {
        $now = now();
        $encodedData = $data !== null ? json_encode($data) : null;

        $rows = [];
        foreach ($userIds as $userId) {
            $rows[] = [
                'user_id'    => $userId,
                'title'      => $title,
                'message'    => $message,
                'type'       => $type,
                'icon'       => $icon,
                'is_read'    => false,
                'data'       => $encodedData,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($rows)) {
            DB::table('app_notifications')->insert($rows);
        }
    }
}
