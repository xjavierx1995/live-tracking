## ADDED Requirements

### Requirement: Obtención de ruta real mediante OpenRouteService
El Tracking Simulator SHALL obtener la ruta encodada entre un origen y un destino consultando la API de OpenRouteService cuando la variable `ORS_API_KEY` esté configurada. La ruta SHALL retornarse como una polyline encodada en formato Google Polyline compatible con el frontend.

#### Scenario: Ruta exitosa con API key configurada
- **WHEN** `ORS_API_KEY` tiene un valor no vacío y la API de ORS responde con éxito
- **THEN** el proveedor extrae las coordenadas `[lon, lat]` del GeoJSON de respuesta, las convierte en `GeoPoint[]` con orden `[lat, lon]`, y retorna la polyline encodada con `PolylineCodec::encode()`

#### Scenario: Fallback sintético sin API key
- **WHEN** `ORS_API_KEY` está vacía o no está definida
- **THEN** el proveedor genera una ruta sintética interpolando puntos entre origen y destino con curvatura artificial, sin realizar ninguna petición HTTP

#### Scenario: Error de conexión con ORS
- **WHEN** la petición HTTP a la API de ORS lanza una excepción de conexión (timeout, DNS, etc.)
- **THEN** el proveedor lanza `RouteProviderException` con código 503

#### Scenario: Respuesta HTTP no exitosa de ORS
- **WHEN** la API de ORS retorna un código HTTP 4xx o 5xx
- **THEN** el proveedor lanza `RouteProviderException` con código 503

#### Scenario: Respuesta GeoJSON sin coordenadas válidas
- **WHEN** la respuesta de ORS es HTTP 200 pero `features.0.geometry.coordinates` está vacío o ausente
- **THEN** el proveedor lanza `RouteProviderException` con código 503

### Requirement: Configuración de OpenRouteService mediante variables de entorno
El sistema SHALL leer la API key y la URL base de ORS desde las variables de entorno `ORS_API_KEY` y `ORS_BASE_URL` a través de `config/services.php`.

#### Scenario: Variables de entorno presentes
- **WHEN** `ORS_API_KEY` y `ORS_BASE_URL` están definidas en `.env`
- **THEN** `config('services.openrouteservice.api_key')` y `config('services.openrouteservice.base_url')` retornan sus valores correspondientes

#### Scenario: URL base por defecto
- **WHEN** `ORS_BASE_URL` no está definida en `.env`
- **THEN** `config('services.openrouteservice.base_url')` retorna `https://api.openrouteservice.org`

### Requirement: Binding de RouteProvider apunta a OpenRouteServiceProvider
El `AppServiceProvider` SHALL registrar `OpenRouteServiceProvider` como implementación del contrato `RouteProvider`.

#### Scenario: Resolución del contrato RouteProvider
- **WHEN** la aplicación resuelve `RouteProvider` desde el contenedor de Laravel
- **THEN** se retorna una instancia de `OpenRouteServiceProvider`
