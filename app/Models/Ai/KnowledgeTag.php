<?php
namespace App\Models\Ai;

use Illuminate\Database\Eloquent\Model;

class KnowledgeTag extends Model
{
    protected $connection = 'ai';
    protected $table = 'knowledge_tags';

    public $timestamps = false; // tags don't need timestamps — saves space

    protected $fillable = [
        'knowledge_paragraph_id', 'knowledge_base_id', 'tag',
    ];

    public function paragraph()
    {
        return $this->belongsTo(KnowledgeParagraph::class, 'knowledge_paragraph_id');
    }
}