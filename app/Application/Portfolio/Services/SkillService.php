<?php

namespace App\Application\Portfolio\Services;

use App\Domain\Portfolio\Entities\Skill;
use App\Domain\Portfolio\Repositories\SkillRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Application Service: SkillService
 * 
 * Handles business logic for skills.
 */
final readonly class SkillService
{
    public function __construct(
        private SkillRepositoryInterface $repository
    ) {
    }

    /**
     * Get all skills ordered by proficiency
     * 
     * @return Collection<int, Skill>
     */
    public function getAllSkills(): Collection
    {
        return $this->repository->findAll();
    }

    /**
     * Get skills filtered by year
     * Personal skills are always included
     * 
     * @param int|null $year
     * @return Collection<int, Skill>
     */
    public function getSkillsByYear(?int $year): Collection
    {
        if ($year === null) {
            return $this->getAllSkills();
        }

        return $this->repository->findByYear($year);
    }

    /**
     * Get a skill by ID
     * 
     * @param int $id
     * @return Skill|null
     */
    public function getSkillById(int $id): ?Skill
    {
        return $this->repository->findById($id);
    }

    /**
     * Get only personal skills
     * 
     * @return Collection<int, Skill>
     */
    public function getPersonalSkills(): Collection
    {
        return $this->repository->findPersonalSkills();
    }
}
