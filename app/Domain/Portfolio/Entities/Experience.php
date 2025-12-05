<?php

namespace App\Domain\Portfolio\Entities;

/**
 * Domain Entity: Experience
 * 
 * Represents a work experience in the portfolio domain.
 * This is a pure domain object with no framework dependencies.
 */
final readonly class Experience
{
    public function __construct(
        public int $id,
        public string $company,
        public string $role,
        public ?string $roleEn,
        public string $startDate,
        public ?string $endDate,
        public ?string $description,
        public ?string $descriptionEn,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'company' => $this->company,
            'role' => $this->role,
            'role_en' => $this->roleEn,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'description' => $this->description,
            'description_en' => $this->descriptionEn,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    /**
     * Check if experience was active during a specific year
     */
    public function wasActiveDuring(int $year): bool
    {
        $startYear = (int) substr($this->startDate, -4);
        $endYear = $this->endDate ? (int) substr($this->endDate, -4) : (int) date('Y');
        
        return $startYear <= $year && $endYear >= $year;
    }
}
