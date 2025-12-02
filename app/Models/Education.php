<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Education Model
 * 
 * @property int $id
 * @property string $institution
 * @property string $degree
 * @property string $start_date
 * @property string|null $end_date
 * @property string|null $description
 */
final class Education extends Model
{
    use HasFactory;

    /**
     * Get the skills associated with the education.
     */
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'education_skill');
    }

    protected $fillable = [
        'institution',
        'degree',
        'degree_en',
        'start_date',
        'end_date',
        'description',
        'description_en',
    ];

    protected function casts(): array
    {
        return [
            'institution' => 'string',
            'degree' => 'string',
            'start_date' => 'string',
            'end_date' => 'string',
            'description' => 'string',
        ];
    }

    /**
     * Get localized data based on current locale
     */
    public function toArray(): array
    {
        $locale = app()->getLocale();
        $data = parent::toArray();

        if ($locale === 'en') {
            $data['degree'] = $this->degree_en ?? $this->degree;
            $data['description'] = $this->description_en ?? $this->description;
        }

        // Remove _en fields from response
        unset($data['degree_en'], $data['description_en']);

        return $data;
    }
}
