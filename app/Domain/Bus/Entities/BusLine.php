<?php

namespace App\Domain\Bus\Entities;

/**
 * Domain Entity: BusLine
 * 
 * Represents a bus line with its route information.
 */
final readonly class BusLine
{

    public function __construct(
        public int $id,
        public string $lineNumber,
        public string $type,
        public string $origin,
        public string $destination,
        public ?string $color,
        public bool $isMainLine,
        public BusCompany $company,
        public array $stopsOutbound, // Array of BusStop
        public array $stopsInbound,  // Array of BusStop
        public array $osrmRouteOutbound, // Array of [lat, lon]
        public array $osrmRouteInbound   // Array of [lat, lon]
    ) {
    }
    /**
     * Create interpolated points between stops for smoother routes
     */
    private function interpolateRoute(array $stopCoords): array
    {
        if (count($stopCoords) < 2) {
            return $stopCoords;
        }
        
        $interpolated = [];
        for ($i = 0; $i < count($stopCoords) - 1; $i++) {
            $interpolated[] = $stopCoords[$i];
            
            // Add intermediate points between stops
            $start = $stopCoords[$i];
            $end = $stopCoords[$i + 1];
            $distance = $this->haversineDistance($start, $end);
            
            // If distance is large, add intermediate points
            if ($distance > 0.5) { // > 500m
                $segments = min(5, ceil($distance / 0.2)); // point every 200m max
                for ($j = 1; $j < $segments; $j++) {
                    $ratio = $j / $segments;
                    $interpolated[] = [
                        $start[0] + ($end[0] - $start[0]) * $ratio,
                        $start[1] + ($end[1] - $start[1]) * $ratio
                    ];
                }
            }
        }
        $interpolated[] = $stopCoords[count($stopCoords) - 1];
        
        return $interpolated;
    }
    
    /**
     * Calculate Haversine distance between two points
     */
    private function haversineDistance(array $point1, array $point2): float
    {
        $lat1 = deg2rad($point1[0]);
        $lon1 = deg2rad($point1[1]);
        $lat2 = deg2rad($point2[0]);
        $lon2 = deg2rad($point2[1]);
        
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;
        
        $a = sin($dlat/2) * sin($dlat/2) + cos($lat1) * cos($lat2) * sin($dlon/2) * sin($dlon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return 6371000 * $c; // Earth radius in meters
    }
    
    /**
     * Create direct route between origin and destination (last resort)
     */
    private function createDirectRoute(string $direction): array
    {
        $stops = $direction === 'outbound' ? $this->stopsOutbound : $this->stopsInbound;
        
        if (count($stops) < 2) {
            return [];
        }
        
        $first = $stops[0]->getCoordinates($direction);
        $last = $stops[count($stops) - 1]->getCoordinates($direction);
        
        if (empty($first) || empty($last)) {
            return [];
        }
        
        // Create intermediate points for direct route
        return $this->interpolateRoute([$first, $last]);
    }

    /**
     * Get OSRM route coordinates for the specified direction (real road route)
     * Falls back to stop coordinates if OSRM route is not available
     */
    public function getRouteCoordinates(string $direction = 'outbound'): array
    {
        $osrmRoute = $direction === 'outbound' ? $this->osrmRouteOutbound : $this->osrmRouteInbound;
        
        // Use OSRM route if available and has sufficient points
        if (!empty($osrmRoute) && count($osrmRoute) >= 2) {
            return $osrmRoute;
        }
        
        // Fallback 1: Use stop coordinates with interpolation
        $stopCoords = $this->getStopCoordinates($direction);
        if (count($stopCoords) >= 2) {
            return $this->interpolateRoute($stopCoords);
        }
        
        // Fallback 2: Try manual route
        $manualRoute = $this->getManualRoute($direction);
        if (!empty($manualRoute)) {
            return $manualRoute;
        }
        
        // Fallback 3: Direct route between origin and destination
        return $this->createDirectRoute($direction);
    }
    /**
     * Get stop coordinates for specified direction
     */
    private function getStopCoordinates(string $direction): array
    {
        $stops = $direction === 'outbound' ? $this->stopsOutbound : $this->stopsInbound;
        return array_map(fn(BusStop $stop) => $stop->getCoordinates($direction), $stops);
    }

    /**
     * Get manual route coordinates (fallback)
     */
    private function getManualRoute(string $direction): array
    {
        // Implementation for manual route fallback
        // For now, return empty array - this can be enhanced later
        return [];
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
            'stops_outbound' => array_map(fn(BusStop $s) => $s->code, $this->stopsOutbound),
            'stops_inbound' => array_map(fn(BusStop $s) => $s->code, $this->stopsInbound),
        ];
    }
}
