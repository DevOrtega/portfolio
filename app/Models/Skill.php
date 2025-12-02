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

    /**
     * Get the experiences associated with the skill.
     */
    public function experiences()
    {
        return $this->belongsToMany(Experience::class, 'experience_skill');
    }

    /**
     * Get the educations associated with the skill.
     */
    public function educations()
    {
        return $this->belongsToMany(Education::class, 'education_skill');
    }

    protected $fillable = [
        'name',
        'category',
        'proficiency',
        'is_personal',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'string',
            'category' => 'string',
            'proficiency' => 'integer',
            'is_personal' => 'boolean',
        ];
    }
}
