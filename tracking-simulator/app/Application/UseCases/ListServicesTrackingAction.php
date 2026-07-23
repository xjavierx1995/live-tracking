<?php

namespace App\Application\UseCases;

use App\Models\Service;
use App\Models\Tracking;

class ListServicesTrackingAction
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function execute(): array
    {
        return Service::query()
            ->with(['trackings' => fn ($query) => $query->orderBy('created_at')])
            ->orderByDesc('id')
            ->get()
            ->map(function (Service $service): array {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'start_time' => $service->start_time?->toISOString(),
                    'end_time' => $service->end_time?->toISOString(),
                    'polyline' => $service->polyline,
                    'trackings' => $service->trackings->map(fn (Tracking $tracking): array => [
                        'latitude' => $tracking->latitude,
                        'longitude' => $tracking->longitude,
                        'created_at' => $tracking->created_at?->toISOString(),
                    ])->all(),
                ];
            })
            ->all();
    }
}