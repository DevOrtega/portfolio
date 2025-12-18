<?php

namespace App\Application\Hiking;

use App\Domain\Hiking\ElevationProviderInterface;
use App\Domain\Hiking\RouteProviderInterface;

final readonly class GetHikingRouteService
{
    public function __construct(
        private RouteProviderInterface $routeProvider,
        private ElevationProviderInterface $elevationProvider
    ) {}

    /**
     * Calculate hiking routes between points (start, end, and optional waypoints) with elevation profiles.
     * Returns a FeatureCollection with multiple alternative routes.
     *
     * @param array $start [lat, lng]
     * @param array $end [lat, lng]
     * @param array|null $waypoints Array of [lat, lng] intermediate points
     * @return array GeoJSON FeatureCollection
     */
    public function execute(array $start, array $end, ?array $waypoints = null): array
    {
        // Build coordinates array: Start -> Waypoints -> End
        $coordinates = [$start];
        
        if ($waypoints) {
            foreach ($waypoints as $wp) {
                $coordinates[] = $wp;
            }
        }
        
        $coordinates[] = $end;

        // 1. Get Routes from OSRM with alternatives
        // We request 3 alternatives explicitly and enable steps for instructions.
        $rawRoutes = $this->routeProvider->getRoutesWithOptions($coordinates, 'foot', [
            'alternatives' => 3,
            'steps' => 'true'
        ]);
        
        $features = [];

        foreach ($rawRoutes as $index => $routeData) {
            $route2D = $routeData['geometry']['coordinates'];
            
            // 2. Add Elevation to each route
            $route3D = $this->elevationProvider->addElevation($route2D);
            
            // 3. Calculate statistics
            $stats = $this->calculateStats($route3D);
            
            // 4. Determine Difficulty
            $stats['difficulty'] = $this->calculateDifficulty($stats['distance_km'], $stats['elevation_gain_m']);
            $stats['route_index'] = $index;
            // OSRM provides 'duration' in seconds and 'distance' in meters
            $stats['osrm_time_min'] = round(($routeData['duration'] ?? 0) / 60);
            
            // Include legs/steps for instructions
            $stats['legs'] = $routeData['legs'] ?? [];

            $features[] = [
                'type' => 'Feature',
                'id' => $index,
                'properties' => $stats,
                'geometry' => [
                    'type' => 'LineString',
                    'coordinates' => array_map(fn($c) => [$c[1], $c[0], $c[2]], $route3D) // [lon, lat, ele]
                ]
            ];
        }
        
        return [
            'type' => 'FeatureCollection',
            'features' => $features
        ];
    }

    private function calculateDifficulty(float $distanceKm, float $gainM): string
    {
        // Simple heuristic (can be improved with MIDE method)
        if ($gainM > 800 || $distanceKm > 15) {
            return 'Difícil';
        }
        if ($gainM > 400 || $distanceKm > 8) {
            return 'Moderada';
        }
        return 'Fácil';
    }

    private function calculateStats(array $route3D): array
    {
        $totalDistance = 0;
        $elevationGain = 0;
        $elevationLoss = 0;
        $maxElevation = -9999;
        $minElevation = 9999;
        
        for ($i = 0; $i < count($route3D) - 1; $i++) {
            $p1 = $route3D[$i];
            $p2 = $route3D[$i+1];
            
            // Distance (Haversine or simple approximation for short segments)
            $dist = $this->distance($p1[0], $p1[1], $p2[0], $p2[1]);
            $totalDistance += $dist;
            
            $eleDiff = $p2[2] - $p1[2];
            if ($eleDiff > 0) {
                $elevationGain += $eleDiff;
            } else {
                $elevationLoss += abs($eleDiff);
            }
            
            $maxElevation = max($maxElevation, $p1[2], $p2[2]);
            $minElevation = min($minElevation, $p1[2], $p2[2]);
        }
        
        return [
            'distance_km' => round($totalDistance / 1000, 2),
            'elevation_gain_m' => round($elevationGain, 0),
            'elevation_loss_m' => round($elevationLoss, 0),
            'max_elevation_m' => round($maxElevation, 0),
            'min_elevation_m' => round($minElevation, 0),
        ];
    }

    private function distance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}