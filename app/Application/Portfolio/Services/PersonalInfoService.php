<?php

namespace App\Application\Portfolio\Services;

use App\Domain\Portfolio\Entities\PersonalInfo;
use App\Domain\Portfolio\Repositories\PersonalInfoRepositoryInterface;

/**
 * Application Service: PersonalInfoService
 * 
 * Handles business logic for personal information.
 */
final readonly class PersonalInfoService
{
    public function __construct(
        private PersonalInfoRepositoryInterface $repository
    ) {
    }

    /**
     * Get personal information
     * 
     * @return PersonalInfo|null
     */
    public function getPersonalInfo(): ?PersonalInfo
    {
        return $this->repository->get();
    }
}
