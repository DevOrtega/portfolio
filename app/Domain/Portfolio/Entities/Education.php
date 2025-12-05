<?php

namespace App\Domain\Portfolio\Entities;

/**
 * Domain Entity: Education
 * 
 * Represents an education record in the portfolio domain.
 * This is a pure domain object with no framework dependencies.
 */
final readonly class Education
{
    public function __construct(
        public int $id,
        public string $institution,
        public string $degree,
        public ?string $degreeEn,
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
            'institution' => $this->institution,
            'degree' => $this->degree,
            'degree_en' => $this->degreeEn,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'description' => $this->description,
            'description_en' => $this->descriptionEn,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    /**
     * Check if education was active during a specific year
     */
    public function wasActiveDuring(int $year): bool
    {
        $startYear = (int) substr($this->startDate, -4);
        $endYear = $this->endDate ? (int) substr($this->endDate, -4) : (int) date('Y');
        
        return $startYear <= $year && $endYear >= $year;
    }
}
