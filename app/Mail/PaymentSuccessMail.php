<?php

namespace App\Mail;

use App\Models\Payment;
use App\Services\PaystackService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string  $amountDisplay;
    public string  $planName;
    public bool    $isSubscription;
    public string  $tierLabel;
    public string  $tierIcon;
    public ?string $premiumExpiry;

    public function __construct(public readonly Payment $payment)
    {
        $this->isSubscription = $payment->payment_type === Payment::TYPE_PREMIUM;

        // Determine tier details from payment metadata
        $tier      = $payment->metadata['subscription_tier'] ?? 'premium';
        $tierConf  = config("paystack.subscriptions.{$tier}", config('paystack.subscriptions.premium'));

        $this->amountDisplay = PaystackService::toNaira($payment->amount);
        $this->tierLabel     = $tierConf['label']       ?? 'Premium';
        $this->tierIcon      = match ($tier) {
            'vvip'  => '👑',
            'vip'   => '⭐',
            default => '🎓',
        };
        $this->planName      = $this->isSubscription
            ? "ReadPal {$this->tierLabel} — {$tierConf['description']}"
            : ($payment->paymentPlan?->name ?? 'Custom Payment');

        $this->premiumExpiry = $payment->expires_at?->format('F j, Y');
    }

    public function envelope(): Envelope
    {
        $subject = $this->isSubscription
            ? "{$this->tierIcon} {$this->tierLabel} Access Activated — ReadPal"
            : "Payment Confirmed: {$this->planName} — ReadPal";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.payment-success');
    }

    public function attachments(): array
    {
        return [];
    }
}
