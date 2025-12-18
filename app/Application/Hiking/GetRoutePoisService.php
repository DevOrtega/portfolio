<?php

namespace App\Application\Hiking;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GetRoutePoisService
{
    private const OVERPASS_URL = 'https://overpass-api.de/api/interpreter';
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Get POIs around a hiking route
     * 
     * @param array $routeCoordinates Array of [lat, lon]
     * @param int $radius Radius in meters (default 1000)
     * @return array List of POIs
     */
    public function execute(array $routeCoordinates, int $radius = 1000): array
    {
        if (empty($routeCoordinates)) {
            return [];
        }

        // 1. Simplify geometry to avoid Overpass limits/timeouts
        // Start with 0.001 (approx 100m)
        $epsilon = 0.001;
        $simplifiedRoute = $this->simplifyRoute($routeCoordinates, $epsilon);
        
        // If still too many points, simplify more aggressively
        // We want to keep the query string manageable. 
        // 5 repetitions of the string in the query * 150 points * 20 chars = ~15KB.
        while (count($simplifiedRoute) > 150 && $epsilon < 0.01) {
            $epsilon *= 2;
            $simplifiedRoute = $this->simplifyRoute($routeCoordinates, $epsilon);
        }

        // Generate cache key based on simplified route + radius
        $cacheKey = 'hiking_pois_' . md5(json_encode($simplifiedRoute) . $radius);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($simplifiedRoute, $radius) {
            return $this->fetchFromOverpass($simplifiedRoute, $radius);
        });
    }

    private function fetchFromOverpass(array $route, int $radius): array
    {
        // Format coordinates for "around" filter: lat1,lon1,lat2,lon2...
        $coordsString = collect($route)
            ->map(fn($p) => "{$p[0]},{$p[1]}")
            ->join(',');

        // Build Query
        // We look for: Food, Water, Views, Shelter, Parking
        $query = <<<QL
[out:json][timeout:25];
(
  node["amenity"~"restaurant|cafe|bar|pub|fast_food"](around:{$radius},{$coordsString});
  way["amenity"~"restaurant|cafe|bar|pub|fast_food"](around:{$radius},{$coordsString});
  
  node["amenity"="drinking_water"](around:{$radius},{$coordsString});
  
  node["tourism"~"viewpoint|picnic_site|camp_site|alpine_hut|wilderness_hut"](around:{$radius},{$coordsString});
  way["tourism"~"viewpoint|picnic_site|camp_site|alpine_hut|wilderness_hut"](around:{$radius},{$coordsString});

  node["amenity"="parking"](around:{$radius},{$coordsString});
  way["amenity"="parking"](around:{$radius},{$coordsString});
  
  node["natural"="peak"](around:{$radius},{$coordsString});
);
out center;
QL;

        try {
            $response = Http::asForm()->post(self::OVERPASS_URL, [
                'data' => $query
            ]);

            if (!$response->successful()) {
                Log::error('Overpass API failed', ['status' => $response->status(), 'body' => $response->body()]);
                return [];
            }

            $data = $response->json();
            return $this->transformPois($data['elements'] ?? []);

        } catch (\Exception $e) {
            Log::error('Overpass connection error', ['message' => $e->getMessage()]);
            return [];
        }
    }

    private function transformPois(array $elements): array
    {
        $pois = [];
        foreach ($elements as $element) {
            $tags = $element['tags'] ?? [];
            $lat = $element['lat'] ?? $element['center']['lat'] ?? null;
            $lon = $element['lon'] ?? $element['center']['lon'] ?? null;

            if (!$lat || !$lon) continue;

            $type = $this->determineType($tags);
            
            // Priority: name tag > specific OSM tag value > generic category
            $name = $tags['name'] ?? $tags['alt_name'] ?? $tags['loc_name'] ?? null;
            
            if (!$name) {
                // For peaks, try to add elevation
                if ($type === 'peak' && isset($tags['ele'])) {
                    $name = "Cima ({$tags['ele']}m)";
                } 
                // For viewpoints
                else if ($type === 'viewpoint') {
                    $name = $tags['description'] ?? $tags['note'] ?? "Mirador";
                }
                else {
                    $specificTag = $tags['amenity'] ?? $tags['tourism'] ?? $tags['natural'] ?? $tags['shop'] ?? null;
                    if ($specificTag && $specificTag !== 'yes') {
                        $name = ucfirst(str_replace('_', ' ', $specificTag));
                    } else {
                        $name = ucfirst(str_replace('_', ' ', $type));
                    }
                }
            }

            $pois[] = [
                'id' => $element['id'],
                'type' => $element['type'], // node/way
                'lat' => $lat,
                'lon' => $lon,
                'name' => $name,
                'category' => $type,
                'tags' => $tags // Keep tags for details if needed
            ];
        }
        return $pois;
    }

    private function determineType(array $tags): string
    {
        if (isset($tags['amenity'])) {
            if (in_array($tags['amenity'], ['restaurant', 'cafe', 'bar', 'pub', 'fast_food'])) return 'food';
            if ($tags['amenity'] === 'drinking_water') return 'water';
            if ($tags['amenity'] === 'parking') return 'parking';
        }
        if (isset($tags['tourism'])) {
            if ($tags['tourism'] === 'viewpoint') return 'viewpoint';
            if ($tags['tourism'] === 'picnic_site') return 'picnic';
            if (in_array($tags['tourism'], ['camp_site', 'alpine_hut', 'wilderness_hut'])) return 'shelter';
        }
        if (isset($tags['natural']) && $tags['natural'] === 'peak') return 'peak';

        return 'other';
    }

    /**
     * Ramer-Douglas-Peucker simplification
     */
    private function simplifyRoute(array $points, float $epsilon): array
    {
        if (count($points) <= 2) {
            return $points;
        }

        // Find the point with the maximum distance from line between start and end
        $dmax = 0;
        $index = 0;
        $end = count($points) - 1;

        for ($i = 1; $i < $end; $i++) {
            $d = $this->perpendicularDistance($points[$i], $points[0], $points[$end]);
            if ($d > $dmax) {
                $index = $i;
                $dmax = $d;
            }
        }

        // If max distance is greater than epsilon, recursively simplify
        if ($dmax > $epsilon) {
            $recResults1 = $this->simplifyRoute(array_slice($points, 0, $index + 1), $epsilon);
            $recResults2 = $this->simplifyRoute(array_slice($points, $index), $epsilon);
            
            // Build the result list
            array_pop($recResults1); // Remove the duplicate point
            return array_merge($recResults1, $recResults2);
        } else {
            return [$points[0], $points[$end]];
        }
    }

    private function perpendicularDistance(array $point, array $lineStart, array $lineEnd): float
    {
        $x = $point[0]; $y = $point[1];
        $x1 = $lineStart[0]; $y1 = $lineStart[1];
        $x2 = $lineEnd[0]; $y2 = $lineEnd[1];

        if ($x1 == $x2 && $y1 == $y2) {
            return sqrt(pow($x - $x1, 2) + pow($y - $y1, 2));
        }

        $num = abs(($y2 - $y1) * $x - ($x2 - $x1) * $y + $x2 * $y1 - $y2 * $x1);
        $den = sqrt(pow($y2 - $y1, 2) + pow($x2 - $x1, 2));

        return $num / $den;
    }
}
