<?php

namespace App\Infrastructure\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service for getting real road routes from OSRM (Open Source Routing Machine)
 * 
 * Provides caching layer for OSRM API calls to avoid rate limiting.
 * Routes are cached for 24 hours.
 */
final readonly class OsrmService
{
    private const OSRM_URL = 'https://router.project-osrm.org/route/v1/driving';
    private const CACHE_TTL = 86400; // 24 hours in seconds
    private const MAX_WAYPOINTS = 25; // OSRM limit for free tier
    
    /**
     * Get real road route between waypoints
     * 
     * @param array $coordinates Array of [lat, lng] coordinates
     * @return array Route coordinates following real roads
     */
    public function getRoute(array $coordinates): array
    {
        if (count($coordinates) < 2) {
            return $coordinates;
        }
        
        // Create cache key from coordinates
        $cacheKey = 'osrm_route_' . md5(json_encode($coordinates));
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($coordinates) {
            return $this->fetchRoute($coordinates);
        });
    }
    
    /**
     * Fetch route from OSRM API
     */
    private function fetchRoute(array $coordinates): array
    {
        try {
            // Reduce waypoints if too many (take key points)
            $waypoints = $this->reduceWaypoints($coordinates, self::MAX_WAYPOINTS);
            
            // OSRM expects format: lng,lat;lng,lat;...
            $coordString = collect($waypoints)
                ->map(fn($coord) => $coord[1] . ',' . $coord[0]) // [lat,lng] -> lng,lat
                ->join(';');
            
            $url = self::OSRM_URL . '/' . $coordString . '?overview=full&geometries=geojson';
            
            Log::debug('OSRM request', ['url' => $url, 'waypoints_count' => count($waypoints)]);
            
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                Log::warning('OSRM request failed', [
                    'status' => $response->status(),
                    'url' => $url,
                ]);
                return $coordinates;
            }
            
            $data = $response->json();
            
            if (($data['code'] ?? '') !== 'Ok' || empty($data['routes'][0]['geometry']['coordinates'])) {
                Log::warning('OSRM returned no route', ['code' => $data['code'] ?? 'none', 'message' => $data['message'] ?? 'none']);
                return $coordinates;
            }
            
            // Convert GeoJSON [lng, lat] to [lat, lng]
            $routeCoords = array_map(
                fn($coord) => [$coord[1], $coord[0]],
                $data['routes'][0]['geometry']['coordinates']
            );
            
            Log::debug('OSRM route success', [
                'input_count' => count($coordinates),
                'output_count' => count($routeCoords),
                'first' => $routeCoords[0] ?? null,
                'last' => $routeCoords[count($routeCoords) - 1] ?? null,
            ]);
            
            return count($routeCoords) > 1 ? $routeCoords : $coordinates;
            
        } catch (\Exception $e) {
            Log::error('OSRM error', [
                'message' => $e->getMessage(),
            ]);
            return $coordinates;
        }
    }
    
    /**
     * Reduce number of waypoints to stay within limits
     */
    private function reduceWaypoints(array $coordinates, int $maxPoints): array
    {
        $count = count($coordinates);
        
        if ($count <= $maxPoints) {
            return $coordinates;
        }
        
        // Always include first and last
        $result = [$coordinates[0]];
        
        // Take evenly spaced points in between
        $step = ($count - 1) / ($maxPoints - 1);
        
        for ($i = 1; $i < $maxPoints - 1; $i++) {
            $index = (int) round($i * $step);
            $result[] = $coordinates[$index];
        }
        
        $result[] = $coordinates[$count - 1];
        
        return $result;
    }
    
    /**
     * Pre-compute routes for all lines (can be called from a command)
     */
    public function warmCache(array $allRoutes): void
    {
        foreach ($allRoutes as $route) {
            if (!empty($route['stops_outbound'])) {
                $this->getRoute($route['stops_outbound']);
            }
            if (!empty($route['stops_inbound'])) {
                $this->getRoute($route['stops_inbound']);
            }
        }
    }
}
