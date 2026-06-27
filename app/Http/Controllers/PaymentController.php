<?php

namespace App\Http\Controllers;

use App\Mail\PaymentSuccessMail;
use App\Models\Payment;
use App\Models\PaymentPlan;
use App\Services\PaystackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class PaymentController extends Controller
{
    // Valid subscription tier keys — must match config('paystack.subscriptions') keys
    private const VALID_TIERS = ['premium', 'vip', 'vvip'];

    public function __construct(private readonly PaystackService $paystack) {}

    // ─── Subscription Initiation (Premium / VIP / VVIP) ──────────────────────

    /**
     * Single entry point for all subscription tiers.
     * Route: POST /payment/subscribe/{tier}
     *
     * @param  string  $tier  'premium' | 'vip' | 'vvip'
     */
    public function initiateSubscription(Request $request, string $tier): RedirectResponse
    {
        // Validate tier param
        if (! in_array($tier, self::VALID_TIERS, true)) {
            abort(404, 'Invalid subscription tier.');
        }

        $this->enforceRateLimit($request, "sub-{$tier}");

        $tierConfig = config("paystack.subscriptions.{$tier}");
        $user       = Auth::user();

        // If already on this exact tier and not expiring within 5 days, skip
        if (
            $user->is_premium
            && $user->premium_tier === $tier
            && $user->premium_expires_at?->gt(now()->addDays(5))
        ) {
            return back()->with('info', "You already have an active {$tierConfig['label']} subscription.");
        }

        $prefix    = strtoupper(substr($tier, 0, 2));
        $reference = $this->paystack->generateReference($prefix);

        $payment = Payment::create([
            'user_id'      => $user->id,
            'reference'    => $reference,
            'amount'       => $tierConfig['amount'],
            'currency'     => 'NGN',
            'payment_type' => Payment::TYPE_PREMIUM,
            'status'       => Payment::STATUS_PENDING,
            'ip_address'   => $request->ip(),
            'user_agent'   => substr($request->userAgent() ?? '', 0, 255),
            'metadata'     => [
                'subscription_tier' => $tier,
                'plan_code'         => $tierConfig['plan_code'],
                'duration_days'     => $tierConfig['duration_days'],
            ],
        ]);

        $result = $this->paystack->initializeTransaction(
            email:       $user->email,
            amount:      $tierConfig['amount'],
            reference:   $reference,
            metadata:    [
                'user_id'           => $user->id,
                'payment_id'        => $payment->id,
                'type'              => 'subscription',
                'subscription_tier' => $tier,
            ],
            callbackUrl: route('payment.callback'),
        );

        if (! $result['status']) {
            $payment->update(['status' => Payment::STATUS_FAILED, 'gateway_response' => $result['message']]);
            return back()->with('error', 'Could not initialize payment. Please try again.');
        }

        return redirect()->away($result['authorization_url']);
    }

    // ─── Custom Plan Payment ──────────────────────────────────────────────────

    public function initiateCustom(Request $request, PaymentPlan $plan): RedirectResponse
    {
        $this->enforceRateLimit($request, 'custom-init');

        if (! $plan->isAvailable()) {
            abort(404, 'This payment plan is not currently available.');
        }

        $user      = Auth::user();
        $reference = $this->paystack->generateReference('CUST');

        $payment = Payment::create([
            'user_id'         => $user->id,
            'payment_plan_id' => $plan->id,
            'reference'       => $reference,
            'amount'          => $plan->amount,
            'currency'        => $plan->currency,
            'payment_type'    => Payment::TYPE_CUSTOM,
            'status'          => Payment::STATUS_PENDING,
            'ip_address'      => $request->ip(),
            'user_agent'      => substr($request->userAgent() ?? '', 0, 255),
            'metadata'        => ['plan_slug' => $plan->slug, 'plan_name' => $plan->name],
        ]);

        $result = $this->paystack->initializeTransaction(
            email:       $user->email,
            amount:      $plan->amount,
            reference:   $reference,
            metadata:    ['user_id' => $user->id, 'payment_id' => $payment->id, 'type' => 'custom', 'plan_id' => $plan->id],
            callbackUrl: route('payment.callback'),
        );

        if (! $result['status']) {
            $payment->update(['status' => Payment::STATUS_FAILED, 'gateway_response' => $result['message']]);
            return back()->with('error', 'Could not initialize payment. Please try again.');
        }

        return redirect()->away($result['authorization_url']);
    }

    // ─── Callback ─────────────────────────────────────────────────────────────

    public function callback(Request $request): RedirectResponse
    {
        $reference = $request->query('reference') ?? $request->query('trxref');

        if (! $reference) {
            return redirect()->route('dashboard')->with('error', 'Invalid payment reference.');
        }

        $payment = Payment::where('reference', $reference)
                          ->where('user_id', Auth::id())
                          ->first();

        if (! $payment) {
            Log::warning('[Payment] Callback: payment not found or user mismatch', [
                'ref'     => $reference,
                'user_id' => Auth::id(),
            ]);
            return redirect()->route('dashboard')->with('error', 'Payment record not found.');
        }

        if ($payment->status === Payment::STATUS_SUCCESS) {
            return redirect()->route('dashboard')->with('success', 'Payment already confirmed!');
        }

        $result = $this->paystack->verifyTransaction($reference);

        if (! $result['status'] || ($result['data']['status'] ?? '') !== 'success') {
            $payment->update([
                'status'           => Payment::STATUS_FAILED,
                'gateway_response' => $result['data']['gateway_response'] ?? $result['message'],
            ]);
            return redirect()->route('dashboard')->with('error', 'Payment was not successful. No charge was made.');
        }

        $data = $result['data'];

        // Server-side amount validation — prevents partial-payment exploits
        if ((int) $data['amount'] !== (int) $payment->amount) {
            Log::critical('[Payment] Amount mismatch — possible tampering!', [
                'ref'      => $reference,
                'expected' => $payment->amount,
                'received' => $data['amount'],
                'user_id'  => Auth::id(),
            ]);
            $payment->update(['status' => Payment::STATUS_FAILED, 'gateway_response' => 'Amount mismatch']);
            return redirect()->route('dashboard')->with('error', 'Payment verification failed. Please contact support.');
        }

        $this->fulfilPayment($payment, $data);

        $tier  = $payment->metadata['subscription_tier'] ?? 'premium';
        $label = config("paystack.subscriptions.{$tier}.label", 'Premium');

        return redirect()->route('dashboard')
            ->with('success', "🎉 {$label} activated! Check your email for confirmation.");
    }

    // ─── Cancel ───────────────────────────────────────────────────────────────

    public function cancel(Request $request): RedirectResponse
    {
        if ($ref = $request->query('reference')) {
            Payment::where('reference', $ref)
                   ->where('user_id', Auth::id())
                   ->where('status', Payment::STATUS_PENDING)
                   ->update(['status' => Payment::STATUS_ABANDONED]);
        }

        return redirect()->route('dashboard')->with('info', 'Payment cancelled.');
    }

    // ─── History ──────────────────────────────────────────────────────────────

    public function history(Request $request): View
    {
        $payments = Payment::with('paymentPlan')
            ->forUser(Auth::id())
            ->whereIn('status', [Payment::STATUS_SUCCESS, Payment::STATUS_FAILED, Payment::STATUS_ABANDONED])
            ->latest()
            ->paginate(15);

        return view('payments.history', compact('payments'));
    }

    // ─── Fulfillment ──────────────────────────────────────────────────────────

    /**
     * Called by both callback() and WebhookController after verified payment.
     * Handles all three subscription tiers + custom plans.
     */
    public function fulfilPayment(Payment $payment, array $paystackData): void
    {
        DB::transaction(function () use ($payment, $paystackData) {
            $authorization = $paystackData['authorization'] ?? [];

            // Determine tier and duration from payment metadata
            $tier         = $payment->metadata['subscription_tier'] ?? 'premium';
            $tierConfig   = config("paystack.subscriptions.{$tier}", config('paystack.subscriptions.premium'));
            $durationDays = (int) ($payment->metadata['duration_days'] ?? $tierConfig['duration_days']);
            $isSubscription = $payment->payment_type === Payment::TYPE_PREMIUM;

            $payment->update([
                'status'             => Payment::STATUS_SUCCESS,
                'gateway_reference'  => $paystackData['reference']  ?? null,
                'gateway_channel'    => $paystackData['channel']     ?? null,
                'gateway_response'   => $paystackData['gateway_response'] ?? null,
                'authorization_code' => $authorization['authorization_code'] ?? null,
                'card_last4'         => $authorization['last4']      ?? null,
                'card_brand'         => $authorization['brand']      ?? null,
                'paid_at'            => now(),
                'expires_at'         => $isSubscription
                                            ? now()->addDays($durationDays)
                                            : null,
            ]);

            $user = $payment->user;

            // ── Subscription access ───────────────────────────────────────────
            if ($isSubscription) {
                $newTierRank     = $tierConfig['tier_rank'];
                $currentTierRank = config("paystack.subscriptions.{$user->premium_tier}.tier_rank", 0);

                // Extend from current expiry if still active; otherwise from now
                $baseDate  = ($user->is_premium && $user->premium_expires_at?->gt(now()))
                    ? $user->premium_expires_at
                    : now();

                $expiresAt = $baseDate->copy()->addDays($durationDays);

                // Always apply the new tier; if paying for a lower tier while
                // already on a higher one, keep the higher tier but still extend.
                $appliedTier = $newTierRank >= $currentTierRank ? $tier : $user->premium_tier;

                $user->update([
                    'is_premium'         => true,
                    'premium_tier'       => $appliedTier,
                    'premium_expires_at' => $expiresAt,
                ]);

                // Update the payment's expires_at to match the actual expiry
                $payment->update(['expires_at' => $expiresAt]);
            }

            // ── Custom plan use counter ───────────────────────────────────────
            if ($payment->paymentPlan) {
                $payment->paymentPlan->increment('uses_count');
            }

            // ── Confirmation email ────────────────────────────────────────────
            try {
                Mail::to($user->email)->queue(new PaymentSuccessMail($payment));
            } catch (\Throwable $e) {
                Log::error('[Payment] Failed to queue confirmation email', [
                    'payment_id' => $payment->id,
                    'error'      => $e->getMessage(),
                ]);
            }
        });
    }

    // ─── Rate Limiter ─────────────────────────────────────────────────────────

    private function enforceRateLimit(Request $request, string $key): void
    {
        $limiterKey = $key . ':' . Auth::id() . ':' . $request->ip();

        if (RateLimiter::tooManyAttempts($limiterKey, 5)) {
            abort(429, 'Too many payment attempts. Please wait before trying again.');
        }

        RateLimiter::hit($limiterKey, 60);
    }
}
