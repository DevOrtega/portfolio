<?php

namespace App\Domain\Portfolio\Repositories;

use App\Domain\Portfolio\Entities\Skill;
use Illuminate\Support\Collection;

/**
 * Repository Interface: SkillRepositoryInterface
 * 
 * Defines the contract for skill data access.
 */
interface SkillRepositoryInterface
{
    /**
     * Retrieve all skills
     * 
     * @return Collection<int, Skill>
     */
    public function findAll(): Collection;

    /**
     * Find skills filtered by year (based on related experiences/education)
     * 
     * @param int $year
     * @return Collection<int, Skill>
     */
    public function findByYear(int $year): Collection;

    /**
     * Find a skill by ID
     * 
     * @param int $id
     * @return Skill|null
     */
    public function findById(int $id): ?Skill;

    /**
     * Find personal skills (always shown)
     * 
     * @return Collection<int, Skill>
     */
    public function findPersonalSkills(): Collection;
}
