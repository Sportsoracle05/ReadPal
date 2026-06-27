@extends('layouts.admin')

@section('title', 'Payment · ' . $payment->reference)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">

  {{-- Back --}}
  <a href="{{ route('admin.payments.index') }}"
     class="inline-flex items-center gap-2 text-sm text-forest-muted hover:text-parchment transition mb-8">
    ← Back to Payments
  </a>

  {{-- Header --}}
  <div class="flex items-start justify-between mb-6">
    <div>
      <h1 class="font-cormorant text-3xl font-semibold text-parchment">Transaction Detail</h1>
      <p class="font-mono text-xs text-forest-muted mt-1">{{ $payment->reference }}</p>
    </div>
    @php
      $sc = ['success'=>'text-green-400 bg-green-900/20 border-green-700/30',
             'pending'=>'text-amber-400 bg-amber-900/20 border-amber-700/30',
             'failed'=>'text-red-400 bg-red-900/20 border-red-700/30',
             'abandoned'=>'text-forest-500 bg-forest-900 border-forest-800'];
    @endphp
    <span class="border px-3 py-1 rounded-full text-sm {{ $sc[$payment->status] ?? $sc['abandoned'] }}">
      {{ ucfirst($payment->status) }}
    </span>
  </div>

  {{-- Main Card --}}
  <div class="bg-forest-950 border border-forest-800 rounded-xl overflow-hidden mb-6">

    {{-- Amount Banner --}}
    <div class="bg-forest-900/60 border-b border-forest-800 px-6 py-5 flex items-center justify-between">
      <div>
        <div class="text-xs text-forest-muted uppercase tracking-wide mb-1">Amount</div>
        <div class="font-cormorant text-4xl {{ $payment->status === 'success' ? 'text-amber-400' : 'text-parchment/40' }}">
          {{ $payment->amount_in_naira }}
        </div>
      </div>
      @if($payment->status === 'success')
      <div class="text-right">
        <div class="text-xs text-forest-muted mb-1">Paid At</div>
        <div class="text-parchment text-sm">{{ $payment->paid_at?->format('d M Y, g:i A') }}</div>
      </div>
      @endif
    </div>

    {{-- Details Grid --}}
    <div class="divide-y divide-forest-900">
      @php
        $rows = [
          ['User',            $payment->user->name ?? '—'],
          ['Email',           $payment->user->email ?? '—'],
          ['Payment Type',    ucfirst($payment->payment_type)],
          ['Plan',            $payment->paymentPlan?->name ?? (($payment->payment_type==='premium') ? 'Premium 1 Month' : '—')],
          ['Channel',         ucfirst($payment->gateway_channel ?? '—')],
          ['Gateway Ref',     $payment->gateway_reference ?? '—'],
          ['Gateway Message', $payment->gateway_response ?? '—'],
          ['Authorization',   $payment->authorization_code ?? '—'],
          ['Card',            $payment->card_last4 ? ucfirst($payment->card_brand ?? '').' •••• '.$payment->card_last4 : '—'],
          ['IP Address',      $payment->ip_address ?? '—'],
          ['Webhook',         $payment->webhook_processed ? '✓ Processed at '.$payment->webhook_processed_at?->format('d M g:i A') : 'Not processed'],
          ['Created',         $payment->created_at->format('d M Y, g:i A')],
        ];
      @endphp
      @foreach($rows as [$label, $value])
      <div class="flex items-start gap-4 px-6 py-3">
        <div class="text-xs text-forest-muted w-36 flex-shrink-0 pt-0.5">{{ $label }}</div>
        <div class="text-sm text-parchment/80 flex-1 break-all">{{ $value }}</div>
      </div>
      @endforeach
    </div>

  </div>

  {{-- Metadata --}}
  @if($payment->metadata)
  <div class="bg-forest-950 border border-forest-800 rounded-xl p-5">
    <h3 class="text-xs text-forest-muted uppercase tracking-wide mb-3">Metadata</h3>
    <pre class="font-mono text-xs text-forest-300 overflow-auto">{{ json_encode($payment->metadata, JSON_PRETTY_PRINT) }}</pre>
  </div>
  @endif

</div>
@endsection
