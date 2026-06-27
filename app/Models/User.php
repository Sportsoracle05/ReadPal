<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\PushSubscription;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    
    protected $connection = 'readpal';
    protected $table = 'users';

    /**
     * Cross-database relationship: User → AI Conversations
     *
     * NOTE: Eloquent relationships across databases work fine as long as
     * both connections point to the same MySQL server. Laravel just runs
     * two separate queries — it does NOT do a SQL JOIN across databases.
     * The "link" is purely in PHP memory.
     */
    public function aiConversations()
    {
        // HasMany resolves via user_id in ai_conversations
        return $this->hasMany(\App\Models\Ai\AiConversation::class, 'user_id');
    }

    public function knowledgeBases()
    {
        return $this->hasMany(\App\Models\Ai\KnowledgeBase::class, 'user_id');
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'email',
        'password',
        'is_premium',
        'premium_expires_at',
        'premium_tier',
    ];

    /**
     * The attributes that should be hidden for arrays and JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'push_enabled' => 'boolean',
        'push_lecture_alerts' => 'boolean',
        'push_lecture_reminders' => 'boolean',
        'is_premium'         => 'boolean',
        'premium_expires_at' => 'datetime',
    ];
    


    /**
     * Get the user's full name (for convenience).
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->firstname} {$this->lastname}";
    }

    public function quizAttempts()
    {
        return $this->hasMany(UserQuizAttempt::class);
    }

    public function pushSubscriptions()
    {
        return $this->hasMany(PushSubscription::class);
    }
    


    /**
     * Automatically format username to lowercase.
     */
    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = strtolower(trim($value));
    }

    public function getRouteKeyName()
    {
        return 'username';
    }

    public function isSuper(): bool
    {
        return $this->role === 'super';
    }

    public function isRep(): bool
    {
        return $this->role === 'rep';
    }
    
    // ── Premium / Tier Helpers ─────────────────────────────────────────────────────

     public function payments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Payment::class);
    }

    
    /**
     * True if the user has ANY active subscription (premium, vip, or vvip).
     */
    public function hasActivePremium(): bool
    {
        if (! $this->is_premium) {
            return false;
        }
        if ($this->premium_expires_at && $this->premium_expires_at->isPast()) {
            return false;
        }
        return true;
    }

    /**
     * True if user is on VIP or VVIP.
     */
    public function isVipOrAbove(): bool
    {
        return $this->hasActivePremium()
            && in_array($this->premium_tier, ['vip', 'vvip'], true);
    }

    /**
     * True only for VVIP subscribers.
     */
    public function isVvip(): bool
    {
        return $this->hasActivePremium() && $this->premium_tier === 'vvip';
    }

    /**
     * Numeric tier rank: 0 = none, 1 = premium, 2 = vip, 3 = vvip.
     * Useful for comparisons.
     */
    public function tierRank(): int
    {
        if (! $this->hasActivePremium()) {
            return 0;
        }
        return (int) config("paystack.subscriptions.{$this->premium_tier}.tier_rank", 0);
    }

    /**
     * Human-readable tier label, e.g. "VIP", "VVIP", "Premium", or "Free".
     */
    public function tierLabel(): string
    {
        if (! $this->hasActivePremium()) {
            return 'Free';
        }
        return config("paystack.subscriptions.{$this->premium_tier}.label", 'Premium');
    }

    /**
     * Days remaining on current subscription. Returns 0 if not active.
     */
    public function premiumDaysRemaining(): int
    {
        if (! $this->hasActivePremium() || ! $this->premium_expires_at) {
            return 0;
        }
        return max(0, (int) now()->diffInDays($this->premium_expires_at, false));
    }

    /**
     * Total paid in kobo across all successful payments.
     */
    public function totalSpentKobo(): int
    {
        return (int) $this->payments()->where('status', 'success')->sum('amount');
    }

    /**
     * Returns true if user is an admin.
     */
    public function isAdmin(): bool
    {
        return (bool) ($this->is_admin ?? false);
    }



}
