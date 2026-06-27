<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\SendLectureReminders;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
 
/*
|--------------------------------------------------------------------------
| Paystack / Payment Scheduler
|--------------------------------------------------------------------------
*/
 
// Revoke premium access for users whose subscription has expired.
// Runs every night at 00:05 to catch any expiries from the previous day.
Schedule::command(ExpirePremiumSubscriptions::class)
    ->daily()
    ->at('00:05')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/premium-expiry.log'));
 
// Optional: clean up permanently abandoned pending payments older than 24h
// to keep the payments table tidy.
Schedule::call(function () {
    \App\Models\Payment::where('status', 'pending')
        ->where('created_at', '<=', now()->subHours(24))
        ->update(['status' => 'abandoned']);
})->daily()->at('01:00')->name('abandon-stale-payments')->withoutOverlapping();


// Explicitly register the command so Artisan::call() can find it
Artisan::command('lectures:remind', function () {
    (new SendLectureReminders)->handle();
})->purpose('Send push notifications for upcoming lectures');

Schedule::command('lectures:remind')->everyThirtyMinutes();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
