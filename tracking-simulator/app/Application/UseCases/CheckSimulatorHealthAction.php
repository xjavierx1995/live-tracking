<?php

namespace App\Application\UseCases;

use App\Application\Contracts\SimulationStateStore;

class CheckSimulatorHealthAction
{
    public function __construct(private readonly SimulationStateStore $simulationStateStore)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        return [
            'simulator' => 'up',
            'simulation_active' => $this->simulationStateStore->isActive(),
        ];
    }
}
