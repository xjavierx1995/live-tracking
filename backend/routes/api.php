<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceReadController;
use App\Http\Controllers\Api\SimulatorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('simulator')->group(function () {
        Route::post('/services/generate', [SimulatorController::class, 'generateServices']);
        Route::post('/simulation/start', [SimulatorController::class, 'startSimulation']);
        Route::post('/simulation/stop', [SimulatorController::class, 'stopSimulation']);
        Route::get('/health', [SimulatorController::class, 'health']);
    });

    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceReadController::class, 'index']);
        Route::get('/tracking', [ServiceReadController::class, 'tracking']);
        Route::get('/{id}', [ServiceReadController::class, 'show']);
        Route::get('/{id}/tracking', [ServiceReadController::class, 'trackingByService']);
    });
});

Route::get('/test', function () {
    return response()->json(['message' => 'Test route works!']);
});
