<?php

namespace App\Application\UseCases;

use App\Models\Service;

class ListServicesAction
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function execute(): array
    {
        return Service::query()
            ->orderByDesc('id')
            ->get()
            ->map(fn (Service $service): array => $this->mapService($service))
            ->all();
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
