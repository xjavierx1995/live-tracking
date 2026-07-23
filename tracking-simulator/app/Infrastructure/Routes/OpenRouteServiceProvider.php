<?php

namespace App\Infrastructure\Routes;

use App\Application\Contracts\PolylineCodec;
use App\Application\Contracts\RouteProvider;
use App\Application\Exceptions\RouteProviderException;
use App\Domain\Simulation\GeoPoint;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class OpenRouteServiceProvider implements RouteProvider
{
    public function __construct(private readonly PolylineCodec $polylineCodec)
    {
    }

    public function getEncodedRoute(GeoPoint $origin, GeoPoint $destination): string
    {
        $apiKey = (string) config('services.openrouteservice.api_key', '');

        if ($apiKey === '') {
            return $this->buildSyntheticRoute($origin, $destination);
        }

        $baseUrl = (string) config('services.openrouteservice.base_url', 'https://api.openrouteservice.org');

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$baseUrl}/v2/directions/driving-car", [
                    'coordinates' => [
                        [$origin->longitude, $origin->latitude],
                        [$destination->longitude, $destination->latitude],
                    ],
                ]);
        } catch (ConnectionException) {
            throw new RouteProviderException('No fue posible consultar OpenRouteService.', 503);
        }

        if (! $response->successful()) {
            throw new RouteProviderException('OpenRouteService rechazo la solicitud de ruta.', 503);
        }

        $polyline = data_get($response->json(), 'routes.0.geometry');

        if (! is_string($polyline) || $polyline === '') {
            throw new RouteProviderException('OpenRouteService no retorno una polyline utilizable.', 503);
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
