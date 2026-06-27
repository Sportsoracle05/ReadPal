<?php
use App\Models\ActiveSemesterSetting;
use App\Models\AcademicSemester;

function currentSemester()
{
    $selected = ActiveSemesterSetting::first();

    if ($selected && $selected->semester_id) {
        return AcademicSemester::find($selected->semester_id);
    }

    return AcademicSemester::whereDate('start_date', '<=', now())
        ->whereDate('end_date', '>=', now())
        ->first();
}