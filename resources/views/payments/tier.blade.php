@extends('layouts.app')

@section('title', 'Get ' . $tierConf['label'])

@section('content')
@php
    $isActive   = $user->hasActivePremium();
    $activeTier = $user->premium_tier;
    $icon       = $tierIcons[$tier] ?? '🎓';

    $styleMap = [
        'premium' => [
            'accent_bar'  => 'from-forest-600 via-forest-500 to-forest-600',
            'price_color' => 'text-parchment',
            'btn_class'   => 'bg-forest-600 hover:bg-forest-500 text-white',
            'check_color' => 'text-forest-500',
            'border'      => 'border-forest-700',
            'badge_class' => 'text-forest-400 bg-forest-900/40 border-forest-700',
        ],
        'vip' => [
            'accent_bar'  => 'from-amber-700 via-amber-500 to-amber-700',
            'price_color' => 'text-amber-400',
            'btn_class'   => 'bg-amber-500 hover:bg-amber-400 text-forest-950 font-semibold',
            'check_color' => 'text-amber-500',
            'border'      => 'border-amber-700/50',
            'badge_class' => 'text-amber-400 bg-amber-900/20 border-amber-700/40',
        ],
        'vvip' => [
            'accent_bar'  => 'from-yellow-600 via-yellow-400 to-yellow-600',
            'price_color' => 'text-yellow-300',
            'btn_class'   => 'bg-gradient-to-r from-yellow-500 to-amber-500 hover:from-yellow-400 hover:to-amber-400 text-forest-950 font-semibold',
            'check_color' => 'text-yellow-400',
            'border'      => 'border-yellow-600/40',
            'badge_class' => 'text-yellow-300 bg-yellow-900/20 border-yellow-600/40',
        ],
    ];

    $s          = $styleMap[$tier];
    $naira      = number_format($tierConf['amount'] / 100, 0);
    $isCurrent  = $isActive && $activeTier === $tier;
    $currentRank = config("paystack.subscriptions.{$activeTier}.tier_rank", 0);
    $thisRank    = $tierConf['tier_rank'];
    $isUpgrade   = $isActive && $thisRank > $currentRank;
@endphp

<div class="min-h-screen flex items-center justify-center px-4 py-16">
  <div class="w-full max-w-md">

    {{-- Card --}}
    <div class="bg-forest-950 border {{ $s['border'] }} rounded-2xl overflow-hidden shadow-2xl">

      {{-- Accent bar --}}
      <div class="h-1 bg-gradient-to-r {{ $s['accent_bar'] }}"></div>

      <div class="p-8">

        {{-- Icon + headline --}}
        <div class="text-center mb-8">
          <div class="text-5xl mb-4">{{ $icon }}</div>
          <h1 class="font-cormorant text-3xl font-semibold text-parchment mb-1">
            ReadPal {{ $tierConf['label'] }}
          </h1>
          <p class="text-sm text-forest-muted">{{ $tierConf['description'] }}</p>
        </div>

        {{-- Perks --}}
        <ul class="space-y-3 mb-8">
          @foreach($tierConf['perks'] as $perk)
          <li class="flex items-start gap-3">
            <svg class="w-4 h-4 {{ $s['check_color'] }} flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
            <span class="text-sm text-parchment/75">{{ $perk }}</span>
          </li>
          @endforeach
        </ul>

        {{-- Pricing --}}
        <div class="bg-forest-900/60 border border-forest-800 rounded-xl p-5 mb-6 text-center">
          <div class="text-xs text-forest-muted uppercase tracking-widest mb-1">
            {{ $tierConf['description'] }}
          </div>
          <div class="font-cormorant text-5xl font-semibold {{ $s['price_color'] }} mb-1">
            ₦{{ $naira }}
          </div>
          <div class="text-xs text-forest-muted">
            {{ $tierConf['duration_days'] }} days ·
            ₦{{ number_format(($tierConf['amount'] / 100) / ($tierConf['duration_days'] / 30), 0) }}/month equivalent
          </div>
        </div>

        {{-- Active status banner --}}
        @if($isCurrent)
        <div class="bg-green-900/20 border border-green-800/40 rounded-xl p-4 mb-6 flex items-start gap-3">
          <span class="text-green-400 flex-shrink-0">✓</span>
          <div>
            <p class="text-green-400 text-sm font-medium">{{ $tierConf['label'] }} is active!</p>
            <p class="text-green-600 text-xs mt-0.5">
              Expires {{ $user->premium_expires_at?->format('d M Y') }}.
              Paying again will extend your access by {{ $tierConf['duration_days'] }} more days.
            </p>
          </div>
        </div>
        @elseif($isActive && ! $isCurrent)
        <div class="bg-amber-900/20 border border-amber-700/30 rounded-xl p-4 mb-6 flex items-start gap-3">
          <span class="text-amber-500 flex-shrink-0">ℹ</span>
          <div>
            <p class="text-amber-400 text-sm font-medium">
              You currently have {{ config("paystack.subscriptions.{$activeTier}.label") }}
            </p>
            <p class="text-amber-600/80 text-xs mt-0.5">
              @if($isUpgrade)
                Upgrading will apply {{ $tierConf['label'] }} access and extend your expiry by {{ $tierConf['duration_days'] }} days.
              @else
                Paying for {{ $tierConf['label'] }} will extend your expiry. Your current {{ config("paystack.subscriptions.{$activeTier}.label") }} access is preserved.
              @endif
            </p>
          </div>
        </div>
        @endif

        {{-- CTA --}}
        <form method="POST" action="{{ route('payment.subscribe', $tier) }}">
          @csrf
          <button type="submit"
            class="w-full {{ $s['btn_class'] }} py-3.5 rounded-xl transition text-sm tracking-wide flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            @if($isCurrent)
              Extend for ₦{{ $naira }}
            @elseif($isUpgrade)
              Upgrade to {{ $tierConf['label'] }} — ₦{{ $naira }}
            @else
              Get {{ $tierConf['label'] }} — ₦{{ $naira }}
            @endif
          </button>
        </form>

        <p class="text-center text-xs text-forest-700 mt-4">
          Secured by Paystack · Card, Bank Transfer, USSD supported
        </p>

      </div>
    </div>

    {{-- Navigation --}}
    <div class="text-center mt-6 flex items-center justify-center gap-6">
      <a href="{{ route('payment.plans') }}"
         class="text-sm text-forest-muted hover:text-parchment transition">
        ← Compare all plans
      </a>
      <a href="{{ route('dashboard') }}"
         class="text-sm text-forest-muted hover:text-parchment transition">
        Back to dashboard
      </a>
    </div>

  </div>
</div>
@endsection
