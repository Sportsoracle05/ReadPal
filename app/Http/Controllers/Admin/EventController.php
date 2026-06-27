<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;

class EventController extends Controller
{
    public function index()
    {
        return response()->json(DB::table('events')->get());
    }

    public function create()
    {
        return view('admin.events.create');

    }
    
    public function store(Request $request)
    {
        DB::table('events')->insert([
            'title' => $request->title,
            'description' => $request->description,
            'start' => $request->start,
            'end' => $request->end,
            'all_day' => $request->all_day ? 1 : 0,
        ]);
        return response()->json(['message' => 'Event added successfully']);
    }

    public function update(Request $request, $id)
    {
        DB::table('events')->where('id', $id)->update([
            'title' => $request->title,
            'description' => $request->description,
            'start' => $request->start,
            'end' => $request->end,
            'all_day' => $request->all_day ? 1 : 0,
        ]);
        return response()->json(['message' => 'Event updated']);
    }

    public function destroy($id)
    {
        DB::table('events')->where('id', $id)->delete();
        return response()->json(['message' => 'Event deleted']);
    }


    // Logging



    public function systemStatus(Schedule $schedule)
    {
        $events = collect($schedule->events())->map(function ($event) {
            return [
                'command' => $event->command,
                'expression' => $event->expression,
                'next_run' => $event->nextRunDate()->format('Y-m-d H:i:s'),
                // Note: Last run requires a cache or a specific log check
                'last_run' => cache()->get('last_run_' . $event->command, 'Never'),
            ];
        });

        return view('admin.system', compact('events'));
    }

    public function systemLogs()
    {
        // 1. Get Scheduled Tasks (Last & Next Run)
        $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);
        $tasks = collect($schedule->events())->map(function ($event) {
            return [
                'command'  => str_replace("'", "", $event->command),
                'interval' => $event->expression,
                'next'     => $event->nextRunDate()->format('H:i A'),
                'last'     => cache()->get('last_run_' . str_replace("'", "", $event->command), 'Never'),
            ];
        });

        // 2. Get Recent Logins (Reading the last 15 lines of our custom log)
        $loginPath = storage_path('logs/logins.log');
        $logins = file_exists($loginPath) ? array_reverse(array_slice(file($loginPath), -15)) : [];

        // 3. Get System Errors (Reading the last 20 lines of laravel.log)
        $errorPath = storage_path('logs/laravel.log');
        $errors = file_exists($errorPath) ? array_reverse(array_slice(file($errorPath), -20)) : [];

        return view('admin.system.logs', compact('tasks', 'logins', 'errors'));
    }


}
