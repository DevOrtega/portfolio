<?php

namespace App\Domain\Bus\Entities;

/**
 * Domain Entity: BusLine
 * 
 * Represents a bus line with its route information.
 */
final readonly class BusLine
{
    /**
     * @param array<BusStop> $stopsOutbound
     * @param array<BusStop> $stopsInbound
     * @param array $osrmRouteOutbound Pre-computed OSRM route for outbound direction
     * @param array $osrmRouteInbound Pre-computed OSRM route for inbound direction
     */
    public function __construct(
        public int $id,
        public string $lineNumber,
        public string $type,
        public string $origin,
        public string $destination,
        public ?string $color,
        public bool $isMainLine,
        public BusCompany $company,
        public array $stopsOutbound = [],
        public array $stopsInbound = [],
        public array $osrmRouteOutbound = [],
        public array $osrmRouteInbound = []
    ) {
    }

    /**
     * Get stop coordinates for the specified direction (simple waypoints)
     */
    public function getStopCoordinates(string $direction = 'outbound'): array
    {
        $stops = $direction === 'outbound' ? $this->stopsOutbound : $this->stopsInbound;
        
        return array_map(
            fn(BusStop $stop) => $stop->getCoordinates($direction),
            $stops
        );
    }

    /**
     * Get OSRM route coordinates for the specified direction (real road route)
     * Falls back to stop coordinates if OSRM route is not available
     */
    public function getRouteCoordinates(string $direction = 'outbound'): array
    {
        $osrmRoute = $direction === 'outbound' ? $this->osrmRouteOutbound : $this->osrmRouteInbound;
        
        // Use OSRM route if available, otherwise fall back to stop coordinates
        if (!empty($osrmRoute)) {
            return $osrmRoute;
        }
        
        return $this->getStopCoordinates($direction);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'line' => $this->lineNumber,
            'type' => $this->type,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'color' => $this->color ?? $this->company->primaryColor,
            'is_main_line' => $this->isMainLine,
            'company' => $this->company->code,
            'route_coords_outbound' => $this->getRouteCoordinates('outbound'),
            'route_coords_inbound' => $this->getRouteCoordinates('inbound'),
            'stops_outbound' => array_map(fn(BusStop $s) => $s->name, $this->stopsOutbound),
            'stops_inbound' => array_map(fn(BusStop $s) => $s->name, $this->stopsInbound),
        ];
    }
}
