<?php

namespace App\Application\Bus\Services;

use App\Domain\Bus\Repositories\BusCompanyRepositoryInterface;
use App\Domain\Bus\Repositories\BusLineRepositoryInterface;
use App\Domain\Bus\Repositories\BusStopRepositoryInterface;
use Illuminate\Support\Collection;

/**
 * Application Service: BusService
 * 
 * Orchestrates bus-related operations.
 */
final readonly class BusService
{
    public function __construct(
        private BusCompanyRepositoryInterface $companyRepository,
        private BusLineRepositoryInterface $lineRepository,
        private BusStopRepositoryInterface $stopRepository
    ) {
    }

    /**
     * Get all companies with their configuration
     */
    public function getCompanies(): Collection
    {
        return $this->companyRepository->findAll();
    }

    /**
     * Get all bus lines grouped by company
     */
    public function getLinesByCompany(): array
    {
        $lines = $this->lineRepository->findAll();
        
        $grouped = [
            'municipales' => [],
            'global' => [],
            'night' => [],
        ];

        foreach ($lines as $line) {
            $companyCode = $line->company->code;
            if (isset($grouped[$companyCode])) {
                $grouped[$companyCode][] = $line->lineNumber;
            }
        }

        return $grouped;
    }

    /**
     * Get main lines for initial display
     */
    public function getMainLines(): array
    {
        $lines = $this->lineRepository->findMainLines();
        
        $grouped = [
            'municipales' => [],
            'global' => [],
            'night' => [],
        ];

        foreach ($lines as $line) {
            $companyCode = $line->company->code;
            if (isset($grouped[$companyCode])) {
                $grouped[$companyCode][] = $line->lineNumber;
            }
        }

        return $grouped;
    }

    /**
     * Get company colors
     */
    public function getCompanyColors(): array
    {
        $companies = $this->companyRepository->findAll();
        $colors = [];

        foreach ($companies as $company) {
            $colors[$company->code] = [
                'primary' => $company->primaryColor,
                'secondary' => $company->secondaryColor,
                'text' => $company->textColor,
            ];
        }

        return $colors;
    }

    /**
     * Get all routes with coordinates
     */
    public function getRoutes(): array
    {
        $lines = $this->lineRepository->findAll();
        
        return $lines->map(fn($line) => $line->toArray())->all();
    }

    /**
     * Get all stops as a map (code => coordinates)
     */
    public function getStops(): array
    {
        $stops = $this->stopRepository->findAll();
        $result = [];

        foreach ($stops as $stop) {
            $result[$stop->code] = [
                'outbound' => [$stop->latOutbound, $stop->lngOutbound],
                'inbound' => [$stop->latInbound, $stop->lngInbound],
            ];
        }

        return $result;
    }

    /**
     * Get complete bus data for the frontend
     */
    public function getBusData(): array
    {
        // Cache the entire payload for 1 hour to ensure instant response
        return \Illuminate\Support\Facades\Cache::remember('api_bus_data_full', 3600, function () {
            // Fetch lines once to avoid multiple DB/OSRM calls
            $allLines = $this->lineRepository->findAll();
            
            // Group lines by company manually to avoid re-fetching
            $linesByCompany = [
                'municipales' => [],
                'global' => [],
                'night' => [],
            ];
            foreach ($allLines as $line) {
                $companyCode = $line->company->code;
                if (isset($linesByCompany[$companyCode])) {
                    $linesByCompany[$companyCode][] = $line->lineNumber;
                }
            }

            // Get routes from the already fetched lines
            $routes = $allLines->map(function($line) {
                $data = $line->toArray();
                $data['company'] = $line->company->code;
                return $data;
            })->all();

            return [
                'companies' => $this->getCompanyColors(),
                'bus_lines' => $linesByCompany,
                'main_lines' => $this->getMainLines(),
                'routes' => $routes,
                'stops' => $this->getStops(),
                'map_config' => $this->getMapConfig(),
                'simulation_config' => $this->getSimulationConfig(),
            ];
        });
    }

    /**
     * Get map configuration
     */
    private function getMapConfig(): array
    {
        return [
            'center' => [28.050, -15.450],
            'bounds' => [
                'north' => 28.18,
                'south' => 27.74,
                'east' => -15.35,
                'west' => -15.60,
            ],
            'max_bounds' => [[27.70, -15.90], [28.20, -15.30]],
            'min_zoom' => 9.5,
            'max_bounds_viscosity' => 0.8,
        ];
    }

    /**
     * Get simulation configuration
     */
    private function getSimulationConfig(): array
    {
        return [
            'update_interval' => 5000,
            'buses_per_route' => [
                'min' => 1,
                'max' => 3,
            ],
            'speed' => [
                'urban' => 0.025,
                'interurban' => 0.035,
            ],
            'delay_probability' => 0.15,
        ];
    }
}
