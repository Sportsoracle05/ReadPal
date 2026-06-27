<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin ?? false;
    }

    /**
     * Normalise HTML checkbox booleans before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active'    => $this->boolean('is_active'),
            'is_recurring' => $this->boolean('is_recurring'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name'            => ['required', 'string', 'max:120'],
            'description'     => ['nullable', 'string', 'max:500'],
            'amount_naira'    => ['required', 'numeric', 'min:1', 'max:10000000'],
            'category'        => ['required', Rule::in(['custom','event','levy','dues','premium'])],
            'is_active'       => ['boolean'],
            'is_recurring'    => ['boolean'],
            'max_uses'        => ['nullable', 'integer', 'min:1', 'max:999999'],
            'available_from'  => ['nullable', 'date'],
            'available_until' => ['nullable', 'date', 'after_or_equal:available_from'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount_naira.required'          => 'Please enter an amount.',
            'amount_naira.min'               => 'Amount must be at least ₦1.',
            'amount_naira.max'               => 'Amount cannot exceed ₦10,000,000.',
            'category.in'                    => 'Category must be: Custom, Event, Levy, or Dues.',
            'available_until.after_or_equal' => 'End date must be the same as or after the start date.',
        ];
    }
}
