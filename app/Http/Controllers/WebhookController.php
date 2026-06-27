<?php

namespace App\Http\Controllers;

use App\Http\Controllers\PaymentController;
use App\Models\Payment;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Handles Paystack webhook push events.
 *
 * Route: POST /paystack/webhook  (excluded from CSRF, verified via HMAC-SHA512)
 *
 * Security checklist:
 *  ✓ HMAC-SHA512 signature verification (VerifyPaystackWebhook middleware)
 *  ✓ IP whitelist check (VerifyPaystackWebhook middleware)
 *  ✓ Idempotency — skips already-processed events
 *  ✓ Amount validation — prevents partial-payment exploits
 *  ✓ DB transaction wrapping
 *  ✓ Structured logging
 */
class WebhookController extends Controller
{
    public function __construct(
        private readonly PaystackService   $paystack,
        private readonly PaymentController $paymentController,
    ) {}

    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $body    = json_decode($payload, true);

        $event     = $body['event'] ?? null;
        $eventData = $body['data']  ?? [];

        Log::info('[Webhook] Received Paystack event', ['event' => $event]);

        match ($event) {
            'charge.success'       => $this->handleChargeSuccess($eventData),
            'transfer.success'     => $this->handleTransferSuccess($eventData),
            'subscription.disable' => $this->handleSubscriptionDisable($eventData),
            default                => Log::info('[Webhook] Unhandled event', ['event' => $event]),
        };

        // Always return 200 quickly so Paystack doesn't retry
        return response('OK', 200);
    }

    // ─── Charge Success ───────────────────────────────────────────────────────

    private function handleChargeSuccess(array $data): void
    {
        $reference = $data['reference'] ?? null;

        if (! $reference) {
            Log::warning('[Webhook] charge.success: missing reference');
            return;
        }

        DB::transaction(function () use ($reference, $data) {
            // Lock the row to prevent race conditions
            $payment = Payment::where('reference', $reference)
                              ->lockForUpdate()
                              ->first();

            if (! $payment) {
                Log::warning('[Webhook] charge.success: payment not found', ['ref' => $reference]);
                return;
            }

            // Idempotency — skip if already handled
            if ($payment->webhook_processed) {
                Log::info('[Webhook] charge.success: already processed, skipping', ['ref' => $reference]);
                return;
            }

            // Skip if already fulfilled (callback may have beaten us)
            if ($payment->status === Payment::STATUS_SUCCESS) {
                $payment->update(['webhook_processed' => true, 'webhook_processed_at' => now()]);
                return;
            }

            // Amount validation (defence against partial-payment exploits)
            if ((int) ($data['amount'] ?? 0) !== (int) $payment->amount) {
                Log::critical('[Webhook] Amount mismatch!', [
                    'ref'      => $reference,
                    'expected' => $payment->amount,
                    'received' => $data['amount'] ?? 0,
                ]);
                return;
            }

            // Verify status from Paystack's own field
            if (($data['status'] ?? '') !== 'success') {
                Log::info('[Webhook] charge.success: status not success', ['status' => $data['status'] ?? null]);
                return;
            }

            // Fulfil the payment
            $this->paymentController->fulfilPayment($payment, $data);

            $payment->update([
                'webhook_processed'    => true,
                'webhook_processed_at' => now(),
            ]);

            Log::info('[Webhook] charge.success: fulfilled', [
                'ref'        => $reference,
                'payment_id' => $payment->id,
                'user_id'    => $payment->user_id,
            ]);
        });
    }

    // ─── Transfer Success ─────────────────────────────────────────────────────

    private function handleTransferSuccess(array $data): void
    {
        // Hook for future payout logic
        Log::info('[Webhook] transfer.success', ['reference' => $data['reference'] ?? null]);
    }

    // ─── Subscription Disable ─────────────────────────────────────────────────

    private function handleSubscriptionDisable(array $data): void
    {
        // If using Paystack recurring subscriptions, revoke premium here
        $email = $data['customer']['email'] ?? null;

        if ($email) {
            \App\Models\User::where('email', $email)->update(['is_premium' => false, 'premium_expires_at' => null]);
            Log::info('[Webhook] subscription.disable: revoked premium', ['email' => $email]);
        }
    }
}
