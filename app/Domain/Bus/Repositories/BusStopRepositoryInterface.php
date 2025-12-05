<?php

namespace App\Domain\Bus\Repositories;

use App\Domain\Bus\Entities\BusStop;
use Illuminate\Support\Collection;

/**
 * Repository Interface: BusStopRepositoryInterface
 */
interface BusStopRepositoryInterface
{
    /**
     * @return Collection<int, BusStop>
     */
    public function findAll(): Collection;

    public function findByCode(string $code): ?BusStop;

    public function findById(int $id): ?BusStop;

    /**
     * @return Collection<int, BusStop>
     */
    public function findByZone(string $zone): Collection;
}
