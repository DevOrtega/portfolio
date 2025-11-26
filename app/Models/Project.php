<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_en',
        'description',
        'description_en',
        'url',
        'github_url',
        'image_path',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    /**
     * Get localized data based on current locale
     */
    public function toArray(): array
    {
        $locale = app()->getLocale();
        $data = parent::toArray();

        if ($locale === 'en') {
            $data['title'] = $this->title_en ?? $this->title;
            $data['description'] = $this->description_en ?? $this->description;
        }

        // Remove _en fields from response
        unset($data['title_en'], $data['description_en']);

        return $data;
    }
}
