<?php

namespace App\DTO\Simulator;

final readonly class TrackingPointDto
{
    public function __construct(
        public float $latitude,
        public float $longitude,
        public ?string $createdAt,
    ) {
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            latitude: (float) ($payload['latitude'] ?? 0),
            longitude: (float) ($payload['longitude'] ?? 0),
            createdAt: isset($payload['created_at']) ? (string) $payload['created_at'] : null,
        );
    }
}
