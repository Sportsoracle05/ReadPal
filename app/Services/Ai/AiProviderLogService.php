<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * AiProviderLogService
 *
 * Lightweight logging service for all AI provider calls.
 * Inserted into every provider service (Gemini, Groq, etc.)
 * to record success/failure, tokens, and response time.
 *
 * Uses raw DB insert (not Eloquent) to keep overhead minimal.
 * One INSERT per AI call — negligible on 1GB RAM.
 *
 * Usage pattern:
 *   $timer = AiProviderLogService::startTimer();
 *   $result = $this->gemini->generate(...);
 *   AiProviderLogService::record($result, $timer, 'chat', $userId);
 */
class AiProviderLogService
{
    /**
     * Start a microsecond timer.
     * @return float  microtime
     */
    public static function startTimer(): float
    {
        return microtime(true);
    }

    /**
     * Record a provider call result.
     *
     * @param array  $result    The result array from any provider service
     * @param float  $startTime From startTimer()
     * @param string $context   'chat' | 'assignment'
     * @param int|null $userId
     */
    public static function record(
        array  $result,
        float  $startTime,
        string $context = 'chat',
        ?int   $userId  = null
    ): void {
        $ms = (int) round((microtime(true) - $startTime) * 1000);

        // Truncate error messages to avoid large rows
        $errorMsg = null;
        if (!$result['success'] && !empty($result['error'])) {
            $errorMsg = mb_substr($result['error'], 0, 200);
        }

        try {
            DB::connection('ai')->table('ai_provider_logs')->insert([
                'provider'        => $result['provider']     ?? 'unknown',
                'model'           => $result['model']        ?? null,
                'success'         => $result['success']      ? 1 : 0,
                'tokens_used'     => $result['tokens_used']  ?? 0,
                'response_time_ms'=> min($ms, 65535), // SMALLINT cap
                'context'         => $context,
                'error_message'   => $errorMsg,
                'user_id'         => $userId,
                'created_at'      => now(),
            ]);
        } catch (\Exception $e) {
            // Never let logging break the actual response
            Log::warning('AiProviderLogService failed to write: ' . $e->getMessage());
        }
    }

    /**
     * Record a provider that was SKIPPED (not configured / no API key).
     * Useful for showing "not configured" vs "failed" in the dashboard.
     */
    public static function recordSkipped(
        string $provider,
        string $context = 'chat',
        ?int   $userId  = null
    ): void {
        try {
            DB::connection('ai')->table('ai_provider_logs')->insert([
                'provider'         => $provider,
                'model'            => null,
                'success'          => 0,
                'tokens_used'      => 0,
                'response_time_ms' => 0,
                'context'          => $context,
                'error_message'    => 'Not configured (no API key)',
                'user_id'          => $userId,
                'created_at'       => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('AiProviderLogService recordSkipped failed: ' . $e->getMessage());
        }
    }

    /**
     * Prune logs older than $days days.
     * Call from a scheduled Artisan command weekly.
     */
    public static function prune(int $days = 90): int
    {
        return DB::connection('ai')
            ->table('ai_provider_logs')
            ->where('created_at', '<', now()->subDays($days))
            ->delete();
    }
}
