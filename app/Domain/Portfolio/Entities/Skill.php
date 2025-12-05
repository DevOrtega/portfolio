<?php

namespace App\Domain\Portfolio\Entities;

/**
 * Domain Entity: Skill
 * 
 * Represents a skill in the portfolio domain.
 * This is a pure domain object with no framework dependencies.
 */
final readonly class Skill
{
    public function __construct(
        public int $id,
        public string $name,
        public string $category,
        public int $proficiency,
        public bool $isPersonal = false,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'proficiency' => $this->proficiency,
            'is_personal' => $this->isPersonal,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
