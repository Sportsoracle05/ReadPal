<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Visit;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{

    public function index()
{
    // ✅ Define time range first 
    $now = Carbon::now();
    $lastMonth = $now->copy()->subMonth();
    $startOfMonth = $now->copy()->startOfMonth();
    $startOfLastMonth = $startOfMonth->copy()->subMonth();
    $endOfLastMonth = $startOfMonth->copy()->subSecond();

    // Current month stats
    $uniqueVisitors = Visit::whereMonth('visited_at', $now->month)
        ->distinct('ip')->count('ip');

    $totalPageviews = Visit::whereMonth('visited_at', $now->month)->count();

    // Last month stats
    $lastMonthVisitors = Visit::whereMonth('visited_at', $lastMonth->month)
        ->distinct('ip')->count('ip');

    $lastMonthPageviews = Visit::whereMonth('visited_at', $lastMonth->month)->count();

    // Percent changes
    $visitorChange = $lastMonthVisitors > 0 ? (($uniqueVisitors - $lastMonthVisitors) / $lastMonthVisitors) * 100 : 0;
    $pageviewChange = $lastMonthPageviews > 0 ? (($totalPageviews - $lastMonthPageviews) / $lastMonthPageviews) * 100 : 0;

    // Mock data for bounce rate and duration
    $bounceRate = 54;
    $bounceChange = -1.59;
    $duration = "2m 56s";
    $durationChange = 7;

     // 👇 Chart data (Visitors & Pageviews per day for current month)
    $dailyData = Visit::select(
            DB::raw('DATE(visited_at) as date'),
            DB::raw('COUNT(*) as pageviews'),
            DB::raw('COUNT(DISTINCT session_id) as visitors')
        )
        ->whereBetween('visited_at', [$startOfMonth, $now])
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

    $dates = $dailyData->pluck('date')->map(fn($d) => date('M j', strtotime($d)));
    $visitors = $dailyData->pluck('visitors');
    $pageviews = $dailyData->pluck('pageviews');

    // Top 4 materials by view count (past 30 days)
    $topMaterials = DB::table('visits')
        ->select('materials.title', DB::raw('COUNT(visits.id) as total_views'))
        ->join('materials', 'visits.material_id', '=', 'materials.id')
        ->whereNotNull('visits.material_id')
        ->where('visited_at', '>=', Carbon::now()->subDays(30))
        ->groupBy('materials.title')
        ->orderByDesc('total_views')
        ->limit(5)
        ->get();

    // Top 5 most visited pages in the last 30 days
    $topPages = DB::table('visits')
        ->select('page', DB::raw('COUNT(id) as total_visits'))
        ->where('visited_at', '>=', Carbon::now()->subDays(30))
        ->groupBy('page')
        ->orderByDesc('total_visits')
        ->limit(5)
        ->get();

    // Live visitors = unique visitors within the last 5 minutes
    $liveVisitors = DB::table('visits')
        ->where('visited_at', '>=', Carbon::now()->subMinutes(5))
        ->distinct('session_id')
        ->count('session_id');

    // Average daily users (past 30 days)
    $avgDailyUsers = DB::table('visits')
        ->select(DB::raw('COUNT(DISTINCT session_id) / 30 as avg_daily'))
        ->where('visited_at', '>=', Carbon::now()->subDays(30))
        ->value('avg_daily');

    // Average weekly users (past 4 weeks)
    $avgWeeklyUsers = DB::table('visits')
        ->select(DB::raw('COUNT(DISTINCT session_id) / 4 as avg_weekly'))
        ->where('visited_at', '>=', Carbon::now()->subWeeks(4))
        ->value('avg_weekly');

    // Average monthly users (past 3 months)
    $avgMonthlyUsers = DB::table('visits')
        ->select(DB::raw('COUNT(DISTINCT session_id) / 3 as avg_monthly'))
        ->where('visited_at', '>=', Carbon::now()->subMonths(3))
        ->value('avg_monthly');



    return view('admin.dashboard', compact(
        'uniqueVisitors', 'visitorChange',
        'totalPageviews', 'pageviewChange',
        'bounceRate', 'bounceChange',
        'duration', 'durationChange',
        'dates', 'visitors', 'pageviews',
        'topMaterials', 'topPages', 'liveVisitors',
        'avgDailyUsers', 'avgWeeklyUsers',
        'avgMonthlyUsers'
    ));
}
}
