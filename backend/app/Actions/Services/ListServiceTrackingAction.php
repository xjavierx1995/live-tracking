<?php

namespace App\Actions\Services;

use App\DTO\Simulator\TrackingPointDto;
use App\Services\TrackingSimulatorClient;

class ListServiceTrackingAction
{
    public function __construct(private readonly TrackingSimulatorClient $client)
    {
    }

    /**
     * @return array<int, TrackingPointDto>
     */
    public function execute(int $id): array
    {
        $payload = $this->client->getServiceTracking($id);
        $rows = $this->extractList($payload);

        return array_map(
            static fn (array $row): TrackingPointDto => TrackingPointDto::fromArray($row),
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
