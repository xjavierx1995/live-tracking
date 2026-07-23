<?php

namespace App\Actions\Simulator;

use App\DTO\Simulator\StartSimulationInputDto;
use App\Services\TrackingSimulatorClient;

class StartSimulationAction
{
    public function __construct(private readonly TrackingSimulatorClient $client)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(StartSimulationInputDto $input): array
    {
        unset($input);

        $payload = $this->client->startSimulation();

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
