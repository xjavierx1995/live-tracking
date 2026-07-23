<?php

namespace App\Actions\Simulator;

use App\Exceptions\TrackingSimulatorException;
use App\Services\TrackingSimulatorClient;

class CheckSimulatorHealthAction
{
    public function __construct(private readonly TrackingSimulatorClient $client)
    {
    }

    /**
     * @return array{simulator: string, simulation_active: bool}
     */
    public function execute(): array
    {
        try {
            $payload = $this->client->health();
        } catch (TrackingSimulatorException) {
            return ['simulator' => 'down', 'simulation_active' => false];
        }

        $data = $payload['data'] ?? $payload;

        $simulator = $data['simulator'] ?? 'down';
        $simulationActive = $data['simulation_active'] ?? false;

        return [
            'simulator' => $simulator === 'up' ? 'up' : 'down',
            'simulation_active' => (bool) $simulationActive,
        ];
    }
}
