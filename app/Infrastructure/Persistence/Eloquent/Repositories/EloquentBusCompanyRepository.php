<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Bus\Entities\BusCompany;
use App\Domain\Bus\Repositories\BusCompanyRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\BusCompanyModel;
use Illuminate\Support\Collection;

final class EloquentBusCompanyRepository implements BusCompanyRepositoryInterface
{
    public function findAll(): Collection
    {
        return BusCompanyModel::all()
            ->map(fn(BusCompanyModel $model) => $this->toEntity($model));
    }

    public function findByCode(string $code): ?BusCompany
    {
        $model = BusCompanyModel::where('code', $code)->first();

        return $model ? $this->toEntity($model) : null;
    }

    public function findById(int $id): ?BusCompany
    {
        $model = BusCompanyModel::find($id);

        return $model ? $this->toEntity($model) : null;
    }

    private function toEntity(BusCompanyModel $model): BusCompany
    {
        return new BusCompany(
            id: $model->id,
            code: $model->code,
            name: $model->name,
            primaryColor: $model->primary_color,
            secondaryColor: $model->secondary_color,
            textColor: $model->text_color
        );
    }
}
