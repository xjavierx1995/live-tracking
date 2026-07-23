<?php

namespace App\Application\UseCases;

use App\Models\Service;
use App\Models\Tracking;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ListServiceTrackingAction
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function execute(int $serviceId): array
    {
        if (! Service::query()->whereKey($serviceId)->exists()) {
            throw new ModelNotFoundException('Servicio no encontrado.');
        }

        return Tracking::query()
            ->where('service_id', $serviceId)
            ->orderBy('created_at')
            ->get()
            ->map(fn (Tracking $tracking): array => [
                'latitude' => $tracking->latitude,
                'longitude' => $tracking->longitude,
                'created_at' => $tracking->created_at?->toISOString(),
            ])
            ->all();
    }
}
