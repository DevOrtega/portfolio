<?php

namespace App\Application\Portfolio\Services;

use App\Domain\Portfolio\Entities\Project;
use App\Domain\Portfolio\Repositories\ProjectRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Application Service: ProjectService
 * 
 * Handles business logic for projects.
 * Depends on repository interface (Dependency Inversion Principle).
 */
final readonly class ProjectService
{
    public function __construct(
        private ProjectRepositoryInterface $repository
    ) {
    }

    /**
     * Get all projects
     * 
     * @return Collection<int, Project>
     */
    public function getAllProjects(): Collection
    {
        return $this->repository->findAll();
    }

    /**
     * Get a project by ID
     * 
     * @param int $id
     * @return Project|null
     */
    public function getProjectById(int $id): ?Project
    {
        return $this->repository->findById($id);
    }

    /**
     * Create or update a project
     * 
     * @param Project $project
     * @return Project
     */
    public function saveProject(Project $project): Project
    {
        return $this->repository->save($project);
    }

    /**
     * Delete a project
     * 
     * @param int $id
     * @return bool
     */
    public function deleteProject(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
