<?php

namespace App\DTO\Simulator;

final readonly class StartSimulationInputDto
{
    public static function create(): self
    {
        return new self();
    }
}
