<?php
namespace App\Models\Ai;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Assignment extends Model
{
    use SoftDeletes;

    protected $connection = 'ai';
    protected $table      = 'assignments';
    
    protected static function booted()
    {
        static::creating(function ($assignment) {
            // Automatically generate the 30-char slug before saving to DB
            if (empty($assignment->identifier)) {
                $assignment->identifier = Str::random(30);
            }
        });
    }
    
    public function getRouteKeyName(): string
    {
        return 'identifier';
    }

    protected $fillable = [
        'created_by', 'title', 'topic', 'course',
        'description', 'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────

    /**
     * Sections ordered by position.
     * ONE query with sections + questions (JSON auto-decoded).
     */
    public function sections()
    {
        return $this->hasMany(AssignmentSection::class, 'assignment_id')
                    ->orderBy('position');
    }

    public function userAssignments()
    {
        return $this->hasMany(UserAssignment::class, 'assignment_id');
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('is_published', 1);
    }

    // ── Cache helpers ─────────────────────────────────────────

    /**
     * Get assignment with all sections from cache.
     * Structure rarely changes → cache for 1 hour.
     * Invalidated in AdminAssignmentController when admin edits.
     */
    public static function getCached(int $id): ?self
    {
        return Cache::remember("assignment_{$id}", 3600, function () use ($id) {
            return self::with('sections')->find($id);
        });
    }

    public static function clearCache(int $id): void
    {
        Cache::forget("assignment_{$id}");
    }
}