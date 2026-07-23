<?php

namespace App\Actions\Services;

use App\DTO\Simulator\ServiceDto;
use App\Services\TrackingSimulatorClient;

class ListServicesAction
{
    public function __construct(private readonly TrackingSimulatorClient $client)
    {
    }

    /**
     * @return array<int, ServiceDto>
     */
    public function execute(): array
    {
        $payload = $this->client->listServices();
        $rows = $this->extractList($payload);

        return array_map(
            static fn (array $row): ServiceDto => ServiceDto::fromArray($row),
            $rows,
        );
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<int, array<string, mixed>>
     */
    private function extractList(array $payload): array
    {
        $rows = $payload['data'] ?? $payload;

        if (! is_array($rows)) {
            return [];
        }

        return array_values(array_filter($rows, 'is_array'));
    }
}
