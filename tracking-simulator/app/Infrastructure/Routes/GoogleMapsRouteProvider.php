<?php

namespace App\Infrastructure\Routes;

use App\Application\Contracts\PolylineCodec;
use App\Application\Contracts\RouteProvider;
use App\Application\Exceptions\RouteProviderException;
use App\Domain\Simulation\GeoPoint;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GoogleMapsRouteProvider implements RouteProvider
{
    public function __construct(private readonly PolylineCodec $polylineCodec)
    {
    }

    public function getEncodedRoute(GeoPoint $origin, GeoPoint $destination): string
    {
        $apiKey = (string) config('services.google_maps.api_key', '');

        if ($apiKey === '') {
            return $this->buildSyntheticRoute($origin, $destination);
        }

        try {
            $response = Http::timeout(10)->get((string) config('services.google_maps.directions_url'), [
                'origin' => sprintf('%F,%F', $origin->latitude, $origin->longitude),
                'destination' => sprintf('%F,%F', $destination->latitude, $destination->longitude),
                'mode' => 'driving',
                'key' => $apiKey,
            ]);
        } catch (ConnectionException) {
            throw new RouteProviderException('No fue posible consultar Google Maps.', 503);
        }

        if (! $response->successful()) {
            throw new RouteProviderException('Google Maps rechazo la solicitud de ruta.', 503);
        }

        $polyline = data_get($response->json(), 'routes.0.overview_polyline.points');

        if (! is_string($polyline) || $polyline === '') {
            throw new RouteProviderException('Google Maps no retorno una polyline utilizable.', 503);
        }

        return $polyline;
    }

    private function buildSyntheticRoute(GeoPoint $origin, GeoPoint $destination): string
    {
        $points = [];
        $segments = 12;

        for ($index = 0; $index <= $segments; $index++) {
            $ratio = $segments === 0 ? 0.0 : $index / $segments;
            $curve = sin($ratio * M_PI) * 0.0025;

            $points[] = new GeoPoint(
                $origin->latitude + (($destination->latitude - $origin->latitude) * $ratio) + $curve,
                $origin->longitude + (($destination->longitude - $origin->longitude) * $ratio) - $curve,
            );
        }

        return $this->polylineCodec->encode($points);
    }
}
