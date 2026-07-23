<?php

namespace App\Http\Controllers\Api;

use App\Actions\Services\GetServiceAction;
use App\Actions\Services\ListServiceTrackingAction;
use App\Actions\Services\ListServicesAction;
use App\Actions\Services\ListServicesTrackingAction;
use App\Exceptions\TrackingSimulatorException;
use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\ServiceWithTrackingsResource;
use App\Http\Resources\TrackingResource;
use Illuminate\Http\JsonResponse;

class ServiceReadController extends Controller
{
    public function index(ListServicesAction $action): JsonResponse
    {
        try {
            $services = $action->execute();
        } catch (TrackingSimulatorException $exception) {
            return $this->errorResponse($exception->getMessage(), $exception->statusCode());
        }

        return $this->successResponse(ServiceResource::collection($services), 'Servicios obtenidos correctamente.');
    }

    public function show(int $id, GetServiceAction $action): JsonResponse
    {
        try {
            $service = $action->execute($id);
        } catch (TrackingSimulatorException $exception) {
            return $this->errorResponse($exception->getMessage(), $exception->statusCode());
        }

        return $this->successResponse(ServiceResource::make($service), 'Servicio obtenido correctamente.');
    }

    public function trackingByService(int $id, ListServiceTrackingAction $action): JsonResponse
    {
        try {
            $trackingRows = $action->execute($id);
        } catch (TrackingSimulatorException $exception) {
            return $this->errorResponse($exception->getMessage(), $exception->statusCode());
        }

        return $this->successResponse(TrackingResource::collection($trackingRows), 'Tracking del servicio obtenido correctamente.');
    }

    public function tracking(ListServicesTrackingAction $action): JsonResponse
    {
        try {
            $trackingRows = $action->execute();
        } catch (TrackingSimulatorException $exception) {
            return $this->errorResponse($exception->getMessage(), $exception->statusCode());
        }

        return $this->successResponse(ServiceWithTrackingsResource::collection($trackingRows), 'Tracking agregado obtenido correctamente.');
    }
}
