<?php

namespace App\Application\Support;

use App\Application\Jobs\AdvanceSimulationJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Testing\Fakes\QueueFake;

trait DispatchesSimulationJob
{
    protected function dispatchSimulationJob(int $delaySeconds = 0): void
    {
        $queue = Queue::getFacadeRoot();

        if (config('queue.default') === 'sync' && ! $queue instanceof QueueFake) {
            return;
        }

        $dispatch = AdvanceSimulationJob::dispatch();

        if ($delaySeconds > 0) {
            $dispatch->delay(now()->addSeconds($delaySeconds));
        }
    }
}
