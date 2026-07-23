<?php

namespace App\Presentation\Http\Controllers\Api;

use App\Application\Exceptions\SimulationException;
use App\Application\UseCases\CheckSimulatorHealthAction;
use App\Application\UseCases\GenerateServicesAction;
use App\Application\UseCases\StartSimulationAction;
use App\Application\UseCases\StopSimulationAction;
use App\Presentation\Http\Requests\GenerateServicesRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SimulatorController extends ApiController
{
    public function generateServices(GenerateServicesRequest $request, GenerateServicesAction $action): JsonResponse
    {
        try {
            $data = $action->execute((int) $request->integer('count'));
        } catch (SimulationException $exception) {
            return $this->errorResponse($exception->getMessage(), $exception->statusCode());
        }

        return $this->successResponse($data, 'Servicios generados correctamente.', 201);
    }

    public function startSimulation(Request $request, StartSimulationAction $action): JsonResponse
    {
        if ($request->all() !== []) {
            return $this->errorResponse('La simulacion global no acepta parametros de entrada.', 422);
        }

        $data = $action->execute();

        return $this->successResponse($data, 'Simulacion iniciada correctamente.');
    }

    public function stopSimulation(Request $request, StopSimulationAction $action): JsonResponse
    {
        if ($request->all() !== []) {
            return $this->errorResponse('La detencion global no acepta parametros de entrada.', 422);
        }

        $data = $action->execute();

        return $this->successResponse($data, 'Simulacion detenida correctamente.');
    }

    public function health(CheckSimulatorHealthAction $action): JsonResponse
    {
        return $this->successResponse($action->execute(), 'Tracking simulator disponible.');
    }
}