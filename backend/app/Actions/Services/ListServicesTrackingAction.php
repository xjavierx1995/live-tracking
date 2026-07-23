<?php

namespace App\Actions\Services;

use App\DTO\Simulator\ServiceWithTrackingsDto;
use App\Services\TrackingSimulatorClient;

class ListServicesTrackingAction
{
    public function __construct(private readonly TrackingSimulatorClient $client)
    {
    }

    /**
     * @return array<int, ServiceWithTrackingsDto>
     */
    public function execute(): array
    {
        $payload = $this->client->getServicesTracking();
        $rows = $this->extractList($payload);

        return array_map(
            static fn (array $row): ServiceWithTrackingsDto => ServiceWithTrackingsDto::fromArray($row),
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
