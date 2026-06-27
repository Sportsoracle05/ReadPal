{{--
  This view is kept for backward compatibility.
  It now redirects to the focused Premium tier page.
  You can safely replace any links pointing here with:
      route('payment.tier', 'premium')
--}}
@php
    // Soft redirect — render the tier page directly
    $tier     = 'premium';
    $tierConf = config('paystack.subscriptions.premium');
    $user     = auth()->user();
    $tierIcons = ['premium' => '📗', 'vip' => '⭐', 'vvip' => '👑'];
@endphp

@include('payments.tier')
