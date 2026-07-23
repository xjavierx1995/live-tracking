<?php

namespace App\Application\UseCases;

use App\Application\Contracts\PolylineCodec;
use App\Application\Contracts\SimulationStateStore;
use App\Application\Support\DispatchesSimulationJob;
use App\Domain\Simulation\GeoPoint;
use App\Models\Service;
use App\Models\Tracking;
use Illuminate\Support\Carbon;

class AdvanceTrackingTickAction
{
    use DispatchesSimulationJob;

    public function __construct(
        private readonly PolylineCodec $polylineCodec,
        private readonly SimulationStateStore $simulationStateStore,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        if (! $this->simulationStateStore->isActive()) {
            return [
                'processed_services' => 0,
                'created_points' => 0,
                'continued' => false,
            ];
        }

        $createdPoints = 0;
        $services = Service::query()->withCount('trackings')->get();

        foreach ($services as $service) {
            if (! is_string($service->polyline) || $service->polyline === '') {
                continue;
            }

            $points = $this->polylineCodec->decode($service->polyline);

            if ($points === []) {
                continue;
            }

            $pointIndex = min($service->trackings_count, count($points) - 1);
            $point = $this->applyNoise($points[$pointIndex]);

            Tracking::query()->create([
                'service_id' => $service->id,
                'latitude' => $point->latitude,
                'longitude' => $point->longitude,
                'created_at' => Carbon::now(),
            ]);

            $createdPoints++;
        }

        $continued = $this->simulationStateStore->isActive();

        if ($continued) {
            $this->dispatchSimulationJob((int) config('services.simulator.tick_seconds', 30));
        }

        return [
            'processed_services' => $services->count(),
            'created_points' => $createdPoints,
            'continued' => $continued,
        ];
    }

    private function applyNoise(GeoPoint $point): GeoPoint
    {
        $offsetLatitude = random_int(-5, 5) / 100000;
        $offsetLongitude = random_int(-5, 5) / 100000;

        return new GeoPoint(
            $point->latitude + $offsetLatitude,
            $point->longitude + $offsetLongitude,
        );
    }
}
