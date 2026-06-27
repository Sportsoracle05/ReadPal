<?php
namespace App\Models\Ai;

use Illuminate\Database\Eloquent\Model;

class AssignmentSection extends Model
{
    public $timestamps = false; // sections don't need timestamps

    protected $connection = 'ai';
    protected $table      = 'assignment_sections';

    protected $fillable = [
        'assignment_id', 'title', 'questions',
        'guidance_note', 'position',
    ];

    protected $casts = [
        // Auto-decode JSON questions array on access
        'questions' => 'array',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class, 'assignment_id');
    }

    public function contents()
    {
        return $this->hasMany(UserAssignmentContent::class, 'section_id');
    }

    /**
     * Return questions as a formatted string for AI prompts.
     * e.g. "1. What is youth?\n2. Why is youth important?"
     */
    public function getQuestionsAsText(): string
    {
        $questions = $this->questions ?? [];
        if (empty($questions)) {
            return 'No specific questions provided.';
        }

        return implode("\n", array_map(
            fn($q, $i) => ($i + 1) . '. ' . $q,
            $questions,
            array_keys($questions)
        ));
    }
}