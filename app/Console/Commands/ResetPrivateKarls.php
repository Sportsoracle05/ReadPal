<?php

namespace App\Console\Commands;

use App\Models\PrivateKarl;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResetPrivateKarls extends Command
{
    /**
     * The name and signature of the console command.
     * Run via: php artisan karls:reset
     * Schedule in app/Console/Kernel.php:
     *   $schedule->command('karls:reset')->dailyAt('00:00');
     */
    protected $signature   = 'karls:reset';
    protected $description = 'Daily reset: delete all viewed private karls and any older than 24 hours.';

    public function handle(): int
    {
        $this->info('[ReadPal Karls] Starting daily private karls reset…');

        // ── 1. Delete all karls that have been viewed ────────────
        $viewedCount = PrivateKarl::whereNotNull('viewed_at')->count();
        PrivateKarl::whereNotNull('viewed_at')->delete();
        $this->line("  → Deleted {$viewedCount} viewed private karls.");

        // ── 2. Delete unread karls older than 24 hours (safety net)
        $staleCount = PrivateKarl::whereNull('viewed_at')
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->count();

        PrivateKarl::whereNull('viewed_at')
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->delete();

        $this->line("  → Deleted {$staleCount} stale unread private karls (>24h old).");

        $total = $viewedCount + $staleCount;
        $this->info("[ReadPal Karls] Reset complete. {$total} records purged.");

        Log::info('karls:reset completed', [
            'viewed_deleted' => $viewedCount,
            'stale_deleted'  => $staleCount,
            'total'          => $total,
            'run_at'         => now()->toDateTimeString(),
        ]);

        return self::SUCCESS;
    }
}