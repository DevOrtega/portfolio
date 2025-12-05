<?php

namespace App\Domain\Bus\Repositories;

use App\Domain\Bus\Entities\BusCompany;
use Illuminate\Support\Collection;

/**
 * Repository Interface: BusCompanyRepositoryInterface
 */
interface BusCompanyRepositoryInterface
{
    /**
     * @return Collection<int, BusCompany>
     */
    public function findAll(): Collection;

    public function findByCode(string $code): ?BusCompany;

    public function findById(int $id): ?BusCompany;
}
