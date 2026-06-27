<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str; // <--- ADD THIS

class Semester extends Model
{
    use HasFactory;

    protected $table = 'semesters';

    protected $fillable = [
        'user_id',
        'level',
        'semester_type',
        'slug', // <--- RECOMMENDED
    ];

    protected $casts = [
        'level'         => 'integer',
        'semester_type' => 'integer',
        'user_id'       => 'integer', // <--- HELPS FIX THE AUTH ERROR FROM EARLIER
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($semester) {
            // Generates a random 15-character string
            $semester->slug = Str::random(15);
        });
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(CgpaCourse::class, 'semester_id');
    }

    // ---------------------------------------------------------------
    //  Computed / Accessor Helpers
    // ---------------------------------------------------------------

    /**
     * Human-readable label, e.g. "200L – 1st Semester"
     */
    public function getLabelAttribute(): string
    {
        $ordinal = $this->semester_type === 1 ? '1st' : '2nd';
        return "{$this->level}L – {$ordinal} Semester";
    }

    /**
     * Total credit units registered in this semester.
     */
    public function getTotalUnitsAttribute(): int
    {
        return $this->courses->sum('unit');
    }

    /**
     * Total quality points earned in this semester.
     */
    public function getTotalQualityPointsAttribute(): float
    {
        return $this->courses->sum('quality_point');
    }

    /**
     * GPA for this semester (0.00 – 5.00).
     */
    public function getGpaAttribute(): float
    {
        $totalUnits = $this->total_units;
        if ($totalUnits === 0) {
            return 0.00;
        }
        return round($this->total_quality_points / $totalUnits, 2);
    }
}
