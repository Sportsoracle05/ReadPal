<?php
// ============================================================
//  app/Models/Karl.php
//  A "karl" is a public message posted inside a thread.
// ============================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Karl extends Model
{
    use HasFactory;

    protected $table = 'karls';

    protected $fillable = [
        'thread_id',
        'user_id',
        'content',
        'is_anonymous',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * The real author — always available internally.
     * UI must check is_anonymous before rendering.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Display Helpers ────────────────────────────────────────

    /**
     * The display name shown in the UI.
     * Returns "Anonymous" when the poster opted in.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Anonymous';
        }

        return $this->author?->firstname
            ? ($this->author->firstname . ($this->author->lastname ? ' ' . $this->author->lastname : ''))
            : 'Unknown';
    }

    /**
     * Whether this karl belongs to the given user.
     */
   
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

}