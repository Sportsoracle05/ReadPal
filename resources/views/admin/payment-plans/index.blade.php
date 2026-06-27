@extends('layouts.admin')

@section('title', 'Payment Plans')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">

  {{-- Header --}}
  <div class="mb-8 flex items-center justify-between">
    <div>
      <h1 class="font-cormorant text-3xl font-semibold text-parchment">Payment Plans</h1>
      <p class="text-sm text-forest-muted mt-1">Configure and manage custom payment categories</p>
    </div>
    <a href="{{ route('admin.payment-plans.create') }}"
       class="flex items-center gap-2 bg-forest-600 hover:bg-forest-500 text-white text-sm px-4 py-2 rounded-lg transition">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      New Plan
    </a>
  </div>

  {{-- Alerts --}}
  @if(session('success'))
    <div class="mb-6 bg-forest-700/20 border border-forest-500/40 text-forest-200 rounded-lg px-4 py-3 text-sm">
      ✓ {{ session('success') }}
    </div>
  @endif

  {{-- Plans Grid --}}
  <div class="overflow-hidden border border-forest-800 rounded-xl">
    <table class="w-full text-sm">
      <thead class="bg-forest-900/80 border-b border-forest-800">
        <tr>
          <th class="text-left px-5 py-3 text-forest-muted text-xs uppercase tracking-wide font-medium">Plan</th>
          <th class="text-left px-5 py-3 text-forest-muted text-xs uppercase tracking-wide font-medium">Category</th>
          <th class="text-right px-5 py-3 text-forest-muted text-xs uppercase tracking-wide font-medium">Price</th>
          <th class="text-right px-5 py-3 text-forest-muted text-xs uppercase tracking-wide font-medium">Payments</th>
          <th class="text-right px-5 py-3 text-forest-muted text-xs uppercase tracking-wide font-medium">Revenue</th>
          <th class="text-center px-5 py-3 text-forest-muted text-xs uppercase tracking-wide font-medium">Status</th>
          <th class="px-5 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-forest-900">
        @forelse($plans as $plan)
        <tr class="hover:bg-forest-900/30 transition {{ $plan->trashed() ? 'opacity-40' : '' }}">
          <td class="px-5 py-4">
            <div class="text-parchment font-medium">{{ $plan->name }}</div>
            @if($plan->description)
            <div class="text-forest-muted text-xs mt-0.5">{{ Str::limit($plan->description, 60) }}</div>
            @endif
          </td>
          <td class="px-5 py-4">
            <span class="text-xs border border-forest-700 text-forest-300 px-2 py-0.5 rounded">
              {{ ucfirst($plan->category) }}
            </span>
          </td>
          <td class="px-5 py-4 text-right font-cormorant text-lg text-parchment">
            {{ $plan->amount_in_naira }}
          </td>
          <td class="px-5 py-4 text-right text-parchment/70">
            {{ $plan->payments_count }}
            @if($plan->max_uses)
            <span class="text-forest-muted">/{{ $plan->max_uses }}</span>
            @endif
          </td>
          <td class="px-5 py-4 text-right text-amber-400 font-medium">
            ₦{{ number_format($plan->total_collected / 100, 2) }}
          </td>
          <td class="px-5 py-4 text-center">
            @if($plan->trashed())
              <span class="text-xs text-forest-muted border border-forest-800 px-2 py-0.5 rounded-full">Archived</span>
            @elseif($plan->is_active)
              <span class="text-xs text-green-400 bg-green-900/30 border border-green-800/40 px-2 py-0.5 rounded-full">Active</span>
            @else
              <span class="text-xs text-red-400 bg-red-900/30 border border-red-800/40 px-2 py-0.5 rounded-full">Inactive</span>
            @endif
          </td>
          <td class="px-5 py-4">
            <div class="flex items-center justify-end gap-2">
              @if(! $plan->trashed())
                {{-- Toggle active --}}
                <form method="POST" action="{{ route('admin.payment-plans.toggle', $plan) }}">
                  @csrf
                  @method('PATCH')
                  <button type="submit"
                    class="text-xs text-forest-muted hover:text-amber-400 transition px-2 py-1 rounded border border-forest-800 hover:border-amber-700">
                    {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                  </button>
                </form>
                <a href="{{ route('admin.payment-plans.edit', $plan) }}"
                   class="text-xs text-forest-muted hover:text-parchment transition px-2 py-1 rounded border border-forest-800">
                  Edit
                </a>
                <form method="POST" action="{{ route('admin.payment-plans.destroy', $plan) }}"
                      onsubmit="return confirm('Archive this plan?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit"
                    class="text-xs text-red-500/60 hover:text-red-400 transition px-2 py-1 rounded border border-red-900/40">
                    Archive
                  </button>
                </form>
              @else
                <form method="POST" action="{{ route('admin.payment-plans.restore', $plan->id) }}">
                  @csrf
                  @method('PATCH')
                  <button type="submit" class="text-xs text-forest-muted hover:text-green-400 transition px-2 py-1 rounded border border-forest-800">
                    Restore
                  </button>
                </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="px-5 py-12 text-center text-forest-muted">
            No payment plans yet. Create your first one.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-5">
    {{ $plans->links() }}
  </div>

</div>
@endsection
