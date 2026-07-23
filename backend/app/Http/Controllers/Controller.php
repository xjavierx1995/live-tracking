<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    protected function successResponse(
        mixed $data = null,
        string $message = 'Operación realizada correctamente.',
        int $status = Response::HTTP_OK,
    ): JsonResponse {
        $payloadData = $data;


        return response()->json([
            'data' => $payloadData,
            'message' => $message,
            'status' => true,
        ], $status);
    }

    protected function createdResponse(
        mixed $data = null,
        string $message = 'Recurso creado correctamente.',
    ): JsonResponse {
        return $this->successResponse($data, $message, Response::HTTP_CREATED);
    }

    protected function errorResponse(
        string $message = 'Ocurrió un error.',
        int $status = Response::HTTP_BAD_REQUEST,
    ): JsonResponse {
        $payloadData = null;

        return response()->json([
            'data' => $payloadData,
            'message' => $message,
            'status' => false,
        ], $status);
    }

    protected function paginatedResponse(
        LengthAwarePaginator $paginator,
        string $message = 'Consulta realizada correctamente.',
    ): JsonResponse {
        return $this->successResponse([
            'items' => $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ], $message);
    }
}
