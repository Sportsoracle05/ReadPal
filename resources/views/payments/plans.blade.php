@extends('layouts.app')

@section('title', 'Payments & Plans')
@section('page_title', 'Payments & Plans')
@section('page_sub', 'Manage your subscriptions and community plans.')

@section('content')
@php
    $user       = auth()->user();
    $activeTier = $user->premium_tier;
    $isActive   = $user->hasActivePremium();
    $expiresAt  = $user->premium_expires_at;
    $tiers      = config('paystack.subscriptions');

    $tierMeta = [
        'premium' => [
            'icon'        => '📗',
            'badge'       => null,
            'card_class'  => 'border-forest-700 hover:border-forest-500',
            'btn_class'   => 'bg-forest-600 hover:bg-forest-500 text-white',
            'price_class' => 'text-parchment',
            'accent'      => 'forest',
        ],
        'vip' => [
            'icon'        => '⭐',
            'badge'       => 'Most Popular',
            'card_class'  => 'border-amber-600/70 hover:border-amber-500 ring-1 ring-amber-600/20',
            'btn_class'   => 'bg-amber-500 hover:bg-amber-400 text-forest-950 font-semibold',
            'price_class' => 'text-amber-400',
            'accent'      => 'amber',
        ],
        'vvip' => [
            'icon'        => '👑',
            'badge'       => 'Best Value',
            'card_class'  => 'border-yellow-500/50 hover:border-yellow-400 ring-1 ring-yellow-500/10',
            'btn_class'   => 'bg-gradient-to-r from-yellow-500 to-amber-500 hover:from-yellow-400 hover:to-amber-400 text-forest-950 font-semibold',
            'price_class' => 'text-yellow-300',
            'accent'      => 'gold',
        ],
    ];
@endphp

<div class="max-w-5xl mx-auto px-4 py-14">

  {{-- Header --}}
  <div class="text-center mb-14">
    <p class="text-xs text-forest-muted uppercase tracking-widest mb-3">ReadPal · Oracle Tech</p>
    <h1 class="font-cormorant text-5xl font-semibold text-parchment mb-4">Choose Your Plan</h1>
    <p class="text-forest-muted text-sm max-w-lg mx-auto leading-relaxed">
      Unlock full access to all study materials, quizzes, and the Karls
      community. Pay once — no auto-renewal, no surprises.
    </p>
  </div>

  {{-- Flash messages --}}
  @if(session('success'))
    <div class="mb-8 bg-forest-700/20 border border-forest-500/40 text-forest-200 rounded-xl px-5 py-4 text-sm flex items-center gap-3">
      <span class="text-green-400">✓</span> {{ session('success') }}
    </div>
  @endif
  @if(session('info'))
    <div class="mb-8 bg-amber-900/20 border border-amber-700/40 text-amber-300 rounded-xl px-5 py-4 text-sm flex items-center gap-3">
      <span>ℹ</span> {{ session('info') }}
    </div>
  @endif

  {{-- Active subscription notice --}}
  @if($isActive)
  @php $activeMeta = $tierMeta[$activeTier] ?? $tierMeta['premium']; @endphp
  <div class="mb-10 flex flex-wrap items-center gap-4 bg-forest-950 border {{ $activeMeta['card_class'] }} rounded-xl px-6 py-4">
    <span class="text-2xl">{{ $activeMeta['icon'] }}</span>
    <div class="flex-1 min-w-0">
      <p class="text-sm font-medium text-parchment">
        {{ $tiers[$activeTier]['label'] }} Active
      </p>
      <p class="text-xs text-forest-muted mt-0.5">
        Access expires <strong class="{{ $activeMeta['price_class'] }}">{{ $expiresAt?->format('d M Y') }}</strong>
        ({{ $expiresAt?->diffForHumans() }})
      </p>
    </div>
    <a href="{{ route('payment.history') }}" class="text-xs text-forest-muted hover:text-parchment transition underline">
      View history
    </a>
  </div>
  @endif

  {{-- Pricing cards --}}
  <div class="grid md:grid-cols-3 gap-6 mb-12">
    @foreach($tiers as $tierKey => $tier)
    @php
      $meta        = $tierMeta[$tierKey];
      $isThisTier  = $activeTier === $tierKey && $isActive;
      $naira       = number_format($tier['amount'] / 100, 0);
      $tierRank    = $tier['tier_rank'];
      $activeRank  = config("paystack.subscriptions.{$activeTier}.tier_rank", 0);
      $isUpgrade   = $tierRank > $activeRank;
      $isCurrent   = $isThisTier;
      $isDowngrade  = $isActive && $tierRank < $activeRank;
    @endphp

    <div class="relative bg-forest-950 border {{ $meta['card_class'] }} rounded-2xl overflow-hidden flex flex-col transition duration-200
        {{ $isCurrent ? 'opacity-80' : '' }}">

      {{-- Top accent line --}}
      @if($tierKey === 'vip')
        <div class="h-0.5 bg-gradient-to-r from-transparent via-amber-500 to-transparent"></div>
      @elseif($tierKey === 'vvip')
        <div class="h-0.5 bg-gradient-to-r from-transparent via-yellow-400 to-transparent"></div>
      @else
        <div class="h-0.5 bg-forest-800"></div>
      @endif

      {{-- Badge --}}
      @if($meta['badge'])
      <div class="absolute top-5 right-5">
        <span class="text-xs {{ $tierKey === 'vvip' ? 'bg-yellow-900/40 border border-yellow-600/50 text-yellow-300' : 'bg-amber-900/40 border border-amber-600/50 text-amber-400' }} px-2.5 py-0.5 rounded-full">
          {{ $meta['badge'] }}
        </span>
      </div>
      @endif

      {{-- Active badge --}}
      @if($isCurrent)
      <div class="absolute top-5 right-5">
        <span class="text-xs bg-green-900/40 border border-green-700/40 text-green-400 px-2.5 py-0.5 rounded-full">
          ✓ Active
        </span>
      </div>
      @endif

      <div class="p-7 flex flex-col flex-1">

        {{-- Icon + name --}}
        <div class="mb-5">
          <div class="text-3xl mb-3">{{ $meta['icon'] }}</div>
          <h2 class="font-cormorant text-2xl font-semibold text-parchment">
            {{ $tier['label'] }}
          </h2>
          <p class="text-xs text-forest-muted mt-1">{{ $tier['description'] }}</p>
        </div>

        {{-- Price --}}
        <div class="mb-6">
          <div class="flex items-baseline gap-1">
            <span class="font-cormorant text-4xl font-semibold {{ $meta['price_class'] }}">₦{{ $naira }}</span>
          </div>
          <p class="text-xs text-forest-muted mt-1">
            {{ $tier['duration_days'] }} days ·
            ₦{{ number_format(($tier['amount'] / 100) / ($tier['duration_days'] / 30), 0) }}/month equivalent
          </p>
        </div>

        {{-- Perks --}}
        <ul class="space-y-2.5 mb-8 flex-1">
          @foreach($tier['perks'] as $perk)
          <li class="flex items-start gap-2.5">
            <svg class="w-3.5 h-3.5 {{ $meta['price_class'] }} flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            <span class="text-xs text-parchment/75 leading-relaxed">{{ $perk }}</span>
          </li>
          @endforeach
        </ul>

        {{-- CTA --}}
        @if($isCurrent)
          <button disabled
            class="w-full bg-forest-900 text-forest-600 border border-forest-800 text-sm py-3 rounded-xl cursor-not-allowed">
            Current Plan
          </button>
          <p class="text-center text-xs text-forest-700 mt-2">
            Renew after {{ $expiresAt?->format('d M') }} or extend now
          </p>
          {{-- Still let them extend if they want --}}
          <form method="POST" action="{{ route('payment.subscribe', $tierKey) }}" class="mt-2">
            @csrf
            <button type="submit"
              class="w-full border border-forest-700 hover:border-forest-500 text-forest-400 hover:text-parchment text-xs py-2 rounded-lg transition">
              Extend by {{ $tier['duration_days'] }} days
            </button>
          </form>
        @elseif($isDowngrade)
          <form method="POST" action="{{ route('payment.subscribe', $tierKey) }}">
            @csrf
            <button type="submit"
              class="w-full border border-forest-800 hover:border-forest-600 text-forest-500 hover:text-parchment text-sm py-3 rounded-xl transition">
              Switch to {{ $tier['label'] }}
            </button>
          </form>
          <p class="text-center text-xs text-forest-700 mt-2">
            Lower tier — current access preserved
          </p>
        @else
          <form method="POST" action="{{ route('payment.subscribe', $tierKey) }}">
            @csrf
            <button type="submit"
              class="w-full {{ $meta['btn_class'] }} text-sm py-3.5 rounded-xl transition duration-150 flex items-center justify-center gap-2">
              @if($isActive && $isUpgrade)
                Upgrade to {{ $tier['label'] }} →
              @else
                Get {{ $tier['label'] }} →
              @endif
            </button>
          </form>
        @endif

      </div>
    </div>
    @endforeach
  </div>

  {{-- Comparison note --}}
  <div class="mb-10 overflow-x-auto">
    <table class="w-full text-xs border-collapse">
      <thead>
        <tr class="border-b border-forest-800">
          <th class="text-left text-forest-muted py-3 pr-4 font-medium">Feature</th>
          @foreach($tiers as $tierKey => $tier)
          <th class="text-center py-3 px-3 font-medium {{ $tierMeta[$tierKey]['price_class'] }}">
            {{ $tierMeta[$tierKey]['icon'] }} {{ $tier['label'] }}
          </th>
          @endforeach
        </tr>
      </thead>
      <tbody class="divide-y divide-forest-900">
        @php
          $comparisonRows = [
            ['Notes & PDFs',            true,  true,  true],
            ['Quiz attempts',           true,  true,  true],
            ['Karls community',         true,  true,  true],
            ['CGPA calculator',         true,  true,  true],
            ['Push notifications',      true,  true,  true],
            ['Priority support',        false, true,  true],
            ['Early material access',   false, true,  true],
            ['VIP badge on Karls',      false, true,  true],
            ['VVIP badge on Karls',     false, false, true],
            ['Exclusive VVIP materials',false, false, true],
            ['Exam prep priority',      false, false, true],
            ['Full semester coverage',  false, false, true],
          ];
        @endphp
        @foreach($comparisonRows as [$feature, $hasPremium, $hasVip, $hasVvip])
        <tr class="hover:bg-forest-900/20">
          <td class="py-2.5 pr-4 text-parchment/60">{{ $feature }}</td>
          <td class="py-2.5 px-3 text-center">
            @if($hasPremium)
              <span class="text-forest-500">✓</span>
            @else
              <span class="text-forest-800">—</span>
            @endif
          </td>
          <td class="py-2.5 px-3 text-center">
            @if($hasVip)
              <span class="text-amber-500">✓</span>
            @else
              <span class="text-forest-800">—</span>
            @endif
          </td>
          <td class="py-2.5 px-3 text-center">
            @if($hasVvip)
              <span class="text-yellow-400">✓</span>
            @else
              <span class="text-forest-800">—</span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Custom plans --}}
  @if($plans->isNotEmpty())
  <div class="mb-10">
    <h2 class="text-xs text-forest-muted uppercase tracking-widest mb-5">Other Payments</h2>
    <div class="grid sm:grid-cols-2 gap-4">
      @foreach($plans as $plan)
      <div class="bg-forest-950 border border-forest-800 hover:border-forest-700 rounded-xl p-5 flex items-center justify-between gap-4 transition">
        <div>
          <div class="flex items-center gap-2 mb-1">
            <span class="text-xs border border-forest-700 text-forest-500 px-2 py-0.5 rounded">{{ ucfirst($plan->category) }}</span>
            @if($plan->available_until?->diffInDays(now()) <= 3)
              <span class="text-xs text-amber-500/70">Closes soon</span>
            @endif
          </div>
          <p class="text-sm font-medium text-parchment">{{ $plan->name }}</p>
          @if($plan->description)
            <p class="text-xs text-forest-muted mt-0.5">{{ Str::limit($plan->description, 55) }}</p>
          @endif
        </div>
        <div class="text-right flex-shrink-0">
          <div class="font-cormorant text-xl text-parchment mb-2">{{ $plan->amount_in_naira }}</div>
          <form method="POST" action="{{ route('payment.plan.initiate', $plan->slug) }}">
            @csrf
            <button type="submit"
              class="text-xs bg-forest-800 hover:bg-forest-700 border border-forest-700 text-parchment px-3 py-1.5 rounded-lg transition">
              Pay →
            </button>
          </form>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- Footer note --}}
  <div class="text-center space-y-2">
    <p class="text-xs text-forest-700">
      Secured by Paystack · Card, Bank Transfer, USSD &amp; more supported
    </p>
    <a href="{{ route('payment.history') }}"
       class="inline-block text-xs text-forest-600 hover:text-parchment transition underline underline-offset-4">
      View your payment history →
    </a>
  </div>

</div>
@endsection
