<?php

namespace Tests\Feature;

use App\Application\Contracts\PolylineCodec;
use App\Application\Contracts\SimulationStateStore;
use App\Application\Jobs\AdvanceSimulationJob;
use App\Application\UseCases\AdvanceTrackingTickAction;
use App\Domain\Simulation\GeoPoint;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SimulatorApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_services_with_backend_compatible_shape(): void
    {
        $response = $this->postJson('/api/simulator/services/generate', [
            'count' => 2,
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.requested', 2)
            ->assertJsonPath('data.created', 2)
            ->assertJsonCount(2, 'data.services')
            ->assertJsonStructure([
                'data' => [
                    'requested',
                    'created',
                    'services' => [[
                        'id',
                        'name',
                        'start_time',
                        'end_time',
                        'polyline',
                    ]],
                ],
                'message',
                'status',
            ]);

        $this->assertDatabaseCount('services', 2);
    }

    public function test_it_starts_simulation_and_dispatches_the_first_tick(): void
    {
        Queue::fake();

        $response = $this->postJson('/api/simulator/simulation/start');

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.was_running', false)
            ->assertJsonStructure([
                'data' => ['job_id', 'started_at', 'was_running'],
                'message',
                'status',
            ]);

        Queue::assertPushed(AdvanceSimulationJob::class);
    }

    public function test_it_rejects_payload_when_starting_global_simulation(): void
    {
        $this->postJson('/api/simulator/simulation/start', [
            'service_ids' => [1],
        ])->assertStatus(422)
            ->assertJsonPath('status', false);
    }

    public function test_it_returns_service_tracking_points_with_backend_shape(): void
    {
        $service = $this->createServiceWithPolyline();
        app(SimulationStateStore::class)->activate();

        app(AdvanceTrackingTickAction::class)->execute();

        $response = $this->getJson(sprintf('/api/services/%d/tracking', $service->id));

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [[
                    'latitude',
                    'longitude',
                    'created_at',
                ]],
                'message',
                'status',
            ]);
    }

    public function test_it_returns_aggregate_services_with_nested_trackings(): void
    {
        $service = $this->createServiceWithPolyline();
        app(SimulationStateStore::class)->activate();

        app(AdvanceTrackingTickAction::class)->execute();

        $response = $this->getJson('/api/services/tracking');

        $response->assertOk()
            ->assertJsonPath('status', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $service->id)
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'name',
                    'start_time',
                    'end_time',
                    'polyline',
                    'trackings' => [[
                        'latitude',
                        'longitude',
                        'created_at',
                    ]],
                ]],
                'message',
                'status',
            ]);
    }

    private function createServiceWithPolyline(): Service
    {
        $codec = app(PolylineCodec::class);

        return Service::query()->create([
            'name' => 'Servicio TEST01',
            'start_time' => now(),
            'end_time' => now()->addHour(),
            'polyline' => $codec->encode([
                new GeoPoint(4.7110, -74.0721),
                new GeoPoint(4.7120, -74.0710),
            ]),
        ]);
    }
}