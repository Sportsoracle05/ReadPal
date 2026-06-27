<?php
namespace App\Models\Ai;

use Illuminate\Database\Eloquent\Model;

class AiConversation extends Model
{
    protected $connection = 'ai';
    protected $table = 'ai_conversations';

    protected $fillable = [
        'user_id', 'knowledge_base_id', 'question',
        'search_keywords', 'answer', 'matched_paragraph_ids',
        'confidence_score', 'from_cache',
    ];

    protected $casts = [
        'matched_paragraph_ids' => 'array', // auto JSON encode/decode
        'from_cache'             => 'boolean',
    ];

    public function knowledgeBase()
    {
        return $this->belongsTo(KnowledgeBase::class, 'knowledge_base_id');
    }

    // Cross-DB helper
    public function getUser(): ?\App\Models\User
    {
        return \App\Models\User::find($this->user_id);
    }

    // Scope: only recent (last 30 days) — avoids full table scans
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subDays(30));
    }
}