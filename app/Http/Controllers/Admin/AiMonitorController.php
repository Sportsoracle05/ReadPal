<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Ai\ProviderHealthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * AiMonitorController
 *
 * Admin-only dashboard for monitoring:
 *  - Token usage per provider (total + daily breakdown)
 *  - Provider success/failure rates
 *  - Live health check (ping each provider)
 *  - Recent error log
 *  - Daily chart data (14 days)
 *
 * All heavy queries are cached (5–15 min) since admins
 * refresh dashboards frequently. Cache is tagged so we can
 * selectively bust it after a live test.
 *
 * Routes (add to admin middleware group):
 *  GET  /admin/ai-monitor          → index()
 *  GET  /admin/ai-monitor/stats    → stats()    [AJAX refresh]
 *  POST /admin/ai-monitor/test     → testProvider() [live ping]
 *  POST /admin/ai-monitor/test-all → testAll()
 *  GET  /admin/ai-monitor/logs     → logs()     [paginated log]
 *  POST /admin/ai-monitor/prune    → prune()    [delete old logs]
 */
class AiMonitorController extends Controller
{
    public function __construct(
        private readonly ProviderHealthService $health,
    ) {}

    // ── Main Dashboard ─────────────────────────────────────────

    public function index()
    {
        $stats  = $this->getStats();
        $health = $this->health->testAll(); // cached 5 min

        return view('admin.ai-monitor.index', compact('stats', 'health'));
    }

    // ── AJAX: refresh stats block only ────────────────────────

    public function stats(): JsonResponse
    {
        return response()->json($this->getStats(forceRefresh: true));
    }

    // ── AJAX: test a single provider (live ping) ───────────────

    public function testProvider(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'provider' => ['required', 'in:gemini,groq,openrouter,huggingface'],
        ]);

        $result = $this->health->testOne($validated['provider']);

        return response()->json([
            'success'  => true,
            'provider' => $validated['provider'],
            'result'   => $result,
        ]);
    }

    // ── AJAX: test all providers at once ───────────────────────

    public function testAll(): JsonResponse
    {
        $results = $this->health->testAll(forceRefresh: true);

        return response()->json([
            'success' => true,
            'results' => $results,
        ]);
    }

    // ── AJAX: paginated raw log ────────────────────────────────

    public function logs(Request $request): JsonResponse
    {
        $provider = $request->query('provider');
        $status   = $request->query('status'); // 'success' | 'fail'
        $context  = $request->query('context');
        $page     = max(1, (int) $request->query('page', 1));
        $perPage  = 30;

        $query = DB::connection('ai')
            ->table('ai_provider_logs')
            ->select([
                'id', 'provider', 'model', 'success', 'tokens_used',
                'response_time_ms', 'context', 'error_message',
                'user_id', 'created_at',
            ])
            ->orderByDesc('created_at');

        if ($provider) {
            $query->where('provider', $provider);
        }

        if ($status === 'success') {
            $query->where('success', 1);
        } elseif ($status === 'fail') {
            $query->where('success', 0);
        }

        if ($context) {
            $query->where('context', $context);
        }

        $total = $query->count();
        $logs  = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

        return response()->json([
            'logs'        => $logs,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => ceil($total / $perPage),
        ]);
    }

    // ── Prune old logs ─────────────────────────────────────────

    public function prune(Request $request): JsonResponse
    {
        $days    = (int) $request->input('days', 90);
        $deleted = \App\Services\Ai\AiProviderLogService::prune($days);

        Cache::forget('ai_monitor_stats');

        return response()->json([
            'success' => true,
            'deleted' => $deleted,
            'message' => "Deleted {$deleted} log entries older than {$days} days.",
        ]);
    }

    // ──────────────────────────────────────────────────────────
    // Core Stats Aggregation
    // ──────────────────────────────────────────────────────────

    private function getStats(bool $forceRefresh = false): array
    {
        $cacheKey = 'ai_monitor_stats';

        if (!$forceRefresh) {
            $cached = Cache::get($cacheKey);
            if ($cached) return $cached;
        }

        $stats = [
            'summary'          => $this->getSummary(),
            'by_provider'      => $this->getByProvider(),
            'daily_tokens'     => $this->getDailyTokens(14),
            'daily_calls'      => $this->getDailyCalls(14),
            'recent_errors'    => $this->getRecentErrors(10),
            'top_users'        => $this->getTopUsers(5),
            'context_split'    => $this->getContextSplit(),
            'hourly_today'     => $this->getHourlyToday(),
            'generated_at'     => now()->toIso8601String(),
        ];

        Cache::put($cacheKey, $stats, 600); // 10-minute cache

        return $stats;
    }

    // Total lifetime + last 30 days overview
    private function getSummary(): array
    {
        $thirtyDays = now()->subDays(30);

        $lifetime = DB::connection('ai')->table('ai_provider_logs')
            ->selectRaw('
                COUNT(*) as total_calls,
                SUM(tokens_used) as total_tokens,
                SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as total_success,
                SUM(CASE WHEN success = 0 THEN 1 ELSE 0 END) as total_failures,
                AVG(CASE WHEN success = 1 AND response_time_ms > 0 THEN response_time_ms END) as avg_response_ms
            ')
            ->first();

        $last30 = DB::connection('ai')->table('ai_provider_logs')
            ->where('created_at', '>=', $thirtyDays)
            ->selectRaw('
                COUNT(*) as calls,
                SUM(tokens_used) as tokens,
                SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as success,
                SUM(CASE WHEN success = 0 THEN 1 ELSE 0 END) as failures
            ')
            ->first();

        $last24h = DB::connection('ai')->table('ai_provider_logs')
            ->where('created_at', '>=', now()->subHours(24))
            ->selectRaw('COUNT(*) as calls, SUM(tokens_used) as tokens')
            ->first();

        $successRate = $lifetime->total_calls > 0
            ? round($lifetime->total_success / $lifetime->total_calls * 100, 1)
            : 0;

        return [
            'total_calls'      => (int) $lifetime->total_calls,
            'total_tokens'     => (int) $lifetime->total_tokens,
            'total_success'    => (int) $lifetime->total_success,
            'total_failures'   => (int) $lifetime->total_failures,
            'success_rate'     => $successRate,
            'avg_response_ms'  => (int) round($lifetime->avg_response_ms ?? 0),
            'last_30d_calls'   => (int) $last30->calls,
            'last_30d_tokens'  => (int) $last30->tokens,
            'last_24h_calls'   => (int) $last24h->calls,
            'last_24h_tokens'  => (int) $last24h->tokens,
        ];
    }

    // Per-provider breakdown
    private function getByProvider(): array
    {
        $rows = DB::connection('ai')->table('ai_provider_logs')
            ->groupBy('provider')
            ->selectRaw('
                provider,
                COUNT(*) as total_calls,
                SUM(tokens_used) as total_tokens,
                SUM(CASE WHEN success = 1 THEN 1 ELSE 0 END) as successes,
                SUM(CASE WHEN success = 0 THEN 1 ELSE 0 END) as failures,
                AVG(CASE WHEN success = 1 AND response_time_ms > 0 THEN response_time_ms END) as avg_ms,
                MAX(created_at) as last_used
            ')
            ->orderByDesc('total_calls')
            ->get();

        $result = [];

        foreach ($rows as $row) {
            $rate = $row->total_calls > 0
                ? round($row->successes / $row->total_calls * 100, 1)
                : 0;

            $result[$row->provider] = [
                'total_calls'  => (int) $row->total_calls,
                'total_tokens' => (int) $row->total_tokens,
                'successes'    => (int) $row->successes,
                'failures'     => (int) $row->failures,
                'success_rate' => $rate,
                'avg_ms'       => (int) round($row->avg_ms ?? 0),
                'last_used'    => $row->last_used,
            ];
        }

        // Ensure all 4 providers appear even if unused
        foreach (['gemini', 'groq', 'openrouter', 'huggingface'] as $p) {
            if (!isset($result[$p])) {
                $result[$p] = [
                    'total_calls'  => 0, 'total_tokens' => 0,
                    'successes'    => 0, 'failures'     => 0,
                    'success_rate' => 0, 'avg_ms'       => 0,
                    'last_used'    => null,
                ];
            }
        }

        return $result;
    }

    // Daily token usage for chart (last N days)
    private function getDailyTokens(int $days): array
    {
        $rows = DB::connection('ai')->table('ai_provider_logs')
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, provider, SUM(tokens_used) as tokens')
            ->groupBy(DB::raw('DATE(created_at)'), 'provider')
            ->orderBy('date')
            ->get();
    
        $result = [];
        $start  = now()->subDays($days - 1)->startOfDay();
    
        for ($i = 0; $i < $days; $i++) {
            $dt = $start->copy()->addDays($i);
            $date = $dt->format('Y-m-d');
            
            // ✅ ADD 'date' AND 'label' HERE
            $result[$date] = [
                'date'         => $date,
                'label'        => $dt->format('M d'),
                'gemini'       => 0, 
                'groq'         => 0, 
                'openrouter'   => 0, 
                'huggingface'  => 0, 
                'total'        => 0
            ];
        }
    
        foreach ($rows as $row) {
            if (isset($result[$row->date])) {
                // Use the lowercase provider name to match your array keys
                $p = strtolower($row->provider);
                if (array_key_exists($p, $result[$row->date])) {
                    $result[$row->date][$p] = (int) $row->tokens;
                }
                $result[$row->date]['total'] += (int) $row->tokens;
            }
        }
    
        return array_values($result);
    }


    // Daily call count + success/fail split
    private function getDailyCalls(int $days): array
    {
        $rows = DB::connection('ai')->table('ai_provider_logs')
            ->where('created_at', '>=', now()->subDays($days))
            ->selectRaw('DATE(created_at) as date, SUM(success) as successes, COUNT(*) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $result = [];
        $start  = now()->subDays($days - 1)->startOfDay();

        for ($i = 0; $i < $days; $i++) {
            $date = $start->copy()->addDays($i)->format('Y-m-d');
            $row  = $rows[$date] ?? null;

            $result[] = [
                'date'      => $date,
                'label'     => $start->copy()->addDays($i)->format('M d'),
                'total'     => $row ? (int) $row->total     : 0,
                'successes' => $row ? (int) $row->successes : 0,
                'failures'  => $row ? (int) $row->total - (int) $row->successes : 0,
            ];
        }

        return $result;
    }

    // Last N errors
    private function getRecentErrors(int $limit): array
    {
        return DB::connection('ai')->table('ai_provider_logs')
            ->where('success', 0)
            ->whereNotNull('error_message')
            ->where('error_message', '!=', 'Not configured (no API key)')
            ->select(['provider', 'model', 'error_message', 'context', 'response_time_ms', 'created_at'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    // Top users by token usage
    private function getTopUsers(int $limit): array
    {
        return DB::connection('ai')->table('ai_provider_logs')
            ->whereNotNull('user_id')
            ->where('success', 1)
            ->groupBy('user_id')
            ->selectRaw('user_id, SUM(tokens_used) as tokens, COUNT(*) as calls')
            ->orderByDesc('tokens')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    // Chat vs Assignment split
    private function getContextSplit(): array
    {
        return DB::connection('ai')->table('ai_provider_logs')
            ->groupBy('context')
            ->selectRaw('context, COUNT(*) as calls, SUM(tokens_used) as tokens')
            ->get()
            ->keyBy('context')
            ->toArray();
    }

    // Hourly breakdown for today
    private function getHourlyToday(): array
    {
        $rows = DB::connection('ai')->table('ai_provider_logs')
            ->where('created_at', '>=', now()->startOfDay())
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as calls, SUM(tokens_used) as tokens')
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        $result = [];
        for ($h = 0; $h < 24; $h++) {
            $row      = $rows[$h] ?? null;
            $result[] = [
                'hour'   => sprintf('%02d:00', $h),
                'calls'  => $row ? (int) $row->calls  : 0,
                'tokens' => $row ? (int) $row->tokens : 0,
            ];
        }

        return $result;
    }
}
