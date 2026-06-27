<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePaymentPlanRequest;
use App\Http\Requests\Admin\UpdatePaymentPlanRequest;
use App\Models\PaymentPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentPlanController extends Controller
{
    public function index(): View
    {
        $plans = PaymentPlan::withCount('payments')
                            ->withTrashed()
                            ->latest()
                            ->paginate(20);

        return view('admin.payment-plans.index', compact('plans'));
    }

    public function create(): View
    {
        return view('admin.payment-plans.create');
    }

    public function store(StorePaymentPlanRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Convert Naira to kobo for storage
        $data['amount']     = (int) round($data['amount_naira'] * 100);
        $data['created_by'] = Auth::id();

        unset($data['amount_naira']);

        PaymentPlan::create($data);

        return redirect()
            ->route('admin.payment-plans.index')
            ->with('success', 'Payment plan created successfully.');
    }

    public function edit(PaymentPlan $paymentPlan): View
    {
        return view('admin.payment-plans.edit', ['plan' => $paymentPlan]);
    }

    public function update(UpdatePaymentPlanRequest $request, PaymentPlan $paymentPlan): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['amount_naira'])) {
            $data['amount'] = (int) round($data['amount_naira'] * 100);
            unset($data['amount_naira']);
        }

        $paymentPlan->update($data);

        return redirect()
            ->route('admin.payment-plans.index')
            ->with('success', 'Payment plan updated.');
    }

    public function destroy(PaymentPlan $paymentPlan): RedirectResponse
    {
        $paymentPlan->delete(); // Soft delete

        return redirect()
            ->route('admin.payment-plans.index')
            ->with('success', 'Payment plan archived.');
    }

    public function restore(int $id): RedirectResponse
    {
        PaymentPlan::withTrashed()->findOrFail($id)->restore();

        return redirect()
            ->route('admin.payment-plans.index')
            ->with('success', 'Payment plan restored.');
    }

    public function toggle(PaymentPlan $paymentPlan): RedirectResponse
    {
        $paymentPlan->update(['is_active' => ! $paymentPlan->is_active]);

        $status = $paymentPlan->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Plan {$status} successfully.");
    }
}
