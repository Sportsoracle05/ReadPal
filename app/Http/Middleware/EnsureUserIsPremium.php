<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate-keep routes that require an active subscription.
 *
 * Usage:
 *   ->middleware('premium')           // any active tier (premium, vip, vvip)
 *   ->middleware('premium:vip')       // vip or higher
 *   ->middleware('premium:vvip')      // vvip only
 *
 * Register aliases in bootstrap/app.php:
 *   'premium' => EnsureUserIsPremium::class,
 */
class EnsureUserIsPremium
{
    private const TIER_RANKS = [
        'premium' => 1,
        'vip'     => 2,
        'vvip'    => 3,
    ];

    public function handle(Request $request, Closure $next, string $requiredTier = 'premium'): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasActivePremium()) {
            return $this->deny($request, 'premium', $requiredTier);
        }

        $userRank     = self::TIER_RANKS[$user->premium_tier]     ?? 0;
        $requiredRank = self::TIER_RANKS[$requiredTier]            ?? 1;

        if ($userRank < $requiredRank) {
            return $this->deny($request, $user->premium_tier, $requiredTier);
        }

        return $next($request);
    }

    private function deny(Request $request, string $currentTier, string $requiredTier): Response
    {
        if ($request->expectsJson()) {
            $label = ucfirst($requiredTier);
            return response()->json([
                'message' => "{$label} access required.",
            ], 403);
        }

        $message = match ($requiredTier) {
            'vvip'  => 'This feature requires a ReadPal VVIP subscription.',
            'vip'   => 'This feature requires a ReadPal VIP or VVIP subscription.',
            default => 'This feature requires a ReadPal Premium subscription.',
        };

        return redirect()->route('payment.plans')->with('info', $message);
    }
}
