# Tracking Simulator

Microservicio Laravel responsable de generar servicios ficticios, construir la ruta base, producir tracking GPS continuo y exponer un contrato interno consumido por `backend`.

## Responsabilidades

- Generar servicios con `id`, `name`, `start_time`, `end_time` y `polyline`.
- Persistir historico de tracking en la tabla `tracking`.
- Encapsular la integracion con Google Maps dentro de `GoogleMapsRouteProvider`.
- Reprogramar ticks de simulacion cada 30 segundos mientras el estado global siga activo.
- Mantener respuestas compatibles con el read model que backend ya expone.

## Estructura

- `app/Presentation`: controllers y requests HTTP del simulador.
- `app/Application`: casos de uso, contratos y jobs.
- `app/Domain`: value objects del dominio de simulacion.
- `app/Infrastructure`: implementaciones tecnicas para polyline, estado y proveedor externo.

## Variables de entorno

Variables principales para Docker Compose:

```env
APP_URL=http://localhost:8001
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=live_tracking
DB_USERNAME=root
DB_PASSWORD=root
CACHE_STORE=database
QUEUE_CONNECTION=database
SIMULATION_TICK_SECONDS=30
GOOGLE_MAPS_API_KEY=
GOOGLE_MAPS_DIRECTIONS_URL=https://maps.googleapis.com/maps/api/directions/json
SIMULATOR_ROUTE_CENTER_LAT=4.7110
SIMULATOR_ROUTE_CENTER_LNG=-74.0721
SIMULATOR_ROUTE_RADIUS_METERS=5000
```

Notas:

- Si `GOOGLE_MAPS_API_KEY` no esta configurada, el provider genera una ruta sintetica pero mantiene la integracion encapsulada en la misma abstraccion.
- `QUEUE_CONNECTION=database` es requerido para la reprogramacion recursiva del tracking.
- `CACHE_STORE=database` conserva el estado central de simulacion entre requests y workers.

## Arranque local

Con Docker Compose:

```bash
docker compose up -d mysql simulator simulator-worker
```

Dentro del contenedor o localmente:

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan serve --host=0.0.0.0 --port=8001
php artisan queue:work --tries=1 --sleep=1
```

## Flujo de simulacion

1. `POST /api/simulator/services/generate` crea servicios y persiste la polyline.
2. `POST /api/simulator/simulation/start` activa el estado global y encola el primer tick.
3. `AdvanceSimulationJob` ejecuta `AdvanceTrackingTickAction`, persiste un nuevo punto por servicio y se vuelve a programar a 30 segundos.
4. `POST /api/simulator/simulation/stop` desactiva el estado global y corta la reprogramacion.
5. `GET /api/services`, `GET /api/services/{id}/tracking` y `GET /api/services/tracking` exponen el read model consumido por backend.

## Endpoints internos

- `POST /api/simulator/services/generate`
- `POST /api/simulator/simulation/start`
- `POST /api/simulator/simulation/stop`
- `GET /api/simulator/health`
- `GET /api/services`
- `GET /api/services/{id}`
- `GET /api/services/{id}/tracking`
- `GET /api/services/tracking`
