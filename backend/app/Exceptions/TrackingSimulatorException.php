<?php

namespace App\Exceptions;

use RuntimeException;

class TrackingSimulatorException extends RuntimeException
{
    private int $statusCode;

    /**
     * @var array<string, mixed>|null
     */
    private ?array $remotePayload;

    /**
     * @param  array<string, mixed>|null  $remotePayload
     */
    public function __construct(
        string $message,
        int $statusCode = 503,
        ?array $remotePayload = null,
    ) {
        parent::__construct($message, $statusCode);

        $this->statusCode = $statusCode;
        $this->remotePayload = $remotePayload;
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function remotePayload(): ?array
    {
        return $this->remotePayload;
    }
}
