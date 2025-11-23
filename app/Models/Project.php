<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'url',
        'github_url',
        'image_path',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];
    //
}
