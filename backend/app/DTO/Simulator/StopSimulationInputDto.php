<?php

namespace App\DTO\Simulator;

final readonly class StopSimulationInputDto
{
    public static function create(): self
    {
        return new self();
    }
}
