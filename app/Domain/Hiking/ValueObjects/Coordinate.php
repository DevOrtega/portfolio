<?php

namespace App\Domain\Hiking\ValueObjects;

use InvalidArgumentException;

final class Coordinate
{
    public function __construct(
        public readonly float $latitude,
        public readonly float $longitude
    ) {
        if ($this->latitude < -90 || $this->latitude > 90) {
            throw new InvalidArgumentException("Latitude must be between -90 and 90. Got {$this->latitude}");
        }
        if ($this->longitude < -180 || $this->longitude > 180) {
            throw new InvalidArgumentException("Longitude must be between -180 and 180. Got {$this->longitude}");
        }
    }

    public static function fromLatLon(float $lat, float $lon): self
    {
        return new self($lat, $lon);
    }

    /**
     * Create from GeoJSON format [longitude, latitude, elevation?]
     */
    public static function fromGeoJson(array $coords): self
    {
        if (count($coords) < 2) {
            throw new InvalidArgumentException("Invalid coordinate array");
        }
        return new self($coords[1], $coords[0]);
    }

    /**
     * Create from standard [latitude, longitude]
     */
    public static function fromArray(array $coords): self
    {
        if (count($coords) < 2) {
            throw new InvalidArgumentException("Invalid coordinate array");
        }
        return new self($coords[0], $coords[1]);
    }

    /**
     * Format as "lat,lon" string using dot as decimal separator
     */
    public function toString(): string
    {
        return sprintf("%.6F,%.6F", $this->latitude, $this->longitude);
    }

    /**
     * Returns [lat, lon]
     */
    public function toArray(): array
    {
        return [$this->latitude, $this->longitude];
    }
}
