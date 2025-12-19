<?php

namespace App\Infrastructure\Services;

use App\Domain\Hiking\PoiProviderInterface;
use App\Domain\Hiking\ValueObjects\RouteGeometry;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OverpassPoiProvider implements PoiProviderInterface
{
    private const OVERPASS_URL = 'https://overpass-api.de/api/interpreter';
    private const CACHE_TTL = 3600;

    public function getPoisNearRoute(RouteGeometry $route, int $radius): array
    {
        // Cache key based on simplified route string + radius
        $cacheKey = 'hiking_pois_' . md5($route->toString() . $radius);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($route, $radius) {
            return $this->fetchFromOverpass($route, $radius);
        });
    }

    private function fetchFromOverpass(RouteGeometry $route, int $radius): array
    {
        $coordsString = $route->toString();

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
            
            // Comprehensive name priority list
            $name = $tags['name'] 
                 ?? $tags['alt_name'] 
                 ?? $tags['loc_name'] 
                 ?? $tags['short_name']
                 ?? $tags['old_name'] 
                 ?? $tags['int_name']
                 ?? $tags['official_name']
                 ?? null;
            
            if (!$name) {
                if ($type === 'peak' && isset($tags['ele'])) {
                    $name = "Cima ({$tags['ele']}m)";
                } 
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
                'type' => $element['type'],
                'lat' => $lat,
                'lon' => $lon,
                'name' => $name,
                'category' => $type,
                'tags' => $tags 
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
}
