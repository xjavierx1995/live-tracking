## Why

La API de Directions de Google Maps no está disponible en Venezuela, lo que impide que `GoogleMapsRouteProvider` genere rutas reales en el entorno de producción. Se migra el proveedor de rutas a **OpenRouteService (ORS)** — servicio gratuito basado en OpenStreetMap con cobertura global — manteniendo la misma interface `RouteProvider` y el mismo algoritmo de encoding de polylines, con impacto mínimo en la arquitectura.

## What Changes

- **[NEW]** `OpenRouteServiceProvider.php` en `app/Infrastructure/Routes/` implementando la interface `RouteProvider`, que consulta la API ORS (formato GeoJSON), extrae las coordenadas y las encodea con el `PolylineCodec` existente.
- **[MODIFY]** `AppServiceProvider`: cambiar el binding de `RouteProvider` de `GoogleMapsRouteProvider` a `OpenRouteServiceProvider`.
- **[MODIFY]** `config/services.php`: agregar la sección `openrouteservice` con `api_key` y `base_url`.
- **[MODIFY]** `.env` y `.env.example`: agregar variables `ORS_API_KEY` y `ORS_BASE_URL`.
- **[RETAIN]** El fallback sintético `buildSyntheticRoute` se mantiene para entornos sin API key configurada.
- **[NO CHANGE]** Interface `RouteProvider`, `PolylineCodec`, capas Application, Domain, Presentation y Frontend.

## Capabilities

### New Capabilities

- `simulator-ors-route-provider`: Proveedor de rutas basado en OpenRouteService para el Tracking Simulator. Consulta la API ORS con un par origen/destino, decodifica el GeoJSON de respuesta y retorna la polyline encodada en formato estándar compatible con el frontend.

### Modified Capabilities

<!-- No hay cambios en los requisitos de ninguna capability existente. El contrato RouteProvider no cambia; sólo cambia su implementación. -->
_(ninguna — sólo cambia la implementación interna, no los contratos ni el comportamiento observable)_

## Impact

- **Tracking Simulator** — única aplicación afectada.
  - `app/Infrastructure/Routes/OpenRouteServiceProvider.php` (nuevo archivo).
  - `app/Providers/AppServiceProvider.php` (re-binding del contrato).
  - `config/services.php` (nueva clave de configuración).
  - `.env` / `.env.example` (nuevas variables de entorno).
- **Dependencia externa**: API pública de OpenRouteService (`api.openrouteservice.org`). Se requiere API key gratuita (ya proporcionada).
- **Sin impacto** en Backend, Frontend ni en ningún contrato REST.
