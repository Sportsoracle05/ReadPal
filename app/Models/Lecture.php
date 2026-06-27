<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Lecture extends Model
{
    protected $fillable = [
        'resource_id',
        'hall',
        'lecturer',
        'start_time',
        'duration_minutes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    // ✅ Accessor so you can call $lecture->is_ongoing in Blade
    public function getIsOngoingAttribute()
    {
        $now = Carbon::now();
        $start = Carbon::parse($this->start_time);
        $end = $start->copy()->addMinutes($this->duration_minutes);

        return $now->between($start, $end);
    }

    public function isExpired()
    {
        return Carbon::now()->greaterThan(
            Carbon::parse($this->start_time)->addMinutes($this->duration_minutes)
        );
    }
}
