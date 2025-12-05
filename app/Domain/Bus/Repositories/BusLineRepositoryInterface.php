<?php

namespace App\Domain\Bus\Repositories;

use App\Domain\Bus\Entities\BusLine;
use Illuminate\Support\Collection;

/**
 * Repository Interface: BusLineRepositoryInterface
 */
interface BusLineRepositoryInterface
{
    /**
     * @return Collection<int, BusLine>
     */
    public function findAll(): Collection;

    /**
     * @return Collection<int, BusLine>
     */
    public function findByCompany(string $companyCode): Collection;

    /**
     * @return Collection<int, BusLine>
     */
    public function findMainLines(): Collection;

    public function findByCompanyAndNumber(string $companyCode, string $lineNumber): ?BusLine;

    public function findById(int $id): ?BusLine;
}
