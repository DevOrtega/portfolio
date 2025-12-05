<?php

namespace App\Domain\Portfolio\Entities;

/**
 * Domain Entity: PersonalInfo
 * 
 * Represents personal information in the portfolio domain.
 * This is a pure domain object with no framework dependencies.
 */
final readonly class PersonalInfo
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $headline,
        public ?string $headlineEn,
        public ?string $bio,
        public ?string $bioEn,
        public string $email,
        public ?string $linkedinUrl,
        public ?string $githubUrl,
        public ?string $cvPath
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'headline' => $this->headline,
            'headline_en' => $this->headlineEn,
            'bio' => $this->bio,
            'bio_en' => $this->bioEn,
            'email' => $this->email,
            'linkedin_url' => $this->linkedinUrl,
            'github_url' => $this->githubUrl,
            'cv_path' => $this->cvPath,
        ];
    }
}
