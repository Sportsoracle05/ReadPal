<?php
namespace App\Models\Ai;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KnowledgeParagraph extends Model
{
    protected $connection = 'ai';
    protected $table = 'knowledge_paragraphs';

    protected $fillable = [
        'knowledge_base_id', 'user_id', 'content',
        'excerpt', 'position', 'word_count', 'section_heading',
    ];

    // Auto-generate excerpt + word count before saving
    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($model) {
            if (!empty($model->content)) {
                // Store first 250 chars as excerpt (no need to load full content for previews)
                $model->excerpt = Str::limit(strip_tags($model->content), 250);
                $model->word_count = str_word_count(strip_tags($model->content));
            }
        });
    }

    public function knowledgeBase()
    {
        return $this->belongsTo(KnowledgeBase::class, 'knowledge_base_id');
    }

    public function tags()
    {
        return $this->hasMany(KnowledgeTag::class, 'knowledge_paragraph_id');
    }

    // ────────────────────────────────────────────────────────
    // SCOPE: only paragraphs with meaningful content
    // (skip 1-sentence fragments that won't help the user)
    // ────────────────────────────────────────────────────────
    public function scopeSubstantial($query, int $minWords = 15)
    {
        return $query->where('word_count', '>=', $minWords);
    }
}
