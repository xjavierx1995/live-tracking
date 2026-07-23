<?php

namespace App\Actions\Simulator;

use App\Exceptions\TrackingSimulatorException;
use App\Services\TrackingSimulatorClient;

class CheckSimulatorHealthAction
{
    public function __construct(private readonly TrackingSimulatorClient $client)
    {
    }

    public function execute(): string
    {
        try {
            $payload = $this->client->health();
        } catch (TrackingSimulatorException) {
            return 'down';
        }

        if (isset($payload['data']) && is_array($payload['data'])) {
            $status = $payload['data']['simulator'] ?? null;

            return $status === 'down' ? 'down' : 'up';
        }

        $status = $payload['simulator'] ?? null;

        return $status === 'down' ? 'down' : 'up';
    }
}
