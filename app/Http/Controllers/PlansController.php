<?php

namespace App\Http\Controllers;

use App\Models\PaymentPlan;
use Illuminate\View\View;

/**
 * Displays the public-facing plans page at /payments/plans.
 * Shows the premium subscription card + all active custom plans.
 *
 * Add to routes/paystack.php inside the auth middleware group:
 *
 *   Route::get('/payments/plans', [PlansController::class, 'index'])->name('payment.plans');
 */
class PlansController extends Controller
{
    public function index(): View
    {
        $plans = PaymentPlan::active()
            ->custom()
            ->where(function ($q) {
                $q->whereNull('available_from')
                  ->orWhere('available_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('available_until')
                  ->orWhere('available_until', '>=', now());
            })
            ->where(function ($q) {
                // Exclude plans that hit their max_uses cap
                $q->whereNull('max_uses')
                  ->orWhereColumn('uses_count', '<', 'max_uses');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('payments.plans', compact('plans'));
    }
}
