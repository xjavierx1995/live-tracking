<?php

namespace App\DTO\Simulator;

final readonly class GenerateServicesInputDto
{
    public function __construct(public int $count)
    {
    }

    /**
     * @param  array{count: int}  $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self((int) $payload['count']);
    }
}
