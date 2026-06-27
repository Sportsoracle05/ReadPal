<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function liveData()
    {
        $now = Carbon::now();
        $start = $now->copy()->subHours(24);

        $visits = DB::table('visits')
            ->select(
                DB::raw('HOUR(visited_at) as hour'),
                DB::raw('COUNT(DISTINCT session_id) as unique_visitors'),
                DB::raw('COUNT(id) as total_views')
            )
            ->where('visited_at', '>=', $start)
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return response()->json([
            'labels' => $visits->pluck('hour'),
            'uniqueVisitors' => $visits->pluck('unique_visitors'),
            'pageViews' => $visits->pluck('total_views'),
        ]);
    }

    public function index()
    {
        return view('admin.analytics');
    }
}
