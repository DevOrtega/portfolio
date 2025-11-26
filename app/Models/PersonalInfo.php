<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Personal Information Model
 * 
 * @property int $id
 * @property string $name
 * @property string|null $headline
 * @property string|null $bio
 * @property string|null $email
 * @property string|null $linkedin_url
 * @property string|null $github_url
 * @property string|null $cv_path
 */
final class PersonalInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'headline',
        'headline_en',
        'bio',
        'bio_en',
        'email',
        'linkedin_url',
        'github_url',
        'cv_path',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'string',
            'headline' => 'string',
            'bio' => 'string',
            'email' => 'string',
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
            $data['headline'] = $this->headline_en ?? $this->headline;
            $data['bio'] = $this->bio_en ?? $this->bio;
        }

        // Remove _en fields from response
        unset($data['headline_en'], $data['bio_en']);

        return $data;
    }
}
