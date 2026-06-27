<?php

namespace App\Http\Middleware;

use App\Services\PaystackService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Verifies incoming Paystack webhook requests via:
 *  1. IP whitelist check (Paystack's known server IPs)
 *  2. HMAC-SHA512 signature validation
 */
class VerifyPaystackWebhook
{
    public function __construct(private readonly PaystackService $paystack) {}

    public function handle(Request $request, Closure $next): Response
    {
        // ── 1. IP Whitelist ───────────────────────────────────────────────────
        $allowedIps = config('paystack.webhook_ip_whitelist', []);

        // Only enforce in production to allow local testing
        if (app()->isProduction() && ! empty($allowedIps)) {
            $clientIp = $request->ip();

            if (! in_array($clientIp, $allowedIps, true)) {
                Log::warning('[Webhook] Blocked: IP not in whitelist', ['ip' => $clientIp]);
                abort(403, 'Forbidden');
            }
        }

        // ── 2. HMAC Signature Verification ───────────────────────────────────
        $signature = $request->header('x-paystack-signature');

        if (! $signature) {
            Log::warning('[Webhook] Blocked: missing signature header', ['ip' => $request->ip()]);
            abort(400, 'Missing signature');
        }

        $payload = $request->getContent();

        if (! $this->paystack->validateWebhookSignature($payload, $signature)) {
            Log::warning('[Webhook] Blocked: invalid HMAC signature', ['ip' => $request->ip()]);
            abort(401, 'Invalid signature');
        }

        // ── 3. Content-type guard ─────────────────────────────────────────────
        if (! str_contains($request->header('Content-Type', ''), 'application/json')) {
            abort(400, 'Invalid content type');
        }

        return $next($request);
    }
}
