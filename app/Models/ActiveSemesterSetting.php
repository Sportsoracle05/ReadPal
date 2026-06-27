<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActiveSemesterSetting extends Model
{
    protected $table = 'active_semester_settings';

    protected $fillable = ['semester_id'];

    public function semester()
    {
        return $this->belongsTo(AcademicSemester::class, 'semester_id');
    }
}
