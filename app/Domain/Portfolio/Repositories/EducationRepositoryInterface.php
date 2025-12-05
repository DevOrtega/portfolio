<?php

namespace App\Domain\Portfolio\Repositories;

use App\Domain\Portfolio\Entities\Education;
use Illuminate\Support\Collection;

/**
 * Repository Interface: EducationRepositoryInterface
 * 
 * Defines the contract for education data access.
 */
interface EducationRepositoryInterface
{
    /**
     * Retrieve all education records
     * 
     * @return Collection<int, Education>
     */
    public function findAll(): Collection;

    /**
     * Find education records filtered by year
     * 
     * @param int $year
     * @return Collection<int, Education>
     */
    public function findByYear(int $year): Collection;

    /**
     * Find an education record by ID
     * 
     * @param int $id
     * @return Education|null
     */
    public function findById(int $id): ?Education;
}
