<?php

namespace App\Actions\Simulator;

use App\DTO\Simulator\GenerateServicesInputDto;
use App\Services\TrackingSimulatorClient;

class GenerateServicesAction
{
    public function __construct(private readonly TrackingSimulatorClient $client)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(GenerateServicesInputDto $input): array
    {
        $payload = $this->client->generateServices($input->count);

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
