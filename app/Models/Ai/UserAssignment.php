<?php
namespace App\Models\Ai;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class UserAssignment extends Model
{
    protected $connection = 'ai';
    protected $table      = 'user_assignments';
    
    public function getRouteKeyName()
    {
        return 'identifier';
    }

    // Automatically generate the 30-char slug when creating
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->identifier = Str::random(30);
        });
    }

    protected $fillable = [
        'user_id', 'assignment_id', 'status',
        'sections_filled', 'total_sections',
    ];

    protected $casts = [
        'sections_filled' => 'integer',
        'total_sections'  => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function assignment()
    {
        return $this->belongsTo(Assignment::class, 'assignment_id');
    }

    /**
     * Eager load with sections to avoid N+1.
     * Use: $userAssignment->contents (keyed by section_id)
     */
    public function contents()
    {
        return $this->hasMany(UserAssignmentContent::class, 'user_assignment_id');
    }

    // ── Helpers ────────────────────────────────────────────────

    /**
     * Get content for a specific section (no extra query if already loaded).
     */
    public function getContentForSection(int $sectionId): ?UserAssignmentContent
    {
        if ($this->relationLoaded('contents')) {
            return $this->contents->firstWhere('section_id', $sectionId);
        }

        return $this->contents()->where('section_id', $sectionId)->first();
    }

    /**
     * Progress percentage for the progress bar.
     */
    public function getProgressPercent(): int
    {
        if ($this->total_sections === 0) {
            return 0;
        }

        return (int) round(($this->sections_filled / $this->total_sections) * 100);
    }

    /**
     * Get cross-database User model.
     */
    public function getUser(): ?\App\Models\User
    {
        return \App\Models\User::find($this->user_id);
    }
}