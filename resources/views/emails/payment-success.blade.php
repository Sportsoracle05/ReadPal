<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Confirmed — ReadPal</title>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=DM+Sans:wght@400;500&display=swap');
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { background: #0f1a13; font-family: 'DM Sans', sans-serif; color: #e8e0d0; padding: 40px 16px; }
  .container { max-width: 560px; margin: 0 auto; background: #15201a; border: 1px solid #2a4030; border-radius: 12px; overflow: hidden; }

  /* Header colours per tier */
  .header-premium { background: #15803D; }
  .header-vip     { background: linear-gradient(135deg, #92400e, #b45309); }
  .header-vvip    { background: linear-gradient(135deg, #78350f, #d97706, #78350f); }

  .header { padding: 32px 40px; text-align: center; }
  .header .badge { display: inline-block; background: rgba(240,176,80,0.15); border: 1px solid #F0B050; color: #F0B050; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; padding: 4px 14px; border-radius: 20px; margin-bottom: 14px; }
  .header .tier-icon { font-size: 36px; margin-bottom: 10px; }
  .header h1 { font-family: 'Cormorant Garamond', serif; font-size: 26px; font-weight: 600; color: #fff; }
  .body { padding: 36px 40px; }
  .greeting { font-size: 15px; color: #c8bfa8; margin-bottom: 24px; line-height: 1.6; }
  .amount-card { background: #1a2e20; border: 1px solid #2a4030; border-left: 4px solid #15803D; border-radius: 8px; padding: 20px 24px; margin-bottom: 28px; text-align: center; }
  .amount-label { font-size: 11px; letter-spacing: 1.5px; text-transform: uppercase; color: #7a9a85; margin-bottom: 6px; }
  .amount-value { font-family: 'Cormorant Garamond', serif; font-size: 38px; font-weight: 600; color: #F0B050; }
  .details-table { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
  .details-table tr { border-bottom: 1px solid #1e3025; }
  .details-table tr:last-child { border-bottom: none; }
  .details-table td { padding: 10px 0; font-size: 13px; }
  .details-table td:first-child { color: #7a9a85; width: 45%; }
  .details-table td:last-child { color: #e8e0d0; font-weight: 500; text-align: right; }
  .access-banner { border-radius: 10px; padding: 20px 24px; margin-bottom: 28px; display: flex; align-items: flex-start; gap: 16px; }
  .access-banner-premium { background: linear-gradient(135deg, #1e3a2a, #1a2e22); border: 1px solid #F0B050; }
  .access-banner-vip     { background: linear-gradient(135deg, #2a1e0a, #231808); border: 1px solid #d97706; }
  .access-banner-vvip    { background: linear-gradient(135deg, #2a1a00, #1e1200); border: 1px solid #f59e0b; }
  .banner-icon { font-size: 28px; flex-shrink: 0; }
  .access-banner h3 { font-family: 'Cormorant Garamond', serif; font-size: 18px; margin-bottom: 4px; }
  .access-banner-premium h3 { color: #F0B050; }
  .access-banner-vip h3     { color: #fbbf24; }
  .access-banner-vvip h3    { color: #fcd34d; }
  .access-banner p { font-size: 13px; color: #a8c0b0; line-height: 1.5; }
  .cta-btn { display: block; color: #fff !important; text-decoration: none; text-align: center; padding: 14px 28px; border-radius: 8px; font-size: 14px; font-weight: 500; margin-bottom: 28px; }
  .cta-premium { background: #15803D; }
  .cta-vip     { background: #b45309; }
  .cta-vvip    { background: linear-gradient(135deg, #92400e, #d97706); }
  .reference { background: #0f1a13; border: 1px dashed #2a4030; border-radius: 6px; padding: 12px 16px; font-family: monospace; font-size: 12px; color: #6a8a75; text-align: center; margin-bottom: 24px; word-break: break-all; }
  .footer { border-top: 1px solid #1e3025; padding: 24px 40px; text-align: center; }
  .footer p { font-size: 12px; color: #4a6455; line-height: 1.7; }
  .footer a { color: #15803D; text-decoration: none; }
</style>
</head>
<body>
<div class="container">

  <div class="header header-{{ $payment->metadata['subscription_tier'] ?? 'premium' }}">
    <div class="badge">ReadPal · Oracle Tech</div>
    <div class="tier-icon">{{ $tierIcon }}</div>
    <h1>{{ $tierLabel }} Activated ✓</h1>
  </div>

  <div class="body">

    <p class="greeting">
      Hi {{ $payment->user->name }},<br><br>
      Your payment has been confirmed and your access has been activated. Here's your receipt.
    </p>

    <div class="amount-card">
      <div class="amount-label">Amount Paid</div>
      <div class="amount-value">{{ $amountDisplay }}</div>
    </div>

    <table class="details-table">
      <tr><td>Plan</td><td>{{ $planName }}</td></tr>
      <tr><td>Date</td><td>{{ $payment->paid_at?->format('F j, Y · g:i A') }}</td></tr>
      @if($payment->gateway_channel)
      <tr><td>Channel</td><td>{{ ucfirst($payment->gateway_channel) }}</td></tr>
      @endif
      @if($payment->card_last4)
      <tr><td>Card</td><td>{{ ucfirst($payment->card_brand ?? '') }} •••• {{ $payment->card_last4 }}</td></tr>
      @endif
      <tr><td>Status</td><td style="color:#4ade80;">✓ Successful</td></tr>
    </table>

    @if($isSubscription)
    @php $tier = $payment->metadata['subscription_tier'] ?? 'premium'; @endphp
    <div class="access-banner access-banner-{{ $tier }}">
      <div class="banner-icon">{{ $tierIcon }}</div>
      <div>
        <h3>{{ $tierLabel }} Access Active</h3>
        <p>
          You now have full {{ $tierLabel }} access on ReadPal.
          @if($premiumExpiry)
            Your subscription is active until <strong>{{ $premiumExpiry }}</strong>.
          @endif
        </p>
      </div>
    </div>
    @endif

    <a href="{{ route('dashboard') }}" class="cta-btn cta-{{ $payment->metadata['subscription_tier'] ?? 'premium' }}">
      {{ $isSubscription ? 'Start Learning →' : 'Return to ReadPal →' }}
    </a>

    <div class="reference">Transaction Reference: {{ $payment->reference }}</div>

    <p style="font-size:13px;color:#7a9a85;line-height:1.6;">
      Keep this email as your receipt. If you need support, quote the reference above.
    </p>

  </div>

  <div class="footer">
    <p>
      ReadPal is developed by Oracle Tech for AAUA Sociology 300L.<br>
      This is an automated receipt — please do not reply to this email.<br>
      <a href="{{ route('dashboard') }}">readpal.online</a>
    </p>
  </div>

</div>
</body>
</html>
