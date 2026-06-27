@extends('layouts.app')

@section('title', 'Payment History')
@section('page_title', 'Payment History')
@section('page_sub', 'Track your transactions and active subscriptions.')

@section('content')
{{-- Breadcrumb --}}
<nav class="flex items-center gap-2 text-xs text-ink-700 mb-5 fade-up">
  <a href="{{ route('dashboard') }}" class="hover:text-ink-400 transition-colors">Dashboard</a>
  <span>›</span>
  <a href="{{ route('payment.plans') }}" class="hover:text-ink-400 transition-colors">Payments</a>
  <span>›</span>
  <span class="text-ink-400">History</span>
</nav>

<div class="max-w-5xl mx-auto py-4 sm:py-6">

  {{-- Header with Premium Badge --}}
  <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4 fade-up">
    <div>
      <h2 class="font-display text-2xl font-bold text-white">Payment History</h2>
      <p class="text-xs text-ink-500 mt-1">A detailed log of your account transactions.</p>
    </div>

    @if(auth()->user()->is_premium)
    <div class="flex items-center gap-3 bg-forest-950/40 border border-forest-800/60 px-4 py-2.5 rounded-xl shadow-lg">
      <span class="text-xl">🎓</span>
      <div class="flex flex-col">
        <span class="text-[10px] font-bold text-forest-500 uppercase tracking-widest leading-none">Premium Active</span>
        <span class="text-[10px] font-mono text-forest-300 mt-1">Until {{ auth()->user()->premium_expires_at?->format('d M Y') }}</span>
      </div>
    </div>
    @endif
  </div>

  {{-- Transactions Table --}}
  @if($payments->isEmpty())
    <div class="app-card py-20 text-center fade-up">
      <div class="w-16 h-16 bg-ink-800/50 border border-ink-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <span class="text-3xl text-ink-600">💳</span>
      </div>
      <h3 class="font-display text-lg font-bold text-white mb-1">No transactions yet</h3>
      <p class="text-sm text-ink-600 mb-8 max-w-xs mx-auto">Your receipts will appear here after a purchase.</p>
      <a href="{{ route('payment.plans') }}" class="app-btn-primary px-6 py-2.5 text-xs">Browse Plans</a>
    </div>
  @else
    <div class="app-card !p-0 overflow-hidden fade-up-d1">
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left border-separate border-spacing-0">
          <thead>
            <tr class="bg-ink-950/30 border-b border-ink-800">
              {{-- Hide Date on ultra-small, show on tablet --}}
              <th class="hidden md:table-cell px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-ink-600">Date</th>
              <th class="px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-ink-600">Plan / Item</th>
              <th class="hidden lg:table-cell px-5 py-4 text-[10px] font-bold uppercase tracking-widest text-ink-600">Reference</th>
              <th class="px-5 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-ink-600">Amount</th>
              <th class="px-5 py-4 text-center text-[10px] font-bold uppercase tracking-widest text-ink-600">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-ink-800/40">
            @foreach($payments as $payment)
            <tr class="hover:bg-ink-800/10 transition-colors group">
              {{-- Date Column (Tablet+) --}}
              <td class="hidden md:table-cell px-5 py-5 whitespace-nowrap text-xs font-mono text-ink-500">
                {{ $payment->paid_at?->format('d M Y') ?? $payment->created_at->format('d M Y') }}
              </td>
              
              {{-- Plan Column (Always Visible) --}}
              <td class="px-5 py-5">
                <div class="flex flex-col">
                  @if($payment->payment_type === 'premium')
                    <div class="flex items-center gap-1.5">
                      <span class="text-[10px] sm:hidden">🎓</span>
                      <span class="font-bold text-forest-400">Premium</span>
                    </div>
                  @else
                    <span class="font-bold text-ink-100">{{ $payment->paymentPlan?->name ?? 'Custom Plan' }}</span>
                  @endif
                  {{-- Show date under plan name only on mobile --}}
                  <span class="md:hidden text-[9px] font-mono text-ink-700 mt-0.5">
                    {{ $payment->paid_at?->format('d M Y') ?? $payment->created_at->format('d M Y') }}
                  </span>
                </div>
              </td>

              {{-- Reference (Desktop Only) --}}
              <td class="hidden lg:table-cell px-5 py-5">
                <span class="font-mono text-[10px] text-ink-700 tracking-tighter">{{ $payment->reference }}</span>
              </td>

              {{-- Amount (Always Visible) --}}
              <td class="px-5 py-5 text-right">
                <span class="font-mono font-bold text-white whitespace-nowrap">
                    {{ $payment->amount_in_naira }}
                </span>
              </td>

              {{-- Status (Always Visible) --}}
              <td class="px-5 py-5 text-center">
                @php
                  $s = match($payment->status) {
                    'success'   => ['label' => 'Paid', 'class' => 'text-forest-400 bg-forest-950/40 border-forest-800/60'],
                    'pending'   => ['label' => 'Wait', 'class' => 'text-yellow-400 bg-yellow-950/20 border-yellow-800/30'],
                    'failed'    => ['label' => 'Fail', 'class' => 'text-red-400 bg-red-950/20 border-red-800/30'],
                    default     => ['label' => 'Void', 'class' => 'text-ink-600 bg-ink-800/50 border-ink-700/40'],
                  };
                @endphp
                <span class="inline-block border text-[9px] font-bold uppercase tracking-widest px-2 py-0.5 rounded {{ $s['class'] }}">
                  {{ $s['label'] }}
                </span>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    {{-- Styled Pagination --}}
    <div class="mt-8 fade-up-d2">
      {{ $payments->links() }}
    </div>
  @endif
</div>
@endsection
