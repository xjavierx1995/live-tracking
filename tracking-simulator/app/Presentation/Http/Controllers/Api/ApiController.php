<?php

namespace App\Presentation\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

abstract class ApiController
{
    /**
     * @param  array<int|string, mixed>|null  $data
     */
    protected function successResponse(array|null $data, string $message, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => true,
        ], $statusCode);
    }

    protected function errorResponse(string $message, int $statusCode): JsonResponse
    {
        return response()->json([
            'data' => null,
            'message' => $message,
            'status' => false,
        ], $statusCode);
    }
}