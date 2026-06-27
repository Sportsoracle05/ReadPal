<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin ?? false;
    }

    protected function prepareForValidation(): void
    {
        // Only normalise booleans when they are actually present in the request
        if ($this->has('is_active')) {
            $this->merge(['is_active' => $this->boolean('is_active')]);
        }
        if ($this->has('is_recurring')) {
            $this->merge(['is_recurring' => $this->boolean('is_recurring')]);
        }
    }

    public function rules(): array
    {
        $plan = $this->route('paymentPlan');

        return [
            'name'            => ['sometimes', 'string', 'max:120'],
            'description'     => ['nullable', 'string', 'max:500'],
            'amount_naira'    => ['sometimes', 'numeric', 'min:1', 'max:10000000'],
            'category'        => ['sometimes', Rule::in(['custom','event','levy','dues','premium'])],
            'is_active'       => ['sometimes', 'boolean'],
            'is_recurring'    => ['sometimes', 'boolean'],
            // max_uses cannot be set lower than existing use count
            'max_uses'        => [
                'nullable', 'integer',
                'min:' . ($plan?->uses_count ?? 0),
                'max:999999',
            ],
            'available_from'  => ['nullable', 'date'],
            'available_until' => ['nullable', 'date', 'after_or_equal:available_from'],
        ];
    }

    public function messages(): array
    {
        $plan = $this->route('paymentPlan');

        return [
            'amount_naira.min'               => 'Amount must be at least ₦1.',
            'amount_naira.max'               => 'Amount cannot exceed ₦10,000,000.',
            'max_uses.min'                   => 'Max uses cannot be lower than the current use count (' . ($plan?->uses_count ?? 0) . ').',
            'available_until.after_or_equal' => 'End date must be the same as or after the start date.',
        ];
    }
}
