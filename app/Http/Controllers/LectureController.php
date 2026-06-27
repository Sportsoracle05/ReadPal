<?php

namespace App\Http\Controllers;

use App\Models\Lecture;
use App\Models\Event;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LectureController extends Controller
{
    public function index()
{
    // 1. Delete expired lectures
    Lecture::whereRaw('DATE_ADD(start_time, INTERVAL duration_minutes MINUTE) < NOW()')->delete();

    // 2. Get upcoming or ongoing lectures
    $lectures = Lecture::with('resource')
        ->whereRaw('DATE_ADD(start_time, INTERVAL duration_minutes MINUTE) > NOW()')
        ->orderBy('start_time')
        ->get();

    // 3. GROUP lectures by date for the "Day" headers in your calendar
    $groupedLectures = $lectures->groupBy(function($lecture) {
        return $lecture->start_time->format('Y-m-d');
    });

    // 4. Prepare events for the JS Calendar (Keep your existing logic)
    $events = $lectures->map(function($lecture) {
        return [
            'id' => $lecture->id,
            'calendarId' => '1',
            'title' => $lecture->resource->course_code ?? 'Lecture',
            'category' => 'time',
            'start' => $lecture->start_time->toIso8601String(),
            'end' => $lecture->start_time->copy()->addMinutes($lecture->duration_minutes)->toIso8601String(),
            'location' => $lecture->hall,
        ];
    });

    // Pass groupedLectures instead of raw lectures to match your Blade logic
    return view('calender.index', compact('groupedLectures', 'events'));
}

}
