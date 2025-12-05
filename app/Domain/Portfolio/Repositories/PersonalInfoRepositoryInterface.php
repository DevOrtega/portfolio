<?php

namespace App\Domain\Portfolio\Repositories;

use App\Domain\Portfolio\Entities\PersonalInfo;

/**
 * Repository Interface: PersonalInfoRepositoryInterface
 * 
 * Defines the contract for personal info data access.
 */
interface PersonalInfoRepositoryInterface
{
    /**
     * Get the personal info (singleton in the system)
     * 
     * @return PersonalInfo|null
     */
    public function get(): ?PersonalInfo;
}
