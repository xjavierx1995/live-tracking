<?php

namespace App\Providers;

use App\Application\Contracts\PolylineCodec;
use App\Application\Contracts\RouteProvider;
use App\Application\Contracts\SimulationStateStore;
use App\Infrastructure\Polyline\GooglePolylineCodec;
use App\Infrastructure\Routes\OpenRouteServiceProvider;
use App\Infrastructure\Simulation\CacheSimulationStateStore;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PolylineCodec::class, GooglePolylineCodec::class);
        $this->app->singleton(RouteProvider::class, OpenRouteServiceProvider::class);
        $this->app->singleton(SimulationStateStore::class, CacheSimulationStateStore::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
