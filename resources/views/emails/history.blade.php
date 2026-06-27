@extends('layouts.app')

@section('title', 'Payment History')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">

  {{-- Header --}}
  <div class="mb-8 flex items-start justify-between">
    <div>
      <h1 class="font-cormorant text-3xl font-semibold text-parchment mb-1">Payment History</h1>
      <p class="text-sm text-forest-muted">All your transactions on ReadPal</p>
    </div>
    {{-- Premium badge --}}
    @if(auth()->user()->is_premium)
    <div class="flex items-center gap-2 bg-amber-500/10 border border-amber-500/40 px-3 py-1.5 rounded-full">
      <span class="text-amber-400 text-sm">🎓</span>
      <span class="text-amber-400 text-xs font-medium">Premium · Expires {{ auth()->user()->premium_expires_at?->format('d M Y') }}</span>
    </div>
    @endif
  </div>

  {{-- Alerts --}}
  @if(session('success'))
    <div class="mb-6 bg-forest-700/30 border border-forest-500/50 text-forest-200 rounded-lg px-4 py-3 text-sm">
      ✓ {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="mb-6 bg-red-900/30 border border-red-500/40 text-red-300 rounded-lg px-4 py-3 text-sm">
      ✗ {{ session('error') }}
    </div>
  @endif

  {{-- Payments Table --}}
  @if($payments->isEmpty())
    <div class="text-center py-16 bg-forest-950 border border-forest-800 rounded-xl">
      <p class="text-5xl mb-4">💳</p>
      <p class="text-parchment font-cormorant text-xl mb-2">No payments yet</p>
      <p class="text-forest-muted text-sm mb-6">Subscribe to premium or make a payment to see your history here.</p>
      <a href="{{ route('payment.premium') }}" class="inline-block bg-forest-600 hover:bg-forest-500 text-white text-sm px-5 py-2 rounded-lg transition">
        Get Premium — ₦500/month
      </a>
    </div>
  @else
    <div class="overflow-hidden border border-forest-800 rounded-xl">
      <table class="w-full text-sm">
        <thead class="bg-forest-900 border-b border-forest-800">
          <tr>
            <th class="text-left px-5 py-3 text-forest-muted font-medium text-xs uppercase tracking-wide">Date</th>
            <th class="text-left px-5 py-3 text-forest-muted font-medium text-xs uppercase tracking-wide">Plan</th>
            <th class="text-left px-5 py-3 text-forest-muted font-medium text-xs uppercase tracking-wide">Reference</th>
            <th class="text-right px-5 py-3 text-forest-muted font-medium text-xs uppercase tracking-wide">Amount</th>
            <th class="text-center px-5 py-3 text-forest-muted font-medium text-xs uppercase tracking-wide">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-forest-900">
          @foreach($payments as $payment)
          <tr class="hover:bg-forest-900/40 transition">
            <td class="px-5 py-4 text-parchment/70 whitespace-nowrap">
              {{ $payment->paid_at?->format('d M Y') ?? $payment->created_at->format('d M Y') }}
            </td>
            <td class="px-5 py-4 text-parchment">
              @if($payment->payment_type === 'premium')
                <span class="text-amber-400">🎓 Premium</span>
              @else
                {{ $payment->paymentPlan?->name ?? '—' }}
              @endif
            </td>
            <td class="px-5 py-4 font-mono text-xs text-forest-muted">
              {{ Str::limit($payment->reference, 28) }}
            </td>
            <td class="px-5 py-4 text-right font-cormorant text-lg text-parchment">
              {{ $payment->amount_in_naira }}
            </td>
            <td class="px-5 py-4 text-center">
              @php
                $statusMap = [
                  'success'   => ['label' => 'Paid',      'class' => 'bg-green-900/40 text-green-400 border-green-700/40'],
                  'pending'   => ['label' => 'Pending',   'class' => 'bg-amber-900/40 text-amber-400 border-amber-700/40'],
                  'failed'    => ['label' => 'Failed',    'class' => 'bg-red-900/40 text-red-400 border-red-700/40'],
                  'abandoned' => ['label' => 'Cancelled', 'class' => 'bg-forest-900 text-forest-muted border-forest-700'],
                ];
                $s = $statusMap[$payment->status] ?? $statusMap['abandoned'];
              @endphp
              <span class="inline-block border text-xs px-2.5 py-0.5 rounded-full {{ $s['class'] }}">
                {{ $s['label'] }}
              </span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-5">
      {{ $payments->links() }}
    </div>
  @endif

</div>
@endsection
