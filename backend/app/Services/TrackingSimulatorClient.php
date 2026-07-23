<?php

namespace App\Services;

use App\Exceptions\TrackingSimulatorException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class TrackingSimulatorClient
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.tracking_simulator.base_url', ''), '/');
    }

    /**
     * @return array<string, mixed>
     */
    public function generateServices(int $count): array
    {
        return $this->request('POST', '/api/simulator/services/generate', ['count' => $count]);
    }

    /**
     * @return array<string, mixed>
     */
    public function startSimulation(): array
    {
        return $this->request('POST', '/api/simulator/simulation/start');
    }

    /**
     * @return array<string, mixed>
     */
    public function stopSimulation(): array
    {
        return $this->request('POST', '/api/simulator/simulation/stop');
    }

    /**
     * @return array<string, mixed>
     */
    public function health(): array
    {
        return $this->request('GET', '/api/simulator/health');
    }

    /**
     * @return array<string, mixed>
     */
    public function listServices(): array
    {
        return $this->request('GET', '/api/services');
    }

    /**
     * @return array<string, mixed>
     */
    public function getService(int $id): array
    {
        return $this->request('GET', sprintf('/api/services/%d', $id));
    }

    /**
     * @return array<string, mixed>
     */
    public function getServiceTracking(int $id): array
    {
        return $this->request('GET', sprintf('/api/services/%d/tracking', $id));
    }

    /**
     * @return array<string, mixed>
     */
    public function getServicesTracking(): array
    {
        return $this->request('GET', '/api/services/tracking');
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function request(string $method, string $uri, array $payload = []): array
    {
        if ($this->baseUrl === '') {
            throw new TrackingSimulatorException('TRACKING_SIMULATOR_BASE_URL no esta configurada.', 500);
        }

        $attempt = 0;
        $maxAttempts = 3;
        $delayMs = 200;

        while ($attempt < $maxAttempts) {
            $attempt++;

            try {
                $response = $this->pendingRequest()->send($method, ltrim($uri, '/'), [
                    'json' => $payload,
                ]);
            } catch (ConnectionException) {
                if ($attempt < $maxAttempts) {
                    usleep($delayMs * 1000);
                    $delayMs *= 2;
                    continue;
                }

                throw new TrackingSimulatorException('No fue posible conectar con tracking-simulator.', 503);
            }

            if ($response->successful()) {
                return $this->normalizePayload($response);
            }

            if ($response->serverError() && $attempt < $maxAttempts) {
                usleep($delayMs * 1000);
                $delayMs *= 2;
                continue;
            }

            throw $this->toException($response);
        }

        throw new TrackingSimulatorException('No fue posible completar la solicitud al tracking-simulator.', 503);
    }

    private function pendingRequest(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->asJson()
            ->connectTimeout(5)
            ->timeout(10)
            ->withHeaders([
                'X-Internal-Caller' => 'live-tracking-backend',
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizePayload(Response $response): array
    {
        $payload = $response->json();

        return is_array($payload) ? $payload : [];
    }

    private function toException(Response $response): TrackingSimulatorException
    {
        $payload = $response->json();
        $safePayload = is_array($payload) ? $payload : null;

        $message = is_array($payload) && is_string($payload['message'] ?? null)
            ? $payload['message']
            : 'Tracking-simulator retorno una respuesta invalida.';

        if ($response->serverError()) {
            $message = 'Tracking-simulator presento un error interno.';
        }

        if ($response->status() === 404) {
            $message = 'Recurso no encontrado en tracking-simulator.';
        }

        if ($response->status() === 422) {
            $message = is_array($payload) && is_string($payload['message'] ?? null)
                ? $payload['message']
                : 'Tracking-simulator rechazo la solicitud.';
        }

        return new TrackingSimulatorException($message, $response->status(), $safePayload);
    }
}
