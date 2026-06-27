@extends('layouts.admin')

@section('title', isset($plan) ? 'Edit Plan' : 'New Payment Plan')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">

  <a href="{{ route('admin.payment-plans.index') }}"
     class="inline-flex items-center gap-2 text-sm text-forest-muted hover:text-parchment transition mb-8">
    ← Back to Plans
  </a>

  <h1 class="font-cormorant text-3xl font-semibold text-parchment mb-8">
    {{ isset($plan) ? 'Edit Plan: '.$plan->name : 'Create Payment Plan' }}
  </h1>

  <form method="POST"
        action="{{ isset($plan) ? route('admin.payment-plans.update', $plan) : route('admin.payment-plans.store') }}"
        class="space-y-6">
    @csrf
    @if(isset($plan)) @method('PUT') @endif

    {{-- Name --}}
    <div>
      <label class="block text-xs text-forest-muted uppercase tracking-wide mb-2">Plan Name *</label>
      <input type="text" name="name" required
             value="{{ old('name', $plan->name ?? '') }}"
             placeholder="e.g. Level Dues 300L 2nd Semester"
             class="w-full bg-forest-950 border border-forest-800 focus:border-forest-600 text-parchment rounded-lg px-4 py-3 text-sm outline-none transition placeholder-forest-700">
      @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Description --}}
    <div>
      <label class="block text-xs text-forest-muted uppercase tracking-wide mb-2">Description</label>
      <textarea name="description" rows="3"
                placeholder="Optional — shown to students before payment"
                class="w-full bg-forest-950 border border-forest-800 focus:border-forest-600 text-parchment rounded-lg px-4 py-3 text-sm outline-none transition placeholder-forest-700 resize-none">{{ old('description', $plan->description ?? '') }}</textarea>
    </div>

    {{-- Amount + Category --}}
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-xs text-forest-muted uppercase tracking-wide mb-2">Amount (₦) *</label>
        <div class="relative">
          <span class="absolute left-3 top-1/2 -translate-y-1/2 text-forest-500 text-sm">₦</span>
          <input type="number" name="amount_naira" required min="1" step="0.01"
                 value="{{ old('amount_naira', isset($plan) ? $plan->amount / 100 : '') }}"
                 placeholder="500"
                 class="w-full bg-forest-950 border border-forest-800 focus:border-forest-600 text-parchment rounded-lg pl-7 pr-4 py-3 text-sm outline-none transition">
        </div>
        @error('amount_naira')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-xs text-forest-muted uppercase tracking-wide mb-2">Category *</label>
        <select name="category"
                class="w-full bg-forest-950 border border-forest-800 focus:border-forest-600 text-parchment rounded-lg px-4 py-3 text-sm outline-none transition">
          @foreach(['custom','event','levy','dues'] as $cat)
          <option value="{{ $cat }}" {{ old('category', $plan->category ?? 'custom') === $cat ? 'selected' : '' }}>
            {{ ucfirst($cat) }}
          </option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- Max Uses + Availability --}}
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-xs text-forest-muted uppercase tracking-wide mb-2">Max Uses <span class="normal-case">(leave blank = unlimited)</span></label>
        <input type="number" name="max_uses" min="1"
               value="{{ old('max_uses', $plan->max_uses ?? '') }}"
               placeholder="∞"
               class="w-full bg-forest-950 border border-forest-800 focus:border-forest-600 text-parchment rounded-lg px-4 py-3 text-sm outline-none transition placeholder-forest-700">
      </div>
      <div>
        {{-- spacer --}}
      </div>
    </div>

    {{-- Date Range --}}
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-xs text-forest-muted uppercase tracking-wide mb-2">Available From</label>
        <input type="datetime-local" name="available_from"
               value="{{ old('available_from', isset($plan->available_from) ? $plan->available_from->format('Y-m-d\TH:i') : '') }}"
               class="w-full bg-forest-950 border border-forest-800 focus:border-forest-600 text-parchment rounded-lg px-4 py-3 text-sm outline-none transition">
      </div>
      <div>
        <label class="block text-xs text-forest-muted uppercase tracking-wide mb-2">Available Until</label>
        <input type="datetime-local" name="available_until"
               value="{{ old('available_until', isset($plan->available_until) ? $plan->available_until->format('Y-m-d\TH:i') : '') }}"
               class="w-full bg-forest-950 border border-forest-800 focus:border-forest-600 text-parchment rounded-lg px-4 py-3 text-sm outline-none transition">
      </div>
    </div>

    {{-- Toggles --}}
    <div class="flex items-center gap-8">
      @foreach([['is_active', 'Active (visible to users)'], ['is_recurring', 'Recurring payment']] as [$field, $fieldLabel])
      <label class="flex items-center gap-3 cursor-pointer">
        <div class="relative">
          <input type="checkbox" name="{{ $field }}" value="1" class="sr-only peer"
                 {{ old($field, $plan->$field ?? ($field === 'is_active' ? true : false)) ? 'checked' : '' }}>
          <div class="w-10 h-5 bg-forest-800 peer-checked:bg-forest-600 rounded-full transition-colors"></div>
          <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5"></div>
        </div>
        <span class="text-sm text-parchment/75">{{ $fieldLabel }}</span>
      </label>
      @endforeach
    </div>

    {{-- Submit --}}
    <div class="flex items-center gap-4 pt-2">
      <button type="submit"
              class="bg-forest-600 hover:bg-forest-500 text-white px-6 py-3 rounded-lg text-sm font-medium transition">
        {{ isset($plan) ? 'Save Changes' : 'Create Plan' }}
      </button>
      <a href="{{ route('admin.payment-plans.index') }}"
         class="text-sm text-forest-muted hover:text-parchment transition">
        Cancel
      </a>
    </div>

  </form>
</div>
@endsection
