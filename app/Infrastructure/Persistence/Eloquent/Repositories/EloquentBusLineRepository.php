<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Bus\Entities\BusCompany;
use App\Domain\Bus\Entities\BusLine;
use App\Domain\Bus\Entities\BusStop;
use App\Domain\Bus\Repositories\BusLineRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\BusLineModel;
use App\Infrastructure\Persistence\Eloquent\Models\BusStopModel;
use App\Infrastructure\Persistence\Eloquent\Models\BusCompanyModel;
use App\Infrastructure\Services\OsrmService;
use Illuminate\Support\Collection;

final class EloquentBusLineRepository implements BusLineRepositoryInterface
{
    public function __construct(
        private readonly EloquentBusStopRepository $stopRepository,
        private readonly OsrmService $osrmService
    ) {
    }

    public function findAll(): Collection
    {
        return BusLineModel::with(['company', 'stopsOutbound', 'stopsInbound'])
            ->get()
            ->map(fn(BusLineModel $model) => $this->toEntity($model));
    }

    public function findByCompany(string $companyCode): Collection
    {
        return BusLineModel::with(['company', 'stopsOutbound', 'stopsInbound'])
            ->whereHas('company', fn($q) => $q->where('code', $companyCode))
            ->get()
            ->map(fn(BusLineModel $model) => $this->toEntity($model));
    }

    public function findMainLines(): Collection
    {
        return BusLineModel::with(['company', 'stopsOutbound', 'stopsInbound'])
            ->where('is_main_line', true)
            ->get()
            ->map(fn(BusLineModel $model) => $this->toEntity($model));
    }

    public function findByCompanyAndNumber(string $companyCode, string $lineNumber): ?BusLine
    {
        $model = BusLineModel::with(['company', 'stopsOutbound', 'stopsInbound'])
            ->whereHas('company', fn($q) => $q->where('code', $companyCode))
            ->where('line_number', $lineNumber)
            ->first();

        return $model ? $this->toEntity($model) : null;
    }

    public function findById(int $id): ?BusLine
    {
        $model = BusLineModel::with(['company', 'stopsOutbound', 'stopsInbound'])->find($id);

        return $model ? $this->toEntity($model) : null;
    }

    private function toEntity(BusLineModel $model): BusLine
    {
        $company = $this->toCompanyEntity($model->company);
        
        $stopsOutbound = $model->stopsOutbound
            ->map(fn(BusStopModel $stop) => $this->stopRepository->toEntity($stop))
            ->all();
        
        $stopsInbound = $model->stopsInbound
            ->map(fn(BusStopModel $stop) => $this->stopRepository->toEntity($stop))
            ->all();

        // Get stop coordinates for OSRM routing
        $outboundCoords = array_map(
            fn(BusStop $stop) => [$stop->latOutbound, $stop->lngOutbound],
            $stopsOutbound
        );
        
        $inboundCoords = array_map(
            fn(BusStop $stop) => [$stop->latInbound, $stop->lngInbound],
            $stopsInbound
        );
        
        // Get OSRM routes (cached)
        $osrmRouteOutbound = count($outboundCoords) >= 2 
            ? $this->osrmService->getRoute($outboundCoords) 
            : $outboundCoords;
        
        $osrmRouteInbound = count($inboundCoords) >= 2 
            ? $this->osrmService->getRoute($inboundCoords) 
            : $inboundCoords;

        return new BusLine(
            id: $model->id,
            lineNumber: $model->line_number,
            type: $model->type,
            origin: $model->origin,
            destination: $model->destination,
            color: $model->color,
            isMainLine: $model->is_main_line,
            company: $company,
            stopsOutbound: $stopsOutbound,
            stopsInbound: $stopsInbound,
            osrmRouteOutbound: $osrmRouteOutbound,
            osrmRouteInbound: $osrmRouteInbound
        );
    }

    private function toCompanyEntity(BusCompanyModel $model): BusCompany
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
