<?php

namespace App\Application\UseCases;

use App\Application\Contracts\RouteProvider;
use App\Domain\Simulation\GeoPoint;
use App\Models\Service;
use Illuminate\Support\Carbon;

class GenerateServicesAction
{
    public function __construct(private readonly RouteProvider $routeProvider)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(int $count): array
    {
        $services = [];

        for ($index = 0; $index < $count; $index++) {
            $startTime = Carbon::now()->addMinutes(random_int(-120, 120));
            $endTime = (clone $startTime)->addMinutes(random_int(30, 180));
            $origin = $this->randomPoint();
            $destination = $this->randomPoint();

            $service = Service::query()->create([
                'name' => sprintf('Servicio %s', strtoupper(bin2hex(random_bytes(3)))),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'polyline' => $this->routeProvider->getEncodedRoute($origin, $destination),
            ]);

            $services[] = $this->mapService($service);
        }

        return [
            'requested' => $count,
            'created' => count($services),
            'services' => $services,
        ];
    }

    private function randomPoint(): GeoPoint
    {
        $centerLatitude = (float) config('services.simulator.route_center_lat');
        $centerLongitude = (float) config('services.simulator.route_center_lng');
        $radiusMeters = max((int) config('services.simulator.route_radius_meters', 5000), 1000);
        $distance = random_int(0, $radiusMeters) / 111320;
        $angle = deg2rad(random_int(0, 359));

        return new GeoPoint(
            $centerLatitude + ($distance * cos($angle)),
            $centerLongitude + (($distance * sin($angle)) / max(cos(deg2rad($centerLatitude)), 0.1)),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function mapService(Service $service): array
    {
        return [
            'id' => $service->id,
            'name' => $service->name,
            'start_time' => $service->start_time?->toISOString(),
            'end_time' => $service->end_time?->toISOString(),
            'polyline' => $service->polyline,
        ];
    }
}
