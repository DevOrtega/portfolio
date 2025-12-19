<?php

namespace App\Domain\Hiking\ValueObjects;

use InvalidArgumentException;

final class RouteGeometry
{
    /** @var Coordinate[] */
    public readonly array $coordinates;

    public function __construct(array $coordinates)
    {
        $this->coordinates = array_map(function ($c) {
            return $c instanceof Coordinate ? $c : throw new InvalidArgumentException("Items must be Coordinate instances");
        }, $coordinates);
    }

    public static function fromGeoJsonArray(array $geoJsonCoords): self
    {
        $coords = array_map(fn($c) => Coordinate::fromGeoJson($c), $geoJsonCoords);
        return new self($coords);
    }

    /**
     * Simplify the route using Ramer-Douglas-Peucker algorithm
     */
    public function simplify(float $epsilon = 0.001, int $maxPoints = 150): self
    {
        // Dynamic epsilon adjustment if needed
        $simplified = $this->runRDP($this->coordinates, $epsilon);
        
        while (count($simplified) > $maxPoints && $epsilon < 0.01) {
            $epsilon *= 2;
            $simplified = $this->runRDP($this->coordinates, $epsilon);
        }

        return new self($simplified);
    }

    private function runRDP(array $points, float $epsilon): array
    {
        if (count($points) <= 2) {
            return $points;
        }

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

        if ($dmax > $epsilon) {
            $recResults1 = $this->runRDP(array_slice($points, 0, $index + 1), $epsilon);
            $recResults2 = $this->runRDP(array_slice($points, $index), $epsilon);
            
            array_pop($recResults1);
            return array_merge($recResults1, $recResults2);
        } else {
            return [$points[0], $points[$end]];
        }
    }

    private function perpendicularDistance(Coordinate $point, Coordinate $lineStart, Coordinate $lineEnd): float
    {
        $x = $point->latitude; $y = $point->longitude;
        $x1 = $lineStart->latitude; $y1 = $lineStart->longitude;
        $x2 = $lineEnd->latitude; $y2 = $lineEnd->longitude;

        if ($x1 == $x2 && $y1 == $y2) {
            return sqrt(pow($x - $x1, 2) + pow($y - $y1, 2));
        }

        $num = abs(($y2 - $y1) * $x - ($x2 - $x1) * $y + $x2 * $y1 - $y2 * $x1);
        $den = sqrt(pow($y2 - $y1, 2) + pow($x2 - $x1, 2));

        return $num / $den;
    }

    public function toString(): string
    {
        return collect($this->coordinates)
            ->map(fn(Coordinate $c) => $c->toString())
            ->join(',');
    }
    
    public function toArray(): array
    {
        return array_map(fn(Coordinate $c) => $c->toArray(), $this->coordinates);
    }
}
