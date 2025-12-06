<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Project Model
 * 
 * @property int $id
 * @property string $title
 * @property string|null $title_en
 * @property string $description
 * @property string|null $description_en
 * @property string|null $url
 * @property string|null $github_url
 * @property string|null $image_path
 * @property array $tags
 */
final class Project extends Model
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
