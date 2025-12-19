<?php

namespace App\Application\Hiking;

use App\Domain\Hiking\PoiProviderInterface;
use App\Domain\Hiking\ValueObjects\Coordinate;
use App\Domain\Hiking\ValueObjects\RouteGeometry;

final readonly class GetRoutePoisService
{
    public function __construct(
        private PoiProviderInterface $poiProvider
    ) {}

    /**
     * Get POIs around a hiking route
     * 
     * @param array $routeCoordinates Array of [lat, lon]
     * @param int $radius Radius in meters
     * @return array List of POIs
     */
    public function execute(array $routeCoordinates, int $radius = 1000): array
    {
        if (empty($routeCoordinates)) {
            return [];
        }

        // Create RouteGeometry from raw coordinates
        // Assuming input is [lat, lon] as per previous contract
        $coords = array_map(fn($c) => Coordinate::fromArray($c), $routeCoordinates);
        $route = new RouteGeometry($coords);

        // Simplify geometry to avoid limits
        // Start with 0.001 (approx 100m)
        $simplifiedRoute = $route->simplify(0.001);

        return $this->poiProvider->getPoisNearRoute($simplifiedRoute, $radius);
    }
}

