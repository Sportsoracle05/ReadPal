<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lecture;
use App\Models\User;
use App\Http\Controllers\NotificationController;
use Carbon\Carbon;

class SendLectureReminders extends Command
{
    protected $signature = 'lectures:remind';
    protected $description = 'Send push notifications for upcoming lectures';

    public function handle()
    {
        cache()->put('last_run_lectures:remind', now()->format('d M, h:i A'));
        
        // 1. Define the windows we want to check (15 mins and 60 mins from now)
        $windows = [15, 60];

        foreach ($windows as $minutes) {
            // Find lectures starting exactly in $minutes (with 1-minute buffer)
            $targetTime = Carbon::now('Africa/Lagos')->addMinutes($minutes);
            
            $upcomingLectures = Lecture::with('resource')
                ->whereBetween('start_time', [
                    $targetTime->copy()->startOfMinute(),
                    $targetTime->copy()->endOfMinute()
                ])
                ->get();

            foreach ($upcomingLectures as $lecture) {
                $courseCode = $lecture->resource->course_code ?? 'Lecture';
                $title = "Reminder: {$courseCode} in {$minutes}m";
                $body  = "Starts at " . Carbon::parse($lecture->start_time)->format('h:i A') . " in {$lecture->hall}.";
                
                // 2. Broadcast only to users who enabled 'push_lecture_reminders'
                $this->broadcastToSubscribers($title, $body, $lecture);
            }
        }
    }

    protected function broadcastToSubscribers($title, $body, $lecture)
    {
        // Get tokens for users who have reminders turned ON
        $tokens = User::whereNotNull('fcm_token')
            ->where('push_lecture_reminders', true)
            ->pluck('fcm_token')
            ->toArray();

        if (!empty($tokens)) {
            // We bypass the full Controller instance and call your static method
            NotificationController::broadcastPush($title, $body, route('dashboard'));
            $this->info("Sent: {$title}");
        }
    }
}
