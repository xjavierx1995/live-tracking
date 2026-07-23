<?php

namespace App\Actions\Simulator;

use App\DTO\Simulator\StopSimulationInputDto;
use App\Services\TrackingSimulatorClient;

class StopSimulationAction
{
    public function __construct(private readonly TrackingSimulatorClient $client)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(StopSimulationInputDto $input): array
    {
        unset($input);

        $payload = $this->client->stopSimulation();

        return $this->extractData($payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function extractData(array $payload): array
    {
        if (isset($payload['data']) && is_array($payload['data'])) {
            return $payload['data'];
        }

        return $payload;
    }
}
