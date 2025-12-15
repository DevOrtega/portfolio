<?php

namespace App\Domain\Portfolio\Entities;

/**
 * Domain Entity: Project
 * 
 * Represents a project in the portfolio domain.
 * This is a pure domain object with no framework dependencies.
 */
final readonly class Project
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $titleEn,
        public string $description,
        public ?string $descriptionEn,
        public ?string $url,
        public ?string $githubUrl,
        public ?string $imagePath,
        public array $tags
    ) {
    }

    public function toArray(?string $locale = null): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'title_en' => $this->titleEn,
            'description' => $this->description,
            'description_en' => $this->descriptionEn,
            'url' => $this->url,
            'github_url' => $this->githubUrl,
            'image_path' => $this->imagePath,
            'tags' => $this->tags,
        ];

        if ($locale === 'en') {
            $data['title'] = $this->titleEn ?? $this->title;
            $data['description'] = $this->descriptionEn ?? $this->description;
        }

        if ($locale) {
            unset($data['title_en'], $data['description_en']);
        }

        return $data;
    }
}
