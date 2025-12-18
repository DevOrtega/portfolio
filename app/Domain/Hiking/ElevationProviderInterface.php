<?php

namespace App\Domain\Hiking;

interface ElevationProviderInterface
{
    /**
     * Add elevation to 2D coordinates.
     * 
     * @param array $coordinates Array of [lat, lng]
     * @return array Array of [lat, lng, elevation]
     */
    public function addElevation(array $coordinates): array;
}
