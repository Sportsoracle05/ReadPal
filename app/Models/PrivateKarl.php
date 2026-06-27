<?php
// ============================================================
//  app/Models/PrivateKarl.php
//  Direct-message karlss between two users.
//
//  Lifecycle:
//   1. Sender posts → stored, viewed_at = null
//   2. Receiver opens conversation → viewed_at stamped (NOW)
//   3. Nightly reset command deletes:
//        (a) viewed_at IS NOT NULL   — already-read messages
//        (b) created_at < NOW - 24h  — unread but stale (safety net)
// ============================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateKarl extends Model
{
    use HasFactory;

    protected $table = 'private_karls';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'content',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // ── Helpers ────────────────────────────────────────────────

    public function isRead(): bool
    {
        return $this->viewed_at !== null;
    }

    /**
     * Mark as read (stamp viewed_at).
     * Called when the receiver opens the conversation.
     */
    public function markAsRead(): void
    {
        if ($this->viewed_at === null) {
            $this->update(['viewed_at' => now()]);
        }
    }

    // ── Scopes ─────────────────────────────────────────────────

    /**
     * Messages in a conversation between two specific users.
     */
    public function scopeConversation($query, int $userA, int $userB)
    {
        return $query->where(function ($q) use ($userA, $userB) {
            $q->where('sender_id', $userA)->where('receiver_id', $userB);
        })->orWhere(function ($q) use ($userA, $userB) {
            $q->where('sender_id', $userB)->where('receiver_id', $userA);
        });
    }

    /**
     * Unread messages for a specific receiver.
     */
    public function scopeUnreadFor($query, int $userId)
    {
        return $query->where('receiver_id', $userId)->whereNull('viewed_at');
    }

public static function prune()
{
    return self::whereNotNull('viewed_at')
        ->where('viewed_at', '<=', now()->subHours(24))
        ->delete();
}

}