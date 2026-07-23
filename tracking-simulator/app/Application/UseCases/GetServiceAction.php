<?php

namespace App\Application\UseCases;

use App\Models\Service;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GetServiceAction
{
    /**
     * @return array<string, mixed>
     */
    public function execute(int $id): array
    {
        $service = Service::query()->find($id);

        if (! $service instanceof Service) {
            throw new ModelNotFoundException('Servicio no encontrado.');
        }

        return [
            'id' => $service->id,
            'name' => $service->name,
            'start_time' => $service->start_time?->toISOString(),
            'end_time' => $service->end_time?->toISOString(),
            'polyline' => $service->polyline,
        ];
    }
}
