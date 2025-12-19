<?php

namespace App\Domain\Hiking;

use App\Domain\Hiking\ValueObjects\RouteGeometry;

interface PoiProviderInterface
{
    /**
     * Get POIs near a route geometry
     * @return array List of POI data (array for now, ideally POI Value Objects)
     */
    public function getPoisNearRoute(RouteGeometry $route, int $radius): array;
}
