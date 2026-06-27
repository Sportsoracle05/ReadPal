<?php

use App\Http\Controllers\Admin\PaymentMonitorController;
use App\Http\Controllers\Admin\PaymentPlanController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlansController;
use App\Http\Controllers\WebhookController;
use App\Http\Middleware\VerifyPaystackWebhook;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Paystack Webhook
|--------------------------------------------------------------------------
| NO CSRF — secured via HMAC-SHA512 inside VerifyPaystackWebhook.
| Path must be listed in bootstrap/app.php validateCsrfTokens(except:[...])
| if loaded under the 'web' group.
|--------------------------------------------------------------------------
*/

Route::post('/paystack/webhook', [WebhookController::class, 'handle'])
    ->middleware(VerifyPaystackWebhook::class)
    ->name('paystack.webhook');

/*
|--------------------------------------------------------------------------
| Authenticated User Payment Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // =========================================================================
    // SUBSCRIPTION — PRICING PAGE
    // =========================================================================
    // The main plans page that shows Premium, VIP, and VVIP side by side.
    // Link users here from your dashboard, navbar, etc.
    // Usage: route('payment.plans')
    // =========================================================================

    Route::get('/payments/plans', [PlansController::class, 'index'])
        ->name('payment.plans');

    // =========================================================================
    // SUBSCRIPTION — INDIVIDUAL TIER PAGES (GET)
    // =========================================================================
    // Each tier has a standalone focused page.
    // Usage: route('payment.tier', 'vip')  →  /payment/tier/vip
    // =========================================================================

    Route::get('/payment/tier/{tier}', function (string $tier) {
        $tiers = config('paystack.subscriptions');

        if (! array_key_exists($tier, $tiers)) {
            abort(404);
        }

        $user      = auth()->user();
        $tierConf  = $tiers[$tier];
        $tierIcons = ['premium' => '📗', 'vip' => '⭐', 'vvip' => '👑'];

        return view('payments.tier', compact('tier', 'tierConf', 'user', 'tierIcons'));
    })
    ->where('tier', 'premium|vip|vvip')
    ->name('payment.tier');

    // =========================================================================
    // SUBSCRIPTION — INITIATION (POST)
    // =========================================================================
    // Single entry point for all three tiers.
    // Usage: route('payment.subscribe', 'vvip')  →  POST /payment/subscribe/vvip
    // =========================================================================

    Route::post('/payment/subscribe/{tier}', [PaymentController::class, 'initiateSubscription'])
        ->where('tier', 'premium|vip|vvip')
        ->name('payment.subscribe');

    // =========================================================================
    // BACKWARD COMPATIBILITY ALIASES
    // =========================================================================
    // Kept so that any existing views/emails/links using the old route names
    // (payment.premium and payment.premium.initiate) don't break.
    // Safe to remove once you've updated all references to use payment.subscribe.
    // =========================================================================

    // GET  /payment/premium  →  redirects to the premium tier page
    Route::get('/payment/premium', function () {
        return redirect()->route('payment.tier', 'premium');
    })->name('payment.premium');

    // POST /payment/premium  →  same as POST /payment/subscribe/premium
    Route::post('/payment/premium', [PaymentController::class, 'initiateSubscription'])
        ->defaults('tier', 'premium')
        ->name('payment.premium.initiate');

    // =========================================================================
    // CUSTOM ADMIN-DEFINED PLANS
    // =========================================================================
    // Usage: route('payment.plan.initiate', $plan->slug)
    // =========================================================================

    Route::post('/payment/plan/{plan:slug}', [PaymentController::class, 'initiateCustom'])
        ->name('payment.plan.initiate');

    // =========================================================================
    // SHARED — CALLBACK & CANCEL
    // =========================================================================
    // Paystack redirects back to /payment/callback after every transaction.
    // All three tiers + custom plans share the same callback handler.
    // =========================================================================

    Route::get('/payment/callback', [PaymentController::class, 'callback'])
        ->name('payment.callback');

    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])
        ->name('payment.cancel');

    // =========================================================================
    // PAYMENT HISTORY
    // =========================================================================

    Route::get('/payment/history', [PaymentController::class, 'history'])
        ->name('payment.history');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    // ── Payment Plans CRUD ────────────────────────────────────────────────────
    Route::resource('payment-plans', PaymentPlanController::class)
        ->except(['show']);

    Route::patch('payment-plans/{paymentPlan}/toggle', [PaymentPlanController::class, 'toggle'])
        ->name('payment-plans.toggle');

    Route::patch('payment-plans/{id}/restore', [PaymentPlanController::class, 'restore'])
        ->name('payment-plans.restore');

    // ── Payment Monitor ───────────────────────────────────────────────────────
    // Export must come BEFORE the {payment} show route to avoid
    // Laravel treating 'export' as a payment ID.
    Route::get('payments',           [PaymentMonitorController::class, 'index'])->name('payments.index');
    Route::get('payments/export',    [PaymentMonitorController::class, 'export'])->name('payments.export');
    Route::get('payments/{payment}', [PaymentMonitorController::class, 'show'])->name('payments.show');
});