<?php

namespace App\Infrastructure\Simulation;

use App\Application\Contracts\SimulationStateStore;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class CacheSimulationStateStore implements SimulationStateStore
{
    private const CACHE_KEY = 'tracking-simulator:state';

    public function isActive(): bool
    {
        return (bool) ($this->snapshot()['active'] ?? false);
    }

    public function activate(): array
    {
        $state = [
            'active' => true,
            'started_at' => Carbon::now()->toISOString(),
            'stopped_at' => null,
        ];

        Cache::forever(self::CACHE_KEY, $state);

        return $state;
    }

    public function deactivate(): array
    {
        $snapshot = $this->snapshot();
        $state = [
            'active' => false,
            'started_at' => $snapshot['started_at'] ?? null,
            'stopped_at' => Carbon::now()->toISOString(),
        ];

        Cache::forever(self::CACHE_KEY, $state);

        return $state;
    }

    public function snapshot(): array
    {
        $state = Cache::get(self::CACHE_KEY);

        return is_array($state)
            ? $state
            : ['active' => false, 'started_at' => null, 'stopped_at' => null];
    }
}
