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
        public string $description,
        public ?string $url,
        public ?string $githubUrl,
        public ?string $imagePath,
        public array $tags
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->url,
            'github_url' => $this->githubUrl,
            'image_path' => $this->imagePath,
            'tags' => $this->tags,
        ];
    }
}
