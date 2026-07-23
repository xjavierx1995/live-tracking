<?php

namespace App\Application\Contracts;

use App\Domain\Simulation\GeoPoint;

interface RouteProvider
{
    public function getEncodedRoute(GeoPoint $origin, GeoPoint $destination): string;
}
