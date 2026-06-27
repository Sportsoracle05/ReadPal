<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'course_title',
        'lecturer',
        'slug',
    ];

    // Automatically generate slug when creating a resource
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($resource) {
            $resource->slug = Str::slug($resource->course_title);
        });
    }

    // Tell Laravel to use slug for route binding instead of ID
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }
}


