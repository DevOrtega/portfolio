<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Bus\Entities\BusStop;
use App\Domain\Bus\Repositories\BusStopRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\BusStopModel;
use Illuminate\Support\Collection;

final class EloquentBusStopRepository implements BusStopRepositoryInterface
{
    public function findAll(): Collection
    {
        return BusStopModel::all()
            ->map(fn(BusStopModel $model) => $this->toEntity($model));
    }

    public function findByCode(string $code): ?BusStop
    {
        $model = BusStopModel::where('code', $code)->first();

        return $model ? $this->toEntity($model) : null;
    }

    public function findById(int $id): ?BusStop
    {
        $model = BusStopModel::find($id);

        return $model ? $this->toEntity($model) : null;
    }

    public function findByZone(string $zone): Collection
    {
        return BusStopModel::where('zone', $zone)
            ->get()
            ->map(fn(BusStopModel $model) => $this->toEntity($model));
    }

    public function toEntity(BusStopModel $model): BusStop
    {
        return new BusStop(
            id: $model->id,
            code: $model->code,
            name: $model->name,
            zone: $model->zone,
            latOutbound: $model->lat_outbound,
            lngOutbound: $model->lng_outbound,
            latInbound: $model->lat_inbound,
            lngInbound: $model->lng_inbound
        );
    }
}
