<?php

namespace App\Domain\Bus\Entities;

/**
 * Domain Entity: BusStop
 * 
 * Represents a bus stop with coordinates for both directions.
 */
final readonly class BusStop
{
    public function __construct(
        public int $id,
        public string $code,
        public string $name,
        public ?string $zone,
        public float $latOutbound,
        public float $lngOutbound,
        public float $latInbound,
        public float $lngInbound
    ) {
    }

    /**
     * Get coordinates for the specified direction
     */
    public function getCoordinates(string $direction = 'outbound'): array
    {
        return $direction === 'outbound' 
            ? [$this->latOutbound, $this->lngOutbound]
            : [$this->latInbound, $this->lngInbound];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'zone' => $this->zone,
            'coordinates' => [
                'outbound' => [$this->latOutbound, $this->lngOutbound],
                'inbound' => [$this->latInbound, $this->lngInbound],
            ],
        ];
    }
}
