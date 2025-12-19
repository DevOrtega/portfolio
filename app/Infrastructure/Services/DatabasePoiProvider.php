<?php

namespace App\Infrastructure\Services;

use App\Domain\Hiking\PoiProviderInterface;
use App\Domain\Hiking\ValueObjects\RouteGeometry;
use Illuminate\Support\Facades\DB;

class DatabasePoiProvider implements PoiProviderInterface
{
    public function getPoisNearRoute(RouteGeometry $route, int $radius): array
    {
        // 1. Calculate Bounding Box of the route
        $points = $route->coordinates;
        $minLat = 90; $maxLat = -90;
        $minLon = 180; $maxLon = -180;

        foreach ($points as $p) {
            $lat = $p->latitude;
            $lon = $p->longitude;
            if ($lat < $minLat) $minLat = $lat;
            if ($lat > $maxLat) $maxLat = $lat;
            if ($lon < $minLon) $minLon = $lon;
            if ($lon > $maxLon) $maxLon = $lon;
        }

        // Add radius buffer (approximate 1 degree ~ 111km)
        $bufferDeg = $radius / 111000;
        $minLat -= $bufferDeg;
        $maxLat += $bufferDeg;
        $minLon -= $bufferDeg; // Cosine correction omitted for simplicity as we check distance later
        $maxLon += $bufferDeg;

        // 2. Fetch candidates from DB
        $candidates = DB::table('pois')
            ->whereBetween('lat', [$minLat, $maxLat])
            ->whereBetween('lon', [$minLon, $maxLon])
            ->get();

        // 3. Filter by distance to Route
        $pois = [];
        foreach ($candidates as $candidate) {
            $lat = (float) $candidate->lat;
            $lon = (float) $candidate->lon;

            // Simple point-to-segment distance check is expensive for complex routes
            // Since route is simplified, we can check against vertices or segments
            // For now, let's use the simplest approach: check distance to any vertex < radius
            // To be more precise, we should check distance to polyline.
            
            // Optimization: First check if near any vertex (fastest)
            if ($this->isNearRoute($lat, $lon, $points, $radius)) {
                $pois[] = [
                    'id' => $candidate->osm_id,
                    'type' => $candidate->osm_type,
                    'lat' => $lat,
                    'lon' => $lon,
                    'name' => $candidate->name,
                    'category' => $candidate->category,
                    'tags' => json_decode($candidate->tags, true),
                    'relevance' => $candidate->relevance
                ];
            }
        }

        return $pois;
    }

    private function isNearRoute(float $lat, float $lon, array $routePoints, int $radius): bool
    {
        // Check distance to segments
        for ($i = 0; $i < count($routePoints) - 1; $i++) {
            $p1 = $routePoints[$i];
            $p2 = $routePoints[$i+1];
            
            $dist = $this->distanceToSegment(
                $lat, $lon, 
                $p1->latitude, $p1->longitude, 
                $p2->latitude, $p2->longitude
            );

            if ($dist <= $radius) {
                return true;
            }
        }
        return false;
    }

    private function distanceToSegment($lat, $lon, $lat1, $lon1, $lat2, $lon2)
    {
        // Haversine distance logic or equirectangular approximation for small distances
        // Let's use simple projected distance (Equirectangular) for speed
        // x = (lon2 - lon1) * cos((lat1 + lat2) / 2)
        // y = lat2 - lat1
        // d = sqrt(x*x + y*y) * R
        
        $R = 6371000;
        
        $x = ($lon - $lon1) * cos(deg2rad(($lat + $lat1) / 2));
        $y = $lat - $lat1;
        
        // Project point onto line segment
        // ... (Math is complex for segment distance in spherical coords)
        
        // Let's stick to simple "distance to closest vertex" if we want super speed, 
        // but for routes, segments are important.
        
        // Implementation of minimum distance from point to line segment
        // Converting to meters locally
        
        $x_p = $lon * 111320 * cos(deg2rad($lat));
        $y_p = $lat * 110574;
        
        $x_1 = $lon1 * 111320 * cos(deg2rad($lat1));
        $y_1 = $lat1 * 110574;
        
        $x_2 = $lon2 * 111320 * cos(deg2rad($lat2));
        $y_2 = $lat2 * 110574;
        
        $l2 = pow($x_2 - $x_1, 2) + pow($y_2 - $y_1, 2);
        if ($l2 == 0) return sqrt(pow($x_p - $x_1, 2) + pow($y_p - $y_1, 2));
        
        $t = (($x_p - $x_1) * ($x_2 - $x_1) + ($y_p - $y_1) * ($y_2 - $y_1)) / $l2;
        $t = max(0, min(1, $t));
        
        $x_proj = $x_1 + $t * ($x_2 - $x_1);
        $y_proj = $y_1 + $t * ($y_2 - $y_1);
        
        return sqrt(pow($x_p - $x_proj, 2) + pow($y_p - $y_proj, 2));
    }
}
