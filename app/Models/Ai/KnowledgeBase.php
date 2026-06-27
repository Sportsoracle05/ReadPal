<?php
namespace App\Models\Ai;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class KnowledgeBase extends Model
{
    use SoftDeletes;

    protected $connection = 'ai';
    protected $table = 'knowledge_bases';

    protected $fillable = [
        'user_id', 'title', 'slug', 'subject',
        'course_code', 'is_public', 'description',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    // Auto-generate slug when title is set
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title) . '-' . Str::random(6);
            }
        });
    }

    // ────────────────────────────────────────────────────────
    // RELATIONSHIPS (all within 'ai' connection)
    // ────────────────────────────────────────────────────────

    public function paragraphs()
    {
        return $this->hasMany(KnowledgeParagraph::class, 'knowledge_base_id')
                    ->orderBy('position');
    }

    public function tags()
    {
        return $this->hasMany(KnowledgeTag::class, 'knowledge_base_id');
    }

    public function conversations()
    {
        return $this->hasMany(AiConversation::class, 'knowledge_base_id');
    }

    // ────────────────────────────────────────────────────────
    // CROSS-DATABASE: get the User from readpalc_readpal
    // ────────────────────────────────────────────────────────

    /**
     * Not a real Eloquent relationship — just a helper method.
     * Real cross-DB joins aren't possible; we resolve in PHP.
     */
    public function getUser(): ?\App\Models\User
    {
        return \App\Models\User::find($this->user_id);
    }
}