<?php
// ============================================================
//  app/Models/Thread.php
// ============================================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Thread extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'type', 'is_pinned', 'created_by'];

    protected $casts = ['is_pinned' => 'boolean'];

    // ── Relationships ──────────────────────────────────────────
    public function karls(): HasMany
    {
        return $this->hasMany(Karl::class)->latest();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Helpers ────────────────────────────────────────────────
    public function isGeneral(): bool
    {
        return $this->type === 'general';
    }

    /**
     * Ensure the general thread always exists.
     * Call from a seeder or service provider boot.
     */
    public static function ensureGeneralExists(): self
    {
        return static::firstOrCreate(
            ['slug' => 'general'],
            [
                'name'        => 'General',
                'description' => 'The main thread for all ReadPal users. Drop your karls here.',
                'type'        => 'general',
                'is_pinned'   => true,
            ]
        );
    }
}