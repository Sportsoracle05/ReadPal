@extends('layouts.admin')

@section('title', 'Payment Monitor')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

  {{-- Header --}}
  <div class="mb-8 flex items-center justify-between">
    <div>
      <h1 class="font-cormorant text-3xl font-semibold text-parchment">Payment Monitor</h1>
      <p class="text-sm text-forest-muted mt-1">Track all transactions and revenue across ReadPal</p>
    </div>
    <a href="{{ route('admin.payments.export', request()->only(['from','to','status','type'])) }}"
       class="flex items-center gap-2 border border-forest-700 hover:border-amber-600 text-forest-300 hover:text-amber-400 text-sm px-4 py-2 rounded-lg transition">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
      </svg>
      Export CSV
    </a>
  </div>

  {{-- Stats Cards --}}
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    @php
      $cards = [
        ['label' => 'Total Revenue',    'value' => '₦'.number_format($stats['total_revenue']/100,2),    'sub' => $stats['total_count'].' payments',          'accent' => 'amber'],
        ['label' => 'Premium Revenue',  'value' => '₦'.number_format($stats['premium_revenue']/100,2),  'sub' => $stats['premium_users'].' active members',  'accent' => 'forest'],
        ['label' => 'This Month',       'value' => '₦'.number_format($stats['this_month']/100,2),       'sub' => 'Current month',                            'accent' => 'forest'],
        ['label' => 'Today',            'value' => '₦'.number_format($stats['today_revenue']/100,2),    'sub' => now()->format('d M Y'),                     'accent' => 'amber'],
      ];
    @endphp
    @foreach($cards as $card)
    <div class="bg-forest-950 border border-forest-800 rounded-xl p-5">
      <div class="text-xs text-forest-muted uppercase tracking-wide mb-2">{{ $card['label'] }}</div>
      <div class="font-cormorant text-2xl font-semibold {{ $card['accent'] === 'amber' ? 'text-amber-400' : 'text-parchment' }} mb-1">
        {{ $card['value'] }}
      </div>
      <div class="text-xs text-forest-muted">{{ $card['sub'] }}</div>
    </div>
    @endforeach
  </div>

  {{-- Plan Breakdown --}}
  @if($planBreakdown->isNotEmpty())
  <div class="mb-8 bg-forest-950 border border-forest-800 rounded-xl p-6">
    <h2 class="font-cormorant text-xl text-parchment mb-4">Revenue by Plan</h2>
    <div class="space-y-3">
      @foreach($planBreakdown as $row)
      @php $pct = $stats['custom_revenue'] > 0 ? round($row->total / $stats['custom_revenue'] * 100) : 0; @endphp
      <div class="flex items-center gap-4">
        <div class="text-sm text-parchment/80 w-40 truncate">{{ $row->name }}</div>
        <div class="flex-1 bg-forest-900 rounded-full h-1.5">
          <div class="bg-forest-500 h-1.5 rounded-full" style="width:{{ min($pct,100) }}%"></div>
        </div>
        <div class="text-xs text-forest-muted w-16 text-right">{{ $row->count }}×</div>
        <div class="text-sm text-amber-400 w-24 text-right">₦{{ number_format($row->total/100,2) }}</div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- Filters --}}
  <form method="GET" class="mb-6 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search name, email, reference…"
           class="bg-forest-950 border border-forest-800 text-parchment placeholder-forest-700 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-forest-600 flex-1 min-w-48">

    <select name="status" class="bg-forest-950 border border-forest-800 text-parchment rounded-lg px-3 py-2 text-sm focus:outline-none">
      <option value="">All Statuses</option>
      @foreach(['success','pending','failed','abandoned'] as $s)
        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
      @endforeach
    </select>

    <select name="type" class="bg-forest-950 border border-forest-800 text-parchment rounded-lg px-3 py-2 text-sm focus:outline-none">
      <option value="">All Types</option>
      <option value="premium" {{ request('type')==='premium'?'selected':'' }}>Premium</option>
      <option value="custom"  {{ request('type')==='custom'?'selected':'' }}>Custom</option>
    </select>

    <input type="date" name="from" value="{{ request('from') }}"
           class="bg-forest-950 border border-forest-800 text-parchment rounded-lg px-3 py-2 text-sm focus:outline-none">
    <input type="date" name="to" value="{{ request('to') }}"
           class="bg-forest-950 border border-forest-800 text-parchment rounded-lg px-3 py-2 text-sm focus:outline-none">

    <button type="submit" class="bg-forest-700 hover:bg-forest-600 text-white px-4 py-2 rounded-lg text-sm transition">Filter</button>
    <a href="{{ route('admin.payments.index') }}" class="border border-forest-800 hover:border-forest-600 text-forest-400 px-4 py-2 rounded-lg text-sm transition">Clear</a>
  </form>

  {{-- Payments Table --}}
  <div class="overflow-hidden border border-forest-800 rounded-xl">
    <table class="w-full text-sm">
      <thead class="bg-forest-900/80 border-b border-forest-800">
        <tr>
          <th class="text-left px-4 py-3 text-forest-muted text-xs uppercase tracking-wide">Date</th>
          <th class="text-left px-4 py-3 text-forest-muted text-xs uppercase tracking-wide">User</th>
          <th class="text-left px-4 py-3 text-forest-muted text-xs uppercase tracking-wide">Plan / Type</th>
          <th class="text-left px-4 py-3 text-forest-muted text-xs uppercase tracking-wide">Reference</th>
          <th class="text-right px-4 py-3 text-forest-muted text-xs uppercase tracking-wide">Amount</th>
          <th class="text-center px-4 py-3 text-forest-muted text-xs uppercase tracking-wide">Channel</th>
          <th class="text-center px-4 py-3 text-forest-muted text-xs uppercase tracking-wide">Status</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-forest-900">
        @forelse($payments as $payment)
        <tr class="hover:bg-forest-900/30 transition">
          <td class="px-4 py-3 text-parchment/60 whitespace-nowrap text-xs">
            {{ ($payment->paid_at ?? $payment->created_at)->format('d M · H:i') }}
          </td>
          <td class="px-4 py-3">
            <div class="text-parchment text-sm">{{ $payment->user->name ?? '—' }}</div>
            <div class="text-forest-muted text-xs">{{ $payment->user->email ?? '' }}</div>
          </td>
          <td class="px-4 py-3">
            @if($payment->payment_type === 'premium')
              <span class="text-amber-400 text-xs">🎓 Premium</span>
            @else
              <span class="text-parchment/80 text-xs">{{ $payment->paymentPlan?->name ?? 'Custom' }}</span>
            @endif
          </td>
          <td class="px-4 py-3 font-mono text-xs text-forest-muted">
            {{ Str::limit($payment->reference, 22) }}
          </td>
          <td class="px-4 py-3 text-right font-cormorant text-base
            {{ $payment->status === 'success' ? 'text-amber-400' : 'text-parchment/40' }}">
            {{ $payment->amount_in_naira }}
          </td>
          <td class="px-4 py-3 text-center text-xs text-forest-muted">
            {{ ucfirst($payment->gateway_channel ?? '—') }}
          </td>
          <td class="px-4 py-3 text-center">
            @php
              $sc = ['success'=>'text-green-400 bg-green-900/20 border-green-800/40',
                     'pending'=>'text-amber-400 bg-amber-900/20 border-amber-800/40',
                     'failed'=>'text-red-400 bg-red-900/20 border-red-800/40',
                     'abandoned'=>'text-forest-500 bg-forest-900/20 border-forest-800'];
            @endphp
            <span class="text-xs border px-2 py-0.5 rounded-full {{ $sc[$payment->status] ?? $sc['abandoned'] }}">
              {{ ucfirst($payment->status) }}
            </span>
          </td>
          <td class="px-4 py-3 text-right">
            <a href="{{ route('admin.payments.show', $payment) }}"
               class="text-xs text-forest-muted hover:text-parchment transition">
              View →
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="px-4 py-12 text-center text-forest-muted">
            No payments found matching your filters.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-5">
    {{ $payments->links() }}
  </div>

</div>
@endsection
