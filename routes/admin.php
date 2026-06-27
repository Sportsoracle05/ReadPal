<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\CgpaCourseOptionController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\LectureController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RepController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\AcademicController;

use App\Http\Controllers\TextExtractionController;
use App\Http\Controllers\QuestionGenerationController;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Carbon\Carbon;


/*
|--------------------------------------------------------------------------
| Helper: Get & Refresh Google Access Token
|--------------------------------------------------------------------------
*/
if (!function_exists('getGoogleAccessToken')) {
    function getGoogleAccessToken()
    {
        $record = DB::table('google_tokens')->where('user_id', auth()->id() ?? 1)->first();
        if (!$record) return null;

        // Token expired → refresh it
        if (Carbon::now()->greaterThan($record->expires_at)) {

            $refresh = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id'     => env('GOOGLE_CLIENT_ID'),
                'client_secret' => env('GOOGLE_CLIENT_SECRET'),
                'refresh_token' => $record->refresh_token,
                'grant_type'    => 'refresh_token',
            ])->json();

            if (!isset($refresh['access_token'])) return null;

            DB::table('google_tokens')->where('id', $record->id)->update([
                'access_token' => $refresh['access_token'],
                'expires_at'   => Carbon::now()->addSeconds($refresh['expires_in']),
                'updated_at'   => now(),
            ]);

            return $refresh['access_token'];
        }

        return $record->access_token;
    }
}



/*
|--------------------------------------------------------------------------
| Google OAuth Routes (OUTSIDE AUTH MIDDLEWARE)
|--------------------------------------------------------------------------
*/

// Step 1: Send user to Google
Route::get('/admin/google/auth', function () {

    $query = http_build_query([
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'redirect_uri'  => env('GOOGLE_REDIRECT_URI'),
        'response_type' => 'code',
        'scope'         => 'https://www.googleapis.com/auth/calendar.events',
        'access_type'   => 'offline',
        'prompt'        => 'consent',
    ]);

    return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
})->name('admin.google.auth');

// Step 2: Google Callback
Route::get('/admin/google/callback', function (Request $request) {

    $token = Http::asForm()->post('https://oauth2.googleapis.com/token', [
        'code'          => $request->code,
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect_uri'  => env('GOOGLE_REDIRECT_URI'),
        'grant_type'    => 'authorization_code',
    ])->json();

    if (!isset($token['access_token'])) {
        return dd('Google auth failed:', $token);
    }

    DB::table('google_tokens')->updateOrInsert(
        ['user_id' => auth()->id() ?? 1],
        [
            'access_token'  => $token['access_token'],
            'refresh_token' => $token['refresh_token'] ?? null,
            'expires_at'    => Carbon::now()->addSeconds($token['expires_in']),
            'updated_at'    => now(),
        ]
    );

    session(['google_access_token' => $token['access_token']]);

    return redirect()->route('admin.dashboard')
        ->with('success', 'Google Calendar connected successfully!');
})->name('admin.google.callback');




/*
|--------------------------------------------------------------------------
| Admin Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Events
        Route::resource('events', EventController::class)->names('events');

        // Run Cron Now
        Route::post('/run-task', function (\Illuminate\Http\Request $request) {

                $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);

                if (file_exists(base_path('routes/console.php'))) {
                    require base_path('routes/console.php');
                }

                // ✅ CLEAN incoming command properly
                $command = trim($request->command);

                if (preg_match('/artisan\s+([^\s]+)/i', $command, $m)) {
                    $command = trim($m[1]);
                } else {
                    $command = trim(preg_replace('/^.*artisan\s+/i', '', $command));
                }

                // ✅ CLEAN allowed commands
                $allowed = collect($schedule->events())
                    ->map(function ($event) {
                        $raw = $event->command;

                        // ✅ extract command after artisan (handles quotes + paths)
                        if (preg_match('/artisan"?\s+([^\s"]+)/i', $raw, $m)) {
                            return trim($m[1]);
                        }

                        // fallback
                        return trim(preg_replace('/^.*artisan"?\s+/i', '', $raw));
                    })
                    ->filter()
                    ->unique()
                    ->values()
                    ->toArray();

                // ❌ BLOCK unknown
                if (!in_array($command, $allowed)) {
                    dd($command, $allowed); // debug if needed
                    abort(403, 'Unauthorized command');
                }

                \Artisan::call($command);

                cache()->put("last_run_{$command}", now()->format('d M, h:i A'));

                return back()->with('success', 'Task executed');

            })->name('run.task');



        // Logs
        Route::get('/logs', function () {
             // Force the scheduler to load all tasks from routes/console.php
            $schedule = app(Schedule::class);
            
            // In Laravel 11+, we need to ensure the console routes are loaded
            if (file_exists(base_path('routes/console.php'))) {
                require base_path('routes/console.php');
            }

            $tasks = collect($schedule->events())->map(function ($event) {
                $rawCommand = $event->command;
                $cleanCmd = 'Unknown';

                // This regex looks for 'artisan' followed by the actual command
                // It ignores everything before it (like C:\Users\...)
                if (preg_match('/artisan\s+([a-z0-9:]+)/i', $rawCommand, $matches)) {
                    $cleanCmd = $matches[1];
                } else {
                    // Fallback: strip quotes and common prefixes
                    $cleanCmd = trim(str_replace(["'", '"', 'php artisan', 'php'], "", $rawCommand));
                }

                return [
                    'command'  => $cleanCmd, // Should now be exactly 'lectures:remind'
                    'interval' => $event->expression,
                    'next'     => $event->nextRunDate()->format('H:i A'),
                    'last'     => cache()->get("last_run_{$cleanCmd}", 'Never'), 
                ];
            });


            // 2. Get Recent Logins from our custom log file
            $loginPath = storage_path('logs/logins.log');
            $logins = file_exists($loginPath) ? array_reverse(array_slice(file($loginPath), -15)) : [];

            // 3. Get System Errors from laravel.log
            $errorPath = storage_path('logs/laravel.log');
            $systemErrors = file_exists($errorPath) ? array_reverse(array_slice(file($errorPath), -20)) : [];

            // Pass ALL required variables to the view
            return view('admin.system.logs', compact('tasks', 'logins', 'systemErrors'));
        })->name('logs');



        /*
        |--------------------------------------------------------------------------
        | MATERIALS — All Admins
        |--------------------------------------------------------------------------
        */
        Route::resource('materials', MaterialController::class);

        Route::post('materials/{id}/extract-text', [TextExtractionController::class, 'extract'])
            ->name('materials.extract');

        Route::get('materials/{id}/generate-questions', [QuestionGenerationController::class, 'generate'])
            ->name('materials.generate');

        Route::get('materials/questions/{filename}', [QuestionGenerationController::class, 'showQuestions'])
            ->name('materials.questions');

        Route::get('/academic', [AcademicController::class, 'index'])->name('academic.index');
        Route::post('/academic/session', [AcademicController::class, 'storeSession'])->name('academic.session.store');
        Route::post('/academic/semester', [AcademicController::class, 'storeSemester'])->name('academic.semester.store');
        Route::post('/academic/select/{id}', [AcademicController::class, 'selectSemester'])->name('academic.semester.select');


        /*
        |--------------------------------------------------------------------------
        | REP-ONLY
        |--------------------------------------------------------------------------
        */
        Route::middleware('rep')->group(function () {
            Route::resource('lectures', LectureController::class);
        });


        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN ONLY
        |--------------------------------------------------------------------------
        */
        Route::middleware('super')->group(function () {
            Route::resource('resources', ResourceController::class);
            Route::resource('users', UserController::class);
            Route::resource('reps', RepController::class);

            Route::get('/analytics/live', [AnalyticsController::class, 'liveData'])
                ->name('analytics.live');

            Route::get('/analytics', [AnalyticsController::class, 'index'])
                ->name('analytics');
        });


        /*
        |--------------------------------------------------------------------------
        | Google Calendar Event Creation (requires logged-in admin)
        |--------------------------------------------------------------------------
        */
        Route::post('/google/add-event', function (Request $request) {

            $accessToken = getGoogleAccessToken();
            if (!$accessToken) return redirect()->route('admin.google.auth');

            $event = [
                'summary'     => $request->title ?? 'Untitled Event',
                'description' => $request->description ?? '',
                'start'       => [
                    'dateTime' => Carbon::parse($request->start)->toAtomString(),
                    'timeZone' => 'Africa/Lagos'
                ],
                'end'         => [
                    'dateTime' => Carbon::parse($request->end)->toAtomString(),
                    'timeZone' => 'Africa/Lagos'
                ],
            ];

            return Http::withToken($accessToken)
                ->post('https://www.googleapis.com/calendar/v3/calendars/primary/events', $event)
                ->json();
        });

    });

// ================================================================
//  Admin: Course Options Management (admin + super only)
// ================================================================

Route::middleware(['auth', 'admin'])
    ->prefix('admin/cgpa')
    ->name('admin.cgpa.')
    ->group(function () {

        // List & create (index has inline add form)
        Route::get('/',      [CgpaCourseOptionController::class, 'index'])
            ->name('index');

        Route::post('/',     [CgpaCourseOptionController::class, 'store'])
            ->name('store');

        // Update + delete specific option
        Route::put('/{option}',    [CgpaCourseOptionController::class, 'update'])
            ->name('update');

        Route::delete('/{option}', [CgpaCourseOptionController::class, 'destroy'])
            ->name('destroy');
    });