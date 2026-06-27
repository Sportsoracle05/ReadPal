<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Runs nightly via scheduler.
 * Revokes is_premium + premium_tier for any user whose premium_expires_at has passed.
 *
 * In routes/console.php:
 *   Schedule::command('payments:expire-premium')->daily()->at('00:05');
 */
class ExpirePremiumSubscriptions extends Command
{
    protected $signature   = 'payments:expire-premium';
    protected $description = 'Revoke premium/vip/vvip access for users whose subscription has expired.';

    public function handle(): int
    {
        $expired = User::where('is_premium', true)
                       ->whereNotNull('premium_expires_at')
                       ->where('premium_expires_at', '<=', now())
                       ->get(['id', 'premium_tier', 'premium_expires_at']);

        if ($expired->isEmpty()) {
            $this->info('No expired subscriptions found.');
            return self::SUCCESS;
        }

        // Group by tier for a readable log summary
        $byTier = $expired->groupBy('premium_tier')->map->count();

        User::whereIn('id', $expired->pluck('id'))
            ->update([
                'is_premium'         => false,
                'premium_tier'       => null,
                'premium_expires_at' => null,
            ]);

        Log::info('[Premium] Expired subscriptions revoked', [
            'total'   => $expired->count(),
            'by_tier' => $byTier->toArray(),
        ]);

        $this->info("Revoked {$expired->count()} subscription(s):");
        foreach ($byTier as $tier => $count) {
            $this->line("  {$count} × {$tier}");
        }

        return self::SUCCESS;
    }
}
