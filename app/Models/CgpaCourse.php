<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CgpaCourse extends Model
{
    use HasFactory;

    protected $table = 'cgpa_courses';

    protected $fillable = [
        'semester_id',
        'course_code',
        'unit',
        'grade_point',
        'grade_letter',
    ];

    protected $casts = [
        'unit'        => 'integer',
        'grade_point' => 'integer',
    ];

    // ---------------------------------------------------------------
    //  AAUA Grade Map  (grade_letter → grade_point)
    // ---------------------------------------------------------------

    public const GRADE_MAP = [
        'A' => 5,
        'B' => 4,
        'C' => 3,
        'D' => 2,
        'E' => 1,
        'F' => 0,
    ];

    // ---------------------------------------------------------------
    //  Relationship
    // ---------------------------------------------------------------

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    // ---------------------------------------------------------------
    //  Accessor – Quality Point = Unit × Grade Point
    // ---------------------------------------------------------------

    /**
     * Quality point for a single course row.
     * e.g.  3 units × 4 pts (B)  = 12 quality points
     */
    public function getQualityPointAttribute(): int
    {
        return $this->unit * $this->grade_point;
    }

    // ---------------------------------------------------------------
    //  Mutator – Auto-resolve grade_point when grade_letter is set
    // ---------------------------------------------------------------

    /**
     * Setting the grade letter automatically updates the numeric grade point.
     */
    public function setGradeLetterAttribute(string $letter): void
    {
        $letter = strtoupper(trim($letter));
        $this->attributes['grade_letter'] = $letter;
        $this->attributes['grade_point']  = self::GRADE_MAP[$letter] ?? 0;
    }
}
