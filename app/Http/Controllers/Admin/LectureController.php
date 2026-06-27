<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lecture;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Http\Controllers\NotificationController;

class LectureController extends Controller
{
    public function index()
    {
        $lectures = Lecture::with('resource')->orderBy('start_time', 'asc')->paginate(10);
        return view('admin.lectures.index', compact('lectures'));
    }

    public function create()
    {
        $resources = Resource::all();
        return view('admin.lectures.create', compact('resources'));
    }

    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'resource_id'      => 'required|exists:resources,id',
            'hall'             => 'required|max:100',
            'lecturer'         => 'required|max:100',
            'start_time'       => 'required|date',
            'duration_minutes' => 'required|integer|min:10',
        ]);

        // 1. Save lecture first
        $lecture = Lecture::create($validated);

        // --- NEW: TRIGGER PUSH NOTIFICATION ---
        try {
            // Fetch course code for a better message
            $courseCode = $lecture->resource->course_code ?? 'New Lecture';
            $startTime  = Carbon::parse($lecture->start_time)->format('h:i A');

            NotificationController::broadcastPush(
                "New Lecture: $courseCode", 
                "Scheduled at $startTime in $lecture->hall. Check your calendar for details!",
                route('calender.index') // Redirects students to dashboard on click
            );
        } catch (\Exception $e) {
            Log::error('FCM Broadcast failed: ' . $e->getMessage());
        }
        // ---------------------------------------

        // 2. Compute end time safely
        $endTime = Carbon::parse($lecture->start_time)->addMinutes((int) $lecture->duration_minutes);

        // 3. Google Calendar Sync (Your existing logic)
        try {
            if (function_exists('getGoogleAccessToken')) {
                $accessToken = getGoogleAccessToken();
                if ($accessToken) {
                    $event = [
                        'summary' => $lecture->resource->course_code 
                            ? "Lecture - {$lecture->resource->course_code}" 
                            : 'Lecture',
                        'description' => "Lecturer: {$lecture->lecturer}\nHall: {$lecture->hall}",
                        'start' => [
                            'dateTime' => Carbon::parse($lecture->start_time)->toAtomString(),
                            'timeZone' => 'Africa/Lagos',
                        ],
                        'end' => [
                            'dateTime' => $endTime->toAtomString(),
                            'timeZone' => 'Africa/Lagos',
                        ],
                    ];

                    $response = Http::withToken($accessToken)
                        ->post('https://www.googleapis.com/calendar/v3/calendars/primary/events', $event);

                    if ($response->successful()) {
                        $lecture->update(['google_event_id' => $response->json('id')]);
                    } else {
                        Log::error('Google Calendar failed', ['response' => $response->json()]);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Google Calendar sync failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.lectures.index')
            ->with('success', 'Lecture added, synced to Google, and students notified!');
    }



    public function edit(Lecture $lecture)
    {
        $resources = Resource::all();
        return view('admin.lectures.edit', compact('lecture', 'resources'));
    }

    public function update(Request $request, Lecture $lecture)
    {
        $validated = $request->validate([
            'hall' => 'required|max:100',
            'lecturer' => 'required|max:100',
            'start_time' => 'required|date',
            'duration_minutes' => 'required|integer|min:10',
        ]);

        // Track changes before the update happens
        $significantChange = $lecture->hall !== $request->hall || 
                            $lecture->start_time != $request->start_time;

        $lecture->update($validated);

        // 1. Send Notifications directly through the NotificationController
        if ($significantChange) {
            $courseCode = $lecture->resource->course_code ?? 'Lecture';
            $newTime = \Carbon\Carbon::parse($lecture->start_time)->format('h:i A');
            
            $title = "Update: {$courseCode}";
            $body  = "The lecture has been moved to {$lecture->hall} at {$newTime}.";
            $url   = route('dashboard'); // Or a specific lecture link

            // Call your static broadcast method
            \App\Http\Controllers\NotificationController::broadcastPush($title, $body, $url);
        }

        // 2. Update Google Calendar (Existing logic)
        if ($lecture->google_event_id && function_exists('getGoogleAccessToken')) {
            try {
                $accessToken = getGoogleAccessToken();
                $endTime = \Carbon\Carbon::parse($lecture->start_time)->addMinutes((int) $lecture->duration_minutes);

                $event = [
                    'summary' => "UPDATED: Lecture - " . ($lecture->resource->course_code ?? 'Lecture'),
                    'description' => "Lecturer: {$lecture->lecturer}\nHall: {$lecture->hall}",
                    'start' => [
                        'dateTime' => \Carbon\Carbon::parse($lecture->start_time)->toAtomString(),
                        'timeZone' => 'Africa/Lagos',
                    ],
                    'end' => [
                        'dateTime' => $endTime->toAtomString(),
                        'timeZone' => 'Africa/Lagos',
                    ],
                ];

                \Illuminate\Support\Facades\Http::withToken($accessToken)
                    ->patch("https://googleapis.com{$lecture->google_event_id}", $event);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Google Calendar update failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.lectures.index')
            ->with('success', 'Lecture updated and push notifications broadcasted.');
    }



    public function destroy(Lecture $lecture)
    {
        // Delete from Google Calendar if exists
        if ($lecture->google_event_id && function_exists('getGoogleAccessToken')) {
            try {
                $accessToken = getGoogleAccessToken();
                Http::withToken($accessToken)
                    ->delete("https://www.googleapis.com/calendar/v3/calendars/primary/events/{$lecture->google_event_id}");
            } catch (\Throwable $e) {
                Log::error('Google Calendar deletion failed: ' . $e->getMessage());
            }
        }

        $lecture->delete();

        return redirect()->route('admin.lectures.index')->with('success', 'Lecture deleted.');
    }
}
