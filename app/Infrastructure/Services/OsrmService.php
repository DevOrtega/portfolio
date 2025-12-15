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
    private const OSRM_BASE_URL = 'https://router.project-osrm.org/route/v1';
    private const CACHE_TTL = 86400; // 24 hours in seconds
    private const MAX_WAYPOINTS = 25; // OSRM limit for free tier
    
    /**
     * Get real road route between waypoints
     * 
     * @param array $coordinates Array of [lat, lng] coordinates
     * @param string $profile OSRM profile (driving, foot, bicycle)
     * @return array Route coordinates following real roads
     */
    public function getRoute(array $coordinates, string $profile = 'driving'): array
    {
        if (count($coordinates) < 2) {
            return $coordinates;
        }
        
        // Create cache key from coordinates and profile
        $cacheKey = 'osrm_route_' . $profile . '_' . md5(json_encode($coordinates));
        
        // Try to get from cache
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        // Fetch from API
        $route = $this->fetchRoute($coordinates, $profile);
        
        // Only cache if we got a real route (more points than input, or at least successful response)
        // If we got fallback (same count as input), do not cache (or cache for short time)
        // But some routes might be straight lines? Unlikely for buses.
        // Let's assume if it returns same coords as input, it failed or is trivial.
        // To be safe, we check if fetchRoute found a real route.
        // Actually fetchRoute returns inputs on failure.
        
        if ($route !== $coordinates) {
            Cache::put($cacheKey, $route, self::CACHE_TTL);
        }
        
        return $route;
    }
    
    /**
     * Get routes with custom options (e.g. alternatives)
     * Returns the raw 'routes' array from OSRM response.
     */
    public function getRoutesWithOptions(array $coordinates, string $profile = 'driving', array $options = []): array
    {
        if (count($coordinates) < 2) {
            return [];
        }

        // Cache key includes options
        $cacheKey = 'osrm_raw_' . $profile . '_' . md5(json_encode($coordinates) . json_encode($options));
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($coordinates, $profile, $options) {
            $server = config('services.osrm.server') ?? env('OSRM_SERVER');
            
            if (!$server) {
                 // Default to standard OSRM or localhost if developing with local OSRM
                 $baseUrl = self::OSRM_BASE_URL . '/' . $profile;
            } else {
                 // Ensure we handle trailing slashes and profiles correctly
                 // If server is "http://osrm:5000/route/v1", append profile
                 $baseUrl = rtrim($server, '/') . '/' . $profile;
            }

            try {
                $waypoints = $this->reduceWaypoints($coordinates, self::MAX_WAYPOINTS);
                
                $coordString = collect($waypoints)
                    ->map(fn($coord) => $coord[1] . ',' . $coord[0])
                    ->join(';');
                
                // Merge default options with user options
                $queryParams = array_merge([
                    'overview' => 'full',
                    'geometries' => 'geojson',
                ], $options);

                $url = $baseUrl . '/' . $coordString . '?' . http_build_query($queryParams);
                
                Log::debug('OSRM raw request', ['url' => $url]);
                
                $response = Http::timeout(10)->get($url);
                
                if (!$response->successful()) {
                    return [];
                }
                
                $data = $response->json();
                
                if (($data['code'] ?? '') !== 'Ok' || empty($data['routes'])) {
                    return [];
                }

                // Transform geometries in all routes from [lng, lat] to [lat, lng]
                $routes = $data['routes'];
                foreach ($routes as &$route) {
                    $route['geometry']['coordinates'] = array_map(
                        fn($coord) => [$coord[1], $coord[0]],
                        $route['geometry']['coordinates']
                    );
                }
                
                return $routes;

            } catch (\Exception $e) {
                Log::error('OSRM raw error', ['message' => $e->getMessage()]);
                return [];
            }
        });
    }

    /**
     * Fetch route from OSRM API
     */
    private function fetchRoute(array $coordinates, string $profile): array
    {
        $server = config('services.osrm.server') ?? env('OSRM_SERVER');
        
        if (!$server) {
             $baseUrl = self::OSRM_BASE_URL . '/' . $profile;
        } else {
             // Handle custom server logic if needed, or assume standard structure
             $baseUrl = rtrim($server, '/') . '/route/v1/' . $profile;
        }

        try {
            // Reduce waypoints if too many (take key points)
            $waypoints = $this->reduceWaypoints($coordinates, self::MAX_WAYPOINTS);
            
            // OSRM expects format: lng,lat;lng,lat;...
            $coordString = collect($waypoints)
                ->map(fn($coord) => $coord[1] . ',' . $coord[0]) // [lat,lng] -> lng,lat
                ->join(';');
            
            // Clean URL to remove double slashes if any, but preserve protocol
            $url = $baseUrl . '/' . $coordString . '?overview=full&geometries=geojson';
            
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