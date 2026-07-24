## Context

El Tracking Simulator utiliza `GoogleMapsRouteProvider` (en `app/Infrastructure/Routes/`) para obtener la ruta encodada entre dos puntos geográficos. Esta implementación llama a la API de Directions de Google Maps, que no está disponible en Venezuela. El resultado es que la aplicación cae siempre al fallback sintético en producción, generando rutas de línea recta artificiales.

El simulador ya define el contrato `RouteProvider` en la capa Application, lo que permite sustituir la implementación sin tocar nada fuera de `Infrastructure` y `AppServiceProvider`.

**Estado actual relevante:**
- `app/Application/Contracts/RouteProvider.php` — interface `getEncodedRoute(GeoPoint, GeoPoint): string`
- `app/Application/Contracts/PolylineCodec.php` — interface `encode(array): string`
- `app/Infrastructure/Routes/GoogleMapsRouteProvider.php` — implementación actual
- `config/services.php` — sección `google_maps` existente
- `app/Providers/AppServiceProvider.php` — binding actual `RouteProvider → GoogleMapsRouteProvider`

## Goals / Non-Goals

**Goals:**
- Crear `OpenRouteServiceProvider` que implemente `RouteProvider` consultando la API pública de ORS.
- Reutilizar el `PolylineCodec` existente para encodear las coordenadas extraídas del GeoJSON de ORS.
- Mantener el fallback `buildSyntheticRoute` cuando `ORS_API_KEY` no esté configurada.
- Actualizar el binding en `AppServiceProvider`.
- Exponer `ORS_API_KEY` y `ORS_BASE_URL` como variables de entorno en `config/services.php`, `.env` y `.env.example`.

**Non-Goals:**
- Modificar la interface `RouteProvider` o `PolylineCodec`.
- Cambiar cualquier lógica en Application, Domain o Presentation.
- Modificar el Frontend o el Backend.
- Proveer soporte multi-proveedor simultáneo o selección dinámica de provider.
- Cachear respuestas de ORS.

## Decisions

### Decisión 1: Usar el mismo `PolylineCodec` existente

**Opción elegida:** Reutilizar `GooglePolylineCodec` (ligado al contrato `PolylineCodec`) para encodear las coordenadas de ORS.

**Rationale:** El algoritmo de encoding de Google Polyline es un estándar abierto. ORS devuelve coordenadas crudas `[lon, lat]` en GeoJSON; al encodearlas con el mismo codec se garantiza que el frontend (que decodifica con el mismo algoritmo) no necesita ningún cambio.

**Alternativa descartada:** Usar la polyline encodada que ORS puede devolver directamente (si se pide `format=encodedpolyline`). Se descarta porque ese endpoint está en la API v2 legacy, es menos estable y cambia el orden lat/lon; trabajar con el GeoJSON estándar es más robusto.

---

### Decisión 2: Estructura de la petición a ORS

**Opción elegida:** `POST /v2/directions/driving-car` con body GeoJSON:
```json
{
  "coordinates": [[lon_origin, lat_origin], [lon_dest, lat_dest]]
}
```
Header: `Authorization: <api_key>`.

**Rationale:** Es el endpoint principal de la API ORS v2, documentado y estable. Devuelve un FeatureCollection GeoJSON con `geometry.coordinates` como array de `[lon, lat]`. Se extrae ese array, se invierte el orden a `[lat, lon]` para crear `GeoPoint[]` y se pasa al codec.

**Alternativa descartada:** API v1 GET `/directions` — deprecada y con menor cobertura de errores.

---

### Decisión 3: Manejo de errores

Se lanzará `RouteProviderException` (ya existente) en los mismos casos que el provider de Google: timeout de conexión y respuesta HTTP no exitosa. Esto garantiza que el servicio de Application que llama al provider tiene un comportamiento consistente independientemente del provider activo.

---

### Decisión 4: Variable de entorno `ORS_BASE_URL`

Se parametriza la URL base para facilitar pruebas contra un servidor ORS local o una instancia self-hosted, sin hardcodear `https://api.openrouteservice.org`.

## Risks / Trade-offs

| Riesgo | Mitigación |
|---|---|
| Límite de peticiones del plan gratuito de ORS (~2000 req/día) | Aceptable para el entorno de simulación actual; se puede escalar a plan de pago si el uso crece. |
| ORS no tiene cobertura detallada en todas las áreas de Venezuela | El fallback sintético permanece activo cuando `ORS_API_KEY` está vacía; en el peor caso se degradan a rutas lineales. |
| ORS puede tener latencia mayor que Google Maps en la región | El timeout se configura en 10 s (igual que Google Maps). Valor parametrizable en el futuro. |
| La API key queda en `.env` en texto plano | Práctica estándar para credenciales de entorno en Laravel; `.env` no se versiona. |

## Migration Plan

1. Crear `OpenRouteServiceProvider.php`.
2. Agregar la sección `openrouteservice` en `config/services.php`.
3. Actualizar `.env.example` con `ORS_API_KEY=` y `ORS_BASE_URL=https://api.openrouteservice.org`.
4. Añadir las variables reales a `.env` (no versionado).
5. Cambiar el binding en `AppServiceProvider`.
6. Verificar manualmente: iniciar una simulación y confirmar que las posiciones generadas siguen una ruta real.

**Rollback:** Revertir el binding en `AppServiceProvider` a `GoogleMapsRouteProvider`. No hay cambios de base de datos ni contratos REST; el rollback es instantáneo.

## Open Questions

_(ninguna — el alcance está completamente definido)_
