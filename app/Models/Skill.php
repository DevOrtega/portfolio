<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Skill Model
 * 
 * @property int $id
 * @property string $name
 * @property string $category
 * @property int $proficiency
 */
final class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'proficiency',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'string',
            'category' => 'string',
            'proficiency' => 'integer',
        ];
    }
}
