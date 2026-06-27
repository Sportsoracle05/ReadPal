<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_id',
        'title',
        'type',
        'slug',
        'pdf_path',
        'note_text',
    ];

    // ✅ Tell Laravel to use slug for route binding
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(UserQuizAttempt::class);
    }
}