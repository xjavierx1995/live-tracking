<?php

namespace App\Application\Contracts;

interface SimulationStateStore
{
    public function isActive(): bool;

    /**
     * @return array<string, mixed>
     */
    public function activate(): array;

    /**
     * @return array<string, mixed>
     */
    public function deactivate(): array;

    /**
     * @return array<string, mixed>
     */
    public function snapshot(): array;
}
