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

        // Build Query with expanded categories
        $query = <<<QL
[out:json][timeout:25];
(
  // Food & Drink
  node["amenity"~"restaurant|cafe|bar|pub|fast_food"](around:{$radius},{$coordsString});
  way["amenity"~"restaurant|cafe|bar|pub|fast_food"](around:{$radius},{$coordsString});
  
  // Health
  node["amenity"~"pharmacy|hospital|clinic|doctors"](around:{$radius},{$coordsString});
  
  // Culture & Tourism
  node["tourism"~"museum|viewpoint|picnic_site"](around:{$radius},{$coordsString});
  way["tourism"~"museum|viewpoint|picnic_site"](around:{$radius},{$coordsString});

  // Camping & Shelters
  node["tourism"~"camp_site|caravan_site|alpine_hut|wilderness_hut"](around:{$radius},{$coordsString});
  way["tourism"~"camp_site|caravan_site|alpine_hut|wilderness_hut"](around:{$radius},{$coordsString});

  // Accommodation
  node["tourism"~"hotel|hostel|guest_house|chalet|apartment|motel"](around:{$radius},{$coordsString});
  way["tourism"~"hotel|hostel|guest_house|chalet|apartment|motel"](around:{$radius},{$coordsString});

  // Transport & Basics
  node["amenity"~"parking|drinking_water"](around:{$radius},{$coordsString});
  way["amenity"="parking"](around:{$radius},{$coordsString});
  
  // Natural
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

            $category = $this->determineCategory($tags);
            
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
                if ($category === 'peak' && isset($tags['ele'])) {
                    $name = "Cima ({$tags['ele']}m)";
                } 
                else if ($category === 'viewpoint') {
                    $name = $tags['description'] ?? $tags['note'] ?? "Mirador";
                }
                else {
                    $specificTag = $tags['amenity'] ?? $tags['tourism'] ?? $tags['natural'] ?? $tags['shop'] ?? null;
                    if ($specificTag && $specificTag !== 'yes') {
                        $name = ucfirst(str_replace('_', ' ', $specificTag));
                    } else {
                        $name = ucfirst(str_replace('_', ' ', $category));
                    }
                }
            }

            $pois[] = [
                'id' => $element['id'],
                'type' => $element['type'],
                'lat' => $lat,
                'lon' => $lon,
                'name' => $name,
                'category' => $category,
                'tags' => $tags,
                'relevance' => $this->calculateRelevance($tags)
            ];
        }
        return $pois;
    }

    private function determineCategory(array $tags): string
    {
        if (isset($tags['amenity'])) {
            $a = $tags['amenity'];
            if (in_array($a, ['restaurant', 'cafe', 'bar', 'pub', 'fast_food'])) return 'food';
            if ($a === 'pharmacy') return 'health';
            if (in_array($a, ['hospital', 'clinic', 'doctors'])) return 'health';
            if ($a === 'drinking_water') return 'water';
            if ($a === 'parking') return 'parking';
        }
        if (isset($tags['tourism'])) {
            $t = $tags['tourism'];
            if ($t === 'viewpoint') return 'viewpoint';
            if ($t === 'picnic_site') return 'picnic';
            if ($t === 'museum') return 'culture';
            if (in_array($t, ['camp_site', 'caravan_site'])) return 'camping';
            if (in_array($t, ['alpine_hut', 'wilderness_hut'])) return 'shelter';
            if (in_array($t, ['hotel', 'hostel', 'guest_house', 'chalet', 'apartment', 'motel'])) return 'accommodation';
        }
        if (isset($tags['natural']) && $tags['natural'] === 'peak') return 'peak';

        return 'other';
    }

    private function calculateRelevance(array $tags): int
    {
        $score = 0;
        if (isset($tags['name'])) $score += 10;
        if (isset($tags['wikidata']) || isset($tags['wikipedia'])) $score += 20;
        if (isset($tags['stars'])) $score += (int) $tags['stars'] * 5;
        if (isset($tags['award:michelin'])) $score += 30;
        if (isset($tags['website']) || isset($tags['contact:website'])) $score += 5;
        if (isset($tags['phone']) || isset($tags['contact:phone'])) $score += 5;
        if (isset($tags['opening_hours'])) $score += 2;
        
        return $score;
    }
}
