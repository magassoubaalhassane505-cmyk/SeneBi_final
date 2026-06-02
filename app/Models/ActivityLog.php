<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'actor_id',
        'target_user_id',
        'action',
        'details',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public static function record(string $action, ?int $targetUserId = null, ?string $details = null): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('activity_logs')) {
            return;
        }

        static::create([
            'actor_id' => auth()->id(),
            'target_user_id' => $targetUserId,
            'action' => $action,
            'details' => $details,
        ]);
    }
}
