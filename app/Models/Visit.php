<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'material_id',
        'session_id',
        'ip',
        'ip_address',    // keep both names if you used different columns across versions
        'user_agent',
        'page',
        'visited_at'
    ];

    protected $casts = [
        'material_id' => 'integer',
    ];

    public function material()
    {
        return $this->belongsTo(\App\Models\Material::class);
    }
}
