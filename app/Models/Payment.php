<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'payment_plan_id',
        'reference',
        'amount',
        'currency',
        'payment_type',
        'status',
        'gateway_reference',
        'gateway_channel',
        'gateway_response',
        'authorization_code',
        'card_last4',
        'card_brand',
        'paid_at',
        'expires_at',
        'metadata',
        'ip_address',
        'user_agent',
        'webhook_processed',
        'webhook_processed_at',
    ];

    protected $casts = [
        'paid_at'               => 'datetime',
        'expires_at'            => 'datetime',
        'webhook_processed_at'  => 'datetime',
        'webhook_processed'     => 'boolean',
        'metadata'              => 'array',
    ];

    // ─── Status constants ────────────────────────────────────────────────────

    const STATUS_PENDING   = 'pending';
    const STATUS_SUCCESS   = 'success';
    const STATUS_FAILED    = 'failed';
    const STATUS_ABANDONED = 'abandoned';

    // ─── Payment type constants ───────────────────────────────────────────────

    const TYPE_PREMIUM = 'premium';
    const TYPE_CUSTOM  = 'custom';

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentPlan(): BelongsTo
    {
        return $this->belongsTo(PaymentPlan::class);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    /**
     * Amount in Naira (for display).
     */
    public function getAmountInNairaAttribute(): string
    {
        return '₦' . number_format($this->amount / 100, 2);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeSuccessful($query)
    {
        return $query->where('status', self::STATUS_SUCCESS);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePremium($query)
    {
        return $query->where('payment_type', self::TYPE_PREMIUM);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
