<?php

namespace App\Domain\Hiking;

interface RouteProviderInterface
{
    /**
     * Get route between coordinates.
     * 
     * @param array $coordinates Array of [lat, lng]
     * @param string $profile
     * @param array $options
     * @return array Raw route data (implementation specific, usually GeoJSON based)
     */
    public function getRoutesWithOptions(array $coordinates, string $profile = 'driving', array $options = []): array;
}
