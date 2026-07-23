<?php

namespace App\Presentation\Http\Controllers\Api;

use App\Application\UseCases\GetServiceAction;
use App\Application\UseCases\ListServiceTrackingAction;
use App\Application\UseCases\ListServicesAction;
use App\Application\UseCases\ListServicesTrackingAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class ServiceReadController extends ApiController
{
    public function index(ListServicesAction $action): JsonResponse
    {
        return $this->successResponse($action->execute(), 'Servicios obtenidos correctamente.');
    }

    public function show(int $id, GetServiceAction $action): JsonResponse
    {
        try {
            $service = $action->execute($id);
        } catch (ModelNotFoundException) {
            return $this->errorResponse('Servicio no encontrado.', 404);
        }

        return $this->successResponse($service, 'Servicio obtenido correctamente.');
    }

    public function trackingByService(int $id, ListServiceTrackingAction $action): JsonResponse
    {
        try {
            $trackings = $action->execute($id);
        } catch (ModelNotFoundException) {
            return $this->errorResponse('Servicio no encontrado.', 404);
        }

        return $this->successResponse($trackings, 'Tracking del servicio obtenido correctamente.');
    }

    public function tracking(ListServicesTrackingAction $action): JsonResponse
    {
        return $this->successResponse($action->execute(), 'Tracking agregado obtenido correctamente.');
    }
}