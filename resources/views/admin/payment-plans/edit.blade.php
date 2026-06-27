@extends('layouts.admin')

@section('title', 'Edit Plan: ' . $plan->name)

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">

  <a href="{{ route('admin.payment-plans.index') }}"
     class="inline-flex items-center gap-2 text-sm text-forest-muted hover:text-parchment transition mb-8">
    ← Back to Plans
  </a>

  <div class="flex items-start justify-between mb-8">
    <div>
      <h1 class="font-cormorant text-3xl font-semibold text-parchment">Edit Plan</h1>
      <p class="text-sm text-forest-muted mt-1">{{ $plan->name }}</p>
    </div>
    <div class="text-right">
      <div class="text-xs text-forest-muted">{{ $plan->payments()->count() }} payments</div>
      <div class="text-sm text-amber-400 font-medium">₦{{ number_format($plan->total_collected / 100, 2) }} collected</div>
    </div>
  </div>

  @if($errors->any())
    <div class="mb-6 bg-red-900/20 border border-red-700/40 text-red-300 rounded-lg px-4 py-3 text-sm space-y-1">
      @foreach($errors->all() as $error)
        <p>✗ {{ $error }}</p>
      @endforeach
    </div>
  @endif

  <form method="POST"
        action="{{ route('admin.payment-plans.update', $plan) }}"
        class="space-y-6">
    @csrf
    @method('PUT')

    {{-- Name --}}
    <div>
      <label class="block text-xs text-forest-muted uppercase tracking-wide mb-2">Plan Name *</label>
      <input type="text" name="name" required
             value="{{ old('name', $plan->name) }}"
             placeholder="e.g. Level Dues 300L 2nd Semester"
             class="w-full bg-forest-950 border border-forest-800 focus:border-forest-600 text-parchment
                    rounded-lg px-4 py-3 text-sm outline-none transition placeholder-forest-700">
      @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Description --}}
    <div>
      <label class="block text-xs text-forest-muted uppercase tracking-wide mb-2">Description</label>
      <textarea name="description" rows="3"
                placeholder="Optional — shown to students before payment"
                class="w-full bg-forest-950 border border-forest-800 focus:border-forest-600 text-parchment
                       rounded-lg px-4 py-3 text-sm outline-none transition placeholder-forest-700 resize-none">{{ old('description', $plan->description) }}</textarea>
    </div>

    {{-- Amount + Category --}}
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-xs text-forest-muted uppercase tracking-wide mb-2">Amount (₦) *</label>
        <div class="relative">
          <span class="absolute left-3 top-1/2 -translate-y-1/2 text-forest-500 text-sm">₦</span>
          <input type="number" name="amount_naira" required min="1" step="0.01"
                 value="{{ old('amount_naira', $plan->amount / 100) }}"
                 class="w-full bg-forest-950 border border-forest-800 focus:border-forest-600 text-parchment
                        rounded-lg pl-7 pr-4 py-3 text-sm outline-none transition">
        </div>
        @error('amount_naira')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        @if($plan->payments()->where('status','success')->exists())
          <p class="text-amber-500/70 text-xs mt-1">
            ⚠ This plan has successful payments. Changing the amount won't affect past transactions.
          </p>
        @endif
      </div>

      <div>
        <label class="block text-xs text-forest-muted uppercase tracking-wide mb-2">Category *</label>
        <select name="category"
                class="w-full bg-forest-950 border border-forest-800 focus:border-forest-600 text-parchment
                       rounded-lg px-4 py-3 text-sm outline-none transition">
          @foreach(['custom','event','levy','dues'] as $cat)
          <option value="{{ $cat }}" {{ old('category', $plan->category) === $cat ? 'selected' : '' }}>
            {{ ucfirst($cat) }}
          </option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- Max Uses --}}
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-xs text-forest-muted uppercase tracking-wide mb-2">
          Max Uses
          <span class="normal-case text-forest-600">(leave blank = unlimited)</span>
        </label>
        <input type="number" name="max_uses" min="{{ $plan->uses_count }}"
               value="{{ old('max_uses', $plan->max_uses) }}"
               placeholder="∞"
               class="w-full bg-forest-950 border border-forest-800 focus:border-forest-600 text-parchment
                      rounded-lg px-4 py-3 text-sm outline-none transition placeholder-forest-700">
        @if($plan->uses_count > 0)
          <p class="text-forest-600 text-xs mt-1">{{ $plan->uses_count }} uses recorded — minimum is {{ $plan->uses_count }}</p>
        @endif
        @error('max_uses')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    {{-- Date Range --}}
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-xs text-forest-muted uppercase tracking-wide mb-2">Available From</label>
        <input type="datetime-local" name="available_from"
               value="{{ old('available_from', $plan->available_from?->format('Y-m-d\TH:i')) }}"
               class="w-full bg-forest-950 border border-forest-800 focus:border-forest-600 text-parchment
                      rounded-lg px-4 py-3 text-sm outline-none transition">
        @error('available_from')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="block text-xs text-forest-muted uppercase tracking-wide mb-2">Available Until</label>
        <input type="datetime-local" name="available_until"
               value="{{ old('available_until', $plan->available_until?->format('Y-m-d\TH:i')) }}"
               class="w-full bg-forest-950 border border-forest-800 focus:border-forest-600 text-parchment
                      rounded-lg px-4 py-3 text-sm outline-none transition">
        @error('available_until')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    {{-- Toggles --}}
    <div class="flex items-center gap-8 pt-2">
      @foreach([
        ['is_active',    'Active (visible to users)'],
        ['is_recurring', 'Recurring payment'],
      ] as [$field, $fieldLabel])
      <label class="flex items-center gap-3 cursor-pointer select-none">
        <div class="relative">
          <input type="checkbox" name="{{ $field }}" value="1" class="sr-only peer"
                 {{ old($field, $plan->$field) ? 'checked' : '' }}>
          <div class="w-10 h-5 bg-forest-800 peer-checked:bg-forest-600 rounded-full transition-colors"></div>
          <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full transition-transform peer-checked:translate-x-5 shadow-sm"></div>
        </div>
        <span class="text-sm text-parchment/75">{{ $fieldLabel }}</span>
      </label>
      @endforeach
    </div>

    {{-- Slug display (read-only) --}}
    <div class="bg-forest-900/40 border border-forest-800 rounded-lg px-4 py-3">
      <div class="text-xs text-forest-muted uppercase tracking-wide mb-1">Payment Link (auto-generated · cannot be changed)</div>
      <code class="text-xs text-forest-400 break-all">{{ route('payment.plan.initiate', $plan->slug) }}</code>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-4 pt-2 border-t border-forest-900">
      <button type="submit"
              class="bg-forest-600 hover:bg-forest-500 active:bg-forest-700 text-white px-6 py-3 rounded-lg text-sm font-medium transition">
        Save Changes
      </button>
      <a href="{{ route('admin.payment-plans.index') }}"
         class="text-sm text-forest-muted hover:text-parchment transition">
        Cancel
      </a>

      {{-- Danger zone --}}
      <div class="ml-auto">
        <form method="POST" action="{{ route('admin.payment-plans.destroy', $plan) }}"
              onsubmit="return confirm('Archive this plan? All payment records are preserved and it can be restored later.')">
          @csrf
          @method('DELETE')
          <button type="submit"
                  class="text-xs text-red-500/60 hover:text-red-400 transition border border-red-900/40 hover:border-red-700/50 px-3 py-2 rounded-lg">
            Archive Plan
          </button>
        </form>
      </div>
    </div>

  </form>
</div>
@endsection
