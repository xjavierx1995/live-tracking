<?php

use App\Presentation\Http\Controllers\Api\ServiceReadController;
use App\Presentation\Http\Controllers\Api\SimulatorController;
use Illuminate\Support\Facades\Route;

Route::prefix('simulator')->group(function () {
    Route::post('/services/generate', [SimulatorController::class, 'generateServices']);
    Route::post('/simulation/start', [SimulatorController::class, 'startSimulation']);
    Route::post('/simulation/stop', [SimulatorController::class, 'stopSimulation']);
    Route::get('/health', [SimulatorController::class, 'health']);
});

Route::prefix('services')->group(function () {
    Route::get('/', [ServiceReadController::class, 'index']);
    Route::get('/tracking', [ServiceReadController::class, 'tracking']);
    Route::get('/{id}', [ServiceReadController::class, 'show'])->whereNumber('id');
    Route::get('/{id}/tracking', [ServiceReadController::class, 'trackingByService'])->whereNumber('id');
});
