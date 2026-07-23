## Context

Estado actual:
- El backend ya dispone de autenticación con Sanctum.
- No existe todavía un contrato formal del módulo 1 para generación de servicios y arranque de simulación.
- El frontend debe consumir únicamente backend, sin acceso directo al tracking-simulator.

Restricciones:
- Mantener bajo acoplamiento entre servicios.
- Mantener respuestas backend con formato estable `data`, `message`, `status`.
- No implementar todavía la lógica DDD del tracking-simulator en esta fase.
- Mantener compatibilidad hacia atrás para capacidades ya publicadas.

Servicios afectados:
- backend: principal impacto funcional y contractual.
- tracking-simulator: impacto contractual (sin implementación en esta fase).
- frontend: sin cambios de implementación, pero habilitado para consumo futuro.
- infraestructura: comunicación interna Docker y variables de entorno.

## Goals / Non-Goals

**Goals:**
- Definir al backend como único punto de entrada del módulo simulador.
- Exponer contratos HTTP estables para generar servicios, iniciar/detener simulación y consultar servicios/tracking.
- Encapsular llamadas al tracking-simulator en un cliente HTTP interno con manejo de timeout/retry/error.
- Mantener controllers delgados y lógica en Actions.
- Documentar contratos de datos para alineación backend-simulator.
- Soportar monitoreo global eficiente con polling unificado cada 30 segundos.

**Non-Goals:**
- Implementar el dominio DDD del tracking-simulator.
- Resolver Google Maps/polyline en backend.
- Construir frontend del módulo.
- Exponer endpoints del simulador directamente al frontend.

## Decisions

1. Backend como API Gateway obligatorio
- Decisión: el frontend consume solo endpoints de backend.
- Racional: reduce acoplamiento, permite versionar y proteger contratos en un punto único.
- Alternativas consideradas:
  - Frontend -> simulator directo: descartado por acoplamiento y exposición de detalles internos.
  - Backend y simulator expuestos en paralelo al frontend: descartado por fragmentación de contratos.

2. Separación de capas en backend (Routes -> Controllers -> Requests/Actions/Resources)
- Decisión: mantener validación en FormRequest, caso de uso en Action y serialización en Resource.
- Racional: facilita mantenibilidad, pruebas futuras y claridad de responsabilidades.
- Alternativas consideradas:
  - Lógica en controladores: descartado por baja escalabilidad y mayor deuda técnica.

3. Cliente HTTP interno dedicado (`TrackingSimulatorClient`)
- Decisión: encapsular integración remota en una sola abstracción interna.
- Racional: centraliza timeout, retry, headers, trazabilidad y mapeo de errores remotos.
- Alternativas consideradas:
  - Usar `Http::` directamente en Actions: descartado por duplicación de configuración.
  - Integración vía cola asíncrona en esta fase: diferida para fases posteriores.

4. Contrato JSON uniforme del backend
- Decisión: todas las respuestas de endpoints del módulo usarán `data`, `message`, `status`.
- Racional: coherencia de consumo en frontend y manejo homogéneo de errores.
- Alternativas consideradas:
  - Formato libre por endpoint: descartado por inconsistencia y complejidad en cliente.

5. Propiedad del dominio de rutas/polyline en tracking-simulator
- Decisión: backend no calcula rutas ni interactúa con Google API para simulación.
- Racional: evita duplicación de lógica y preserva límites de dominio.
- Alternativas consideradas:
  - Backend calcula polyline: descartado por violación de ownership del dominio de simulación.

6. Contratos REST del módulo 1
- Endpoints backend propuestos:
  - `POST /api/simulator/services/generate`
  - `POST /api/simulator/simulation/start`
  - `POST /api/simulator/simulation/stop`
  - `GET /api/services`
  - `GET /api/services/tracking`
  - `GET /api/services/{id}`
  - `GET /api/services/{id}/tracking`
  - `GET /api/simulator/health`
- Requests esperadas:
  - `POST /api/simulator/services/generate`
    - request: `{ "count": number }`
    - response: `{ "data": { "requested": number, "created": number, "services": [...] }, "message": string, "status": boolean }`
  - `POST /api/simulator/simulation/start`
    - request: `{}` (sin parámetros de selección; inicia simulación para todos los servicios registrados)
    - response: `{ "data": { "job_id"?: string, "started_at": string }, "message": string, "status": boolean }`
  - `POST /api/simulator/simulation/stop`
    - request: `{}` (sin parámetros; detiene la simulación activa global)
    - response: `{ "data": { "stopped_at": string, "was_running": boolean }, "message": string, "status": boolean }`
  - `GET /api/services`
    - response: `{ "data": [ServiceResource], "message": string, "status": boolean }`
  - `GET /api/services/tracking`
    - request: sin parámetros de entrada
    - response: `{ "data": [ServiceTrackingResource], "message": string, "status": boolean }`
  - `GET /api/services/{id}`
    - response: `{ "data": ServiceResource, "message": string, "status": boolean }`
  - `GET /api/services/{id}/tracking`
    - response: `{ "data": [TrackingResource], "message": string, "status": boolean }`
  - `GET /api/simulator/health`
    - response: `{ "data": { "simulator": "up"|"down" }, "message": string, "status": boolean }`

1. DTOs de integración
- `GenerateServicesInputDto`: `count`.
- `StartSimulationInputDto`: sin atributos de entrada (disparo global para todos los servicios registrados).
- `StopSimulationInputDto`: sin atributos de entrada (detención global de simulación activa).
- `ServicesTrackingRowDto`: `service_id`, `tracking_id`, `name`, `start_time`, `end_time`, `polyline`, `latitude`, `longitude`.
- `ServiceDto`: `id`, `name`, `start_time`, `end_time`, `polyline`.
- `TrackingPointDto`: `service_id`, `latitude`, `longitude`, `created_at`.

1. Estrategia de actualización para live tracking
- Decisión: el frontend consumirá `GET /api/services/tracking` como endpoint agregado de monitoreo global.
- Racional: evita llamadas N+1 por servicio cuando existen cientos o miles de servicios activos.
- Operación: polling cada 30 segundos para refresco de datos en cliente.
- Alternativas consideradas:
  - Polling por servicio con `GET /api/services/{id}/tracking`: descartado por alto costo y baja escalabilidad.
  - WebSockets desde el inicio: diferido para fase posterior.

1. Arquitectura por servicio
- backend:
  - Define y expone contratos públicos.
  - Valida entrada, orquesta flujos y traduce respuestas.
  - Centraliza integración HTTP con simulator.
- tracking-simulator:
  - Ejecuta generación de servicios y simulación de tracking.
  - Decodifica polyline, genera desplazamiento y persiste histórico.
  - Expone API interna solo para backend.
- frontend:
  - Consume únicamente contratos backend.
  - Para monitoreo global usa polling de `GET /api/services/tracking` cada 30 segundos.
- infraestructura:
  - Conectividad backend -> simulator por red interna Docker.
  - Configuración de `TRACKING_SIMULATOR_BASE_URL` en backend.

## Risks / Trade-offs

- [Contrato backend-simulator diverge de implementación real] -> Mitigación: versionar contrato en OpenSpec y validar payloads en ambos lados antes de implementar frontend.
- [Errores remotos poco claros para frontend] -> Mitigación: mapear excepciones del cliente HTTP a mensajes estables y `status=false`.
- [Timeouts por simulación prolongada] -> Mitigación: definir timeouts diferenciados y considerar respuesta asincrónica en siguiente fase.
- [Acoplamiento accidental del frontend al simulator] -> Mitigación: no publicar URL del simulator al frontend y revisar rutas expuestas.
- [Cambios de esquema en tracking sin coordinación] -> Mitigación: fijar DTOs mínimos y mantener compatibilidad aditiva.

## Migration Plan

1. Publicar endpoints backend del módulo 1 como adición no disruptiva.
2. Incorporar cliente HTTP interno con feature-flag/config de URL base.
3. Desplegar con simulador inicialmente en modo stub o health-check mínimo.
4. Activar integración completa cuando simulator implemente contrato acordado.
5. Monitorear errores de integración y latencia para ajustar retry/timeout.

Rollback:
- Desactivar rutas del módulo 1 mediante configuración o revert del despliegue.
- Mantener intactos endpoints existentes de autenticación y dominio previo.
- Restaurar configuración anterior de variables de entorno si falla conectividad.

Cambios de base de datos:
- Esta fase no introduce migraciones nuevas en backend.
- Las tablas `services` y `tracking` se consideran contrato de dominio del simulador y serán gestionadas en su servicio cuando corresponda.

## Open Questions

- ¿El inicio de simulación debe ser síncrono o devolver siempre un identificador de proceso asíncrono?
 se ejecutara un job cada 30 segundos en tracking-simulator para procesar la simulación de todos los servicios activos, por lo que el backend puede devolver un job_id y started_at para seguimiento.

  y cada 30 segundos el frontend puede refrescar el estado de la simulación mediante `GET /api/services/tracking` para ver los últimos puntos generados.

- ¿Qué política exacta de retry y backoff se adoptará para errores 5xx del simulador?
  se definirá un retry con backoff exponencial limitado a 3 intentos, y si persiste el error, el backend devolverá un mensaje claro al frontend con `status=false` y un código HTTP coherente (por ejemplo, 503 Service Unavailable).

- ¿Se exigirá paginación desde el primer release para `GET /api/services/tracking`?
  no, se asumirá que el simulador puede devolver todos los puntos de tracking en una sola respuesta para monitoreo global, y el frontend hará polling cada 30 segundos. Si en el futuro se requiere paginación por volumen de datos, se podrá introducir sin romper contrato.
- ¿El endpoint de health del simulador debe incluir versión de contrato además del estado?
  No, por simplicidad inicial se reportará solo `up` o `down`. La versión de contrato se documentará en OpenSpec y se asumirá que el backend y simulator están alineados.
