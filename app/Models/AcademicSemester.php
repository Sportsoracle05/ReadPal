<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicSemester extends Model
{
    protected $table = 'academic_semesters';

    protected $fillable = [
        'session_id',
        'name',
        'start_date',
        'end_date',
        'is_active'
    ];

    public function session()
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }
}
