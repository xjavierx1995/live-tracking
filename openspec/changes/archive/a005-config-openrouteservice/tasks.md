## 1. Configuración de entorno (Tracking Simulator)

- [x] 1.1 Agregar sección `openrouteservice` en `config/services.php` con las claves `api_key` (desde `ORS_API_KEY`) y `base_url` (desde `ORS_BASE_URL`, default `https://api.openrouteservice.org`)
- [x] 1.2 Agregar `ORS_API_KEY=` y `ORS_BASE_URL=https://api.openrouteservice.org` en `.env.example`
- [x] 1.3 Agregar las variables reales en `.env` del Tracking Simulator con la API key proporcionada

## 2. Implementación de OpenRouteServiceProvider

- [x] 2.1 Crear `app/Infrastructure/Routes/OpenRouteServiceProvider.php` implementando el contrato `RouteProvider`
- [x] 2.2 Implementar el método `getEncodedRoute(GeoPoint $origin, GeoPoint $destination): string` que realiza `POST /v2/directions/driving-car` a la API de ORS con el body GeoJSON `{ "coordinates": [[lon_origin, lat_origin], [lon_dest, lat_dest]] }` y el header `Authorization: <api_key>`
- [x] 2.3 Extraer `features.0.geometry.coordinates` del GeoJSON de respuesta, invertir el orden de cada par a `[lat, lon]`, construir el array de `GeoPoint[]` y encodear con `PolylineCodec::encode()`
- [x] 2.4 Lanzar `RouteProviderException` (código 503) cuando la API devuelva un código HTTP no exitoso o cuando `features.0.geometry.coordinates` esté vacío o ausente
- [x] 2.5 Lanzar `RouteProviderException` (código 503) cuando se capture una `ConnectionException` (timeout, DNS)
- [x] 2.6 Implementar el fallback `buildSyntheticRoute` (copiar de `GoogleMapsRouteProvider`) para entornos sin `ORS_API_KEY` configurada

## 3. Actualización del binding en AppServiceProvider

- [x] 3.1 En `app/Providers/AppServiceProvider.php`, reemplazar el `use` de `GoogleMapsRouteProvider` por `OpenRouteServiceProvider`
- [x] 3.2 Actualizar la línea `$this->app->singleton(RouteProvider::class, ...)` para apuntar a `OpenRouteServiceProvider::class`

## 4. Verificación

- [x] 4.1 Verificar que el contenedor de Laravel resuelve `RouteProvider` como `OpenRouteServiceProvider` (por ejemplo con `php artisan tinker` o revisando el binding registrado)
- [x] 4.2 Iniciar una simulación real y confirmar que las posiciones GPS generadas siguen una ruta real (no línea recta) cuando `ORS_API_KEY` está configurada
- [x] 4.3 Verificar que el fallback sintético se activa correctamente cuando se elimina temporalmente el valor de `ORS_API_KEY`
