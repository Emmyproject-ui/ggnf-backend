<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'category',
        'description',
        'full_description',
        'image',
        'published',
    ];

    protected $casts = [
        'published' => 'boolean',
    ];
}
