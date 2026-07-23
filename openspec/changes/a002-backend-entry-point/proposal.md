## Why

El módulo de simulación necesita un punto de entrada único y estable para que el frontend no se acople al microservicio de tracking. Esta fase es necesaria ahora para fijar contratos HTTP y responsabilidades antes de implementar la lógica DDD del simulador.

## What Changes

- Objetivo:
  - Convertir el backend en API Gateway y orquestador del módulo 1 (simulador), manteniendo Sanctum como control de acceso.
- Alcance:
  - Exponer endpoints backend para generar servicios, iniciar/detener simulación y consultar servicios/tracking.
  - Incorporar un endpoint agregado `GET /api/services/tracking` para monitoreo global eficiente.
  - Estandarizar refresco del frontend mediante polling cada 30 segundos sobre el endpoint agregado.
  - Encapsular la integración HTTP con tracking-simulator en un cliente interno.
  - Definir contratos de request/response estables con patrón `data`, `message`, `status`.
  - Preparar `FormRequest`, `Actions`, `Resources` y DTOs para desacoplar controladores de lógica de negocio.
  - Documentar que la responsabilidad de polyline/Google y simulación vive en tracking-simulator.
- No objetivos:
  - No implementar en esta fase la lógica DDD interna del tracking-simulator.
  - No integrar Google Maps en backend.
  - No construir frontend ni exponer tracking-simulator al frontend.
- Riesgos:
  - Inestabilidad del contrato HTTP backend-simulator.
  - Mezcla de lógica de negocio en controladores.
  - Duplicación de responsabilidades entre backend y simulator.
  - Manejo inconsistente de errores remotos.
- Impacto por servicio:
  - backend: nuevas rutas, controladores delgados, requests, actions, resources, DTOs y cliente HTTP de simulación.
  - tracking-simulator: sin implementación en esta fase; se establece contrato esperado para fase posterior.
  - frontend: sin cambios funcionales; seguirá consumiendo solo backend.
  - infraestructura: ajustes de comunicación interna en Docker Compose y variable de entorno para URL base del simulador.
- Dependencias:
  - Sanctum operativo en backend.
  - MySQL disponible para persistencia.
  - Endpoint interno de tracking-simulator accesible desde la red Docker.
  - Variables de entorno separadas para URL del simulador y credenciales externas del simulador.
- Compatibilidad y migración de API:
  - Cambio aditivo (sin ruptura) para capacidades existentes.
  - Nuevos endpoints se publican bajo `/api` y no alteran contratos de autenticación actuales.
  - Sin versionado adicional en esta fase; se preserva compatibilidad hacia atrás.
- Criterios de aceptación:
  - Backend expone endpoints formales de servicios/simulación.
  - Validación de entrada mediante `FormRequest`.
  - Integración con simulador centralizada en cliente HTTP dedicado.
  - Frontend no depende del simulador.
  - Respuestas backend consistentes con `data`, `message`, `status`.
  - Separación de responsabilidades backend/simulator explícitamente documentada.

## Capabilities

### New Capabilities
- `initialize-simulation`: Contratos HTTP del backend para generar servicios, iniciar/detener simulación y exponer health del simulador sin acoplar al frontend.
- `entrypoint-service`: Contratos de consulta de servicios y tracking con respuestas normalizadas para consumo frontend.

### Modified Capabilities
- Ninguna en esta fase.

## Impact

- Código backend afectado en `routes/api.php`, `app/Http/Controllers`, `app/Http/Requests`, `app/Actions` y `app/Http/Resources`.
- Se añade cliente HTTP interno para `tracking-simulator` y configuración de entorno para su URL base.
- Se formaliza contrato backend-simulator para operaciones de generación de servicios, ejecución y detención de simulación.
- Se define contrato de lectura agregada de tracking para evitar consultas 1 por 1 por servicio.
- Se documenta explícitamente la propiedad del dominio de rutas/polyline en tracking-simulator.
