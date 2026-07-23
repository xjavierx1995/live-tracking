<?php

namespace App\Application\Contracts;

use App\Domain\Simulation\GeoPoint;

interface PolylineCodec
{
    /**
     * @param  array<int, GeoPoint>  $points
     */
    public function encode(array $points): string;

    /**
     * @return array<int, GeoPoint>
     */
    public function decode(string $polyline): array;
}
