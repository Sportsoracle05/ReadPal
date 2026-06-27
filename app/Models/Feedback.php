<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = [
        'rating',
        'category',
        'name',
        'matric_no',
        'likes',
        'improvements',
        'other',
        'recommend',
    ];
}
