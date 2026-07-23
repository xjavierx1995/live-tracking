<?php

namespace App\DTO\Simulator;

final readonly class ServiceWithTrackingsDto
{
    /**
     * @param  array<int, TrackingPointDto>  $trackings
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $startTime,
        public ?string $endTime,
        public ?string $polyline,
        public array $trackings,
    ) {
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        $trackings = $payload['trackings'] ?? [];

        if (! is_array($trackings)) {
            $trackings = [];
        }

        return new self(
            id: (int) ($payload['id'] ?? 0),
            name: (string) ($payload['name'] ?? ''),
            startTime: isset($payload['start_time']) ? (string) $payload['start_time'] : null,
            endTime: isset($payload['end_time']) ? (string) $payload['end_time'] : null,
            polyline: isset($payload['polyline']) ? (string) $payload['polyline'] : null,
            trackings: array_map(
                static fn (array $tracking): TrackingPointDto => TrackingPointDto::fromArray($tracking),
                array_values(array_filter($trackings, 'is_array')),
            ),
        );
    }
}
