<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PaymentPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'amount',
        'currency',
        'category',
        'is_active',
        'is_recurring',
        'metadata',
        'max_uses',
        'uses_count',
        'available_from',
        'available_until',
        'created_by',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'is_recurring'    => 'boolean',
        'metadata'        => 'array',
        'available_from'  => 'datetime',
        'available_until' => 'datetime',
    ];

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (PaymentPlan $plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name) . '-' . Str::random(6);
            }
        });
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getAmountInNairaAttribute(): string
    {
        return '₦' . number_format($this->amount / 100, 2);
    }

    public function getTotalCollectedAttribute(): int
    {
        return $this->payments()->successful()->sum('amount');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isAvailable(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->max_uses && $this->uses_count >= $this->max_uses) {
            return false;
        }

        $now = now();

        if ($this->available_from && $now->lt($this->available_from)) {
            return false;
        }

        if ($this->available_until && $now->gt($this->available_until)) {
            return false;
        }

        return true;
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCustom($query)
    {
        return $query->where('category', 'custom');
    }
}
