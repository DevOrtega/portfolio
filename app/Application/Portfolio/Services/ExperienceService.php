<?php

namespace App\Application\Portfolio\Services;

use App\Domain\Portfolio\Entities\Experience;
use App\Domain\Portfolio\Repositories\ExperienceRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Application Service: ExperienceService
 * 
 * Handles business logic for work experiences.
 */
final readonly class ExperienceService
{
    public function __construct(
        private ExperienceRepositoryInterface $repository
    ) {
    }

    /**
     * Get all experiences ordered by date
     * 
     * @return Collection<int, Experience>
     */
    public function getAllExperiences(): Collection
    {
        return $this->repository->findAll();
    }

    /**
     * Get experiences filtered by year
     * 
     * @param int|null $year
     * @return Collection<int, Experience>
     */
    public function getExperiencesByYear(?int $year): Collection
    {
        if ($year === null) {
            return $this->getAllExperiences();
        }

        return $this->repository->findByYear($year);
    }

    /**
     * Get an experience by ID
     * 
     * @param int $id
     * @return Experience|null
     */
    public function getExperienceById(int $id): ?Experience
    {
        return $this->repository->findById($id);
    }
}
