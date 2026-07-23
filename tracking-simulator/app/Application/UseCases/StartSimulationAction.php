<?php

namespace App\Application\UseCases;

use App\Application\Contracts\SimulationStateStore;
use App\Application\Support\DispatchesSimulationJob;

class StartSimulationAction
{
    use DispatchesSimulationJob;

    public function __construct(private readonly SimulationStateStore $simulationStateStore)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $wasRunning = $this->simulationStateStore->isActive();
        $state = $this->simulationStateStore->activate();
        $this->dispatchSimulationJob();

        return [
            'job_id' => null,
            'started_at' => $state['started_at'] ?? null,
            'was_running' => $wasRunning,
        ];
    }
}
