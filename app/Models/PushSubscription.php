<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushSubscription extends Model
{
    use HasFactory;

    protected $table = 'push_subscriptions';

    protected $fillable = [
        'user_id',
        'endpoint',
        'public_key',
        'auth_token',
        'user_agent',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ─────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Active subscriptions for users who have push_enabled = 1
     * and the specific preference enabled.
     *
     * @param  string  $preference  'push_lecture_alerts' | 'push_lecture_reminders'
     */
    public function scopeForNotification($query, string $preference)
    {
        return $query->active()
            ->whereHas('user', fn($q) => $q
                ->where('push_enabled', true)
                ->where($preference, true)
            );
    }

    // ── Helpers ────────────────────────────────────────────────

    /**
     * Return the subscription data array needed by minishlink/web-push.
     */
    public function toWebPushSubscription(): array
    {
        return [
            'endpoint' => $this->endpoint,
            'keys'     => [
                'p256dh' => $this->public_key,
                'auth'   => $this->auth_token,
            ],
        ];
    }
}