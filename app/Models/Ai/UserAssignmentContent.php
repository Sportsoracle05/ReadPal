<?php
namespace App\Models\Ai;

use Illuminate\Database\Eloquent\Model;

class UserAssignmentContent extends Model
{
    public $timestamps  = false; // only updated_at, handled by DB default
    public $incrementing = true;

    protected $connection = 'ai';
    protected $table      = 'user_assignment_contents';

    protected $fillable = [
        'user_assignment_id', 'section_id', 'content', 'word_count',
    ];

    // Hook: auto-calculate word_count before save
    protected static function boot(): void
    {
        parent::boot();

        static::saving(function ($model) {
            if (!empty($model->content)) {
                $model->word_count = str_word_count(strip_tags($model->content));
            }
        });
    }

    public function userAssignment()
    {
        return $this->belongsTo(UserAssignment::class, 'user_assignment_id');
    }

    public function section()
    {
        return $this->belongsTo(AssignmentSection::class, 'section_id');
    }
}
