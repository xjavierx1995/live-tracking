<?php

namespace App\Actions\Services;

use App\DTO\Simulator\ServiceDto;
use App\Services\TrackingSimulatorClient;

class GetServiceAction
{
    public function __construct(private readonly TrackingSimulatorClient $client)
    {
    }

    public function execute(int $id): ServiceDto
    {
        $payload = $this->client->getService($id);

        if (isset($payload['data']) && is_array($payload['data'])) {
            return ServiceDto::fromArray($payload['data']);
        }

        return ServiceDto::fromArray($payload);
    }
}
