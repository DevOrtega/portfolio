<?php

namespace App\Domain\Portfolio\Repositories;

use App\Domain\Portfolio\Entities\Project;
use Illuminate\Support\Collection;

/**
 * Repository Interface: ProjectRepositoryInterface
 * 
 * Defines the contract for project data access.
 * This follows the Dependency Inversion Principle (SOLID).
 */
interface ProjectRepositoryInterface
{
    /**
     * Retrieve all projects
     * 
     * @return Collection<int, Project>
     */
    public function findAll(): Collection;

    /**
     * Find a project by ID
     * 
     * @param int $id
     * @return Project|null
     */
    public function findById(int $id): ?Project;

    /**
     * Save a project
     * 
     * @param Project $project
     * @return Project
     */
    public function save(Project $project): Project;

    /**
     * Delete a project
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
