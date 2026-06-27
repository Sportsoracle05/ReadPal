<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CgpaCourseOption extends Model
{
    use HasFactory;

    protected $table = 'cgpa_course_options';

    protected $fillable = [
        'level',
        'semester_type',
        'course_code',
        'course_title',
        'credit_unit',
        'is_active',
    ];

    protected $casts = [
        'level'         => 'integer',
        'semester_type' => 'integer',
        'credit_unit'   => 'integer',
        'is_active'     => 'boolean',
    ];

    // ── Scopes ─────────────────────────────────────────────────

    public function scopeForSemester($query, int $level, int $semesterType)
    {
        return $query->where('level', $level)
                     ->where('semester_type', $semesterType)
                     ->where('is_active', true)
                     ->orderBy('course_code');
    }

    // ── Static helper — returns options as a keyed array for Blade ──

    /**
     * Returns [course_code => ['title' => ..., 'unit' => ...]]
     * Used to pre-populate the course add modal dropdown.
     */
    public static function optionsFor(int $level, int $semesterType): array
    {
        return static::forSemester($level, $semesterType)
            ->get()
            ->keyBy('course_code')
            ->map(fn($c) => [
                'title' => $c->course_title,
                'unit'  => $c->credit_unit,
            ])
            ->toArray();
    }

    /**
     * JSON for frontend auto-fill when user selects a course code.
     */
    public static function jsonFor(int $level, int $semesterType): string
    {
        return json_encode(static::optionsFor($level, $semesterType));
    }
}