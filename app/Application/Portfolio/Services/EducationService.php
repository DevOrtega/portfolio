<?php

namespace App\Application\Portfolio\Services;

use App\Domain\Portfolio\Entities\Education;
use App\Domain\Portfolio\Repositories\EducationRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Application Service: EducationService
 * 
 * Handles business logic for education records.
 */
final readonly class EducationService
{
    public function __construct(
        private EducationRepositoryInterface $repository
    ) {
    }

    /**
     * Get all education records ordered by date
     * 
     * @return Collection<int, Education>
     */
    public function getAllEducation(): Collection
    {
        return $this->repository->findAll();
    }

    /**
     * Get education records filtered by year
     * 
     * @param int|null $year
     * @return Collection<int, Education>
     */
    public function getEducationByYear(?int $year): Collection
    {
        if ($year === null) {
            return $this->getAllEducation();
        }

        return $this->repository->findByYear($year);
    }

    /**
     * Get an education record by ID
     * 
     * @param int $id
     * @return Education|null
     */
    public function getEducationById(int $id): ?Education
    {
        return $this->repository->findById($id);
    }
}
