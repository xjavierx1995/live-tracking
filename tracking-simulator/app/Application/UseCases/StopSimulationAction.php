<?php

namespace App\Application\UseCases;

use App\Application\Contracts\SimulationStateStore;

class StopSimulationAction
{
    public function __construct(private readonly SimulationStateStore $simulationStateStore)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $wasRunning = $this->simulationStateStore->isActive();
        $state = $this->simulationStateStore->deactivate();

        return [
            'stopped_at' => $state['stopped_at'] ?? null,
            'was_running' => $wasRunning,
        ];
    }
}
