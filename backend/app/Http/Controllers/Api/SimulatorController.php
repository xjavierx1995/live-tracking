<?php

namespace App\Http\Controllers\Api;

use App\Actions\Simulator\CheckSimulatorHealthAction;
use App\Actions\Simulator\GenerateServicesAction;
use App\Actions\Simulator\StartSimulationAction;
use App\Actions\Simulator\StopSimulationAction;
use App\DTO\Simulator\GenerateServicesInputDto;
use App\DTO\Simulator\StartSimulationInputDto;
use App\DTO\Simulator\StopSimulationInputDto;
use App\Exceptions\TrackingSimulatorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Simulator\GenerateServicesRequest;
use App\Http\Requests\Simulator\StartSimulationRequest;
use App\Http\Requests\Simulator\StopSimulationRequest;
use Illuminate\Http\JsonResponse;

class SimulatorController extends Controller
{
    public function generateServices(GenerateServicesRequest $request, GenerateServicesAction $action): JsonResponse
    {
        try {
            $data = $action->execute(GenerateServicesInputDto::fromArray($request->validated()));
        } catch (TrackingSimulatorException $exception) {
            return $this->errorResponse($exception->getMessage(), $exception->statusCode());
        }

        return $this->successResponse($data, 'Servicios generados correctamente.');
    }

    public function startSimulation(StartSimulationRequest $request, StartSimulationAction $action): JsonResponse
    {
        $request->validated();

        try {
            $data = $action->execute(StartSimulationInputDto::create());
        } catch (TrackingSimulatorException $exception) {
            return $this->errorResponse($exception->getMessage(), $exception->statusCode());
        }

        return $this->successResponse($data, 'Simulacion iniciada correctamente.');
    }

    public function stopSimulation(StopSimulationRequest $request, StopSimulationAction $action): JsonResponse
    {
        $request->validated();

        try {
            $data = $action->execute(StopSimulationInputDto::create());
        } catch (TrackingSimulatorException $exception) {
            return $this->errorResponse($exception->getMessage(), $exception->statusCode());
        }

        return $this->successResponse($data, 'Simulacion detenida correctamente.');
    }

    public function health(CheckSimulatorHealthAction $action): JsonResponse
    {
        $status = $action->execute();

        if ($status['simulator'] === 'down') {
            return response()->json([
                'data' => ['simulator' => 'down', 'simulation_active' => false],
                'message' => 'Tracking simulator no disponible.',
                'status' => false,
            ], 503);
        }

        return $this->successResponse($status, 'Tracking simulator disponible.');
    }
}
