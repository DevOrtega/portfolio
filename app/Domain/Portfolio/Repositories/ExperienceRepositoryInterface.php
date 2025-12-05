<?php

namespace App\Domain\Portfolio\Repositories;

use App\Domain\Portfolio\Entities\Experience;
use Illuminate\Support\Collection;

/**
 * Repository Interface: ExperienceRepositoryInterface
 * 
 * Defines the contract for experience data access.
 */
interface ExperienceRepositoryInterface
{
    /**
     * Retrieve all experiences
     * 
     * @return Collection<int, Experience>
     */
    public function findAll(): Collection;

    /**
     * Find experiences filtered by year
     * 
     * @param int $year
     * @return Collection<int, Experience>
     */
    public function findByYear(int $year): Collection;

    /**
     * Find an experience by ID
     * 
     * @param int $id
     * @return Experience|null
     */
    public function findById(int $id): ?Experience;
}
