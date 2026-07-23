## 1. Backend - Contratos HTTP del módulo simulador

- [x] 1.1 Definir rutas en `routes/api.php` para `POST /api/simulator/services/generate`, `POST /api/simulator/simulation/start`, `POST /api/simulator/simulation/stop` y `GET /api/simulator/health` | CA: rutas disponibles bajo `/api` con middleware de autenticación definido | Dep: ninguna | Prioridad: alta
- [x] 1.2 Crear `GenerateServicesRequest` con validación exclusiva de `count` | CA: request rechaza payload inválido con `422`, permite payload válido y no admite `start_time`/`end_time` de entrada | Dep: 1.1 | Prioridad: alta
- [x] 1.3 Crear `StartSimulationRequest` sin parámetros de selección | CA: request acepta invocación vacía para disparo global y rechaza `service_ids`/criterios de subset con `422` | Dep: 1.1 | Prioridad: alta
- [x] 1.4 Crear `StopSimulationRequest` sin parámetros de selección | CA: request acepta invocación vacía para detención global y rechaza `service_ids`/criterios de subset con `422` | Dep: 1.1 | Prioridad: alta
- [x] 1.5 Implementar controlador delgado para endpoints del simulador delegando en Actions | CA: controller no contiene lógica de negocio, solo orquestación HTTP y respuestas | Dep: 1.2, 1.3, 1.4 | Prioridad: alta

## 2. Backend - Integración interna con tracking-simulator

- [x] 2.1 Implementar `TrackingSimulatorClient` con URL base configurable por entorno | CA: cliente consume `TRACKING_SIMULATOR_BASE_URL` y centraliza headers comunes | Dep: 1.1 | Prioridad: alta
- [x] 2.2 Configurar timeouts, retries y mapeo de errores remotos en el cliente | CA: errores 4xx/5xx/timeout se traducen a excepciones controladas y mensajes claros | Dep: 2.1 | Prioridad: alta
- [x] 2.3 Implementar `GenerateServicesAction` usando `TrackingSimulatorClient` | CA: action delega la generación al simulador y retorna contrato backend estable | Dep: 2.2 | Prioridad: alta
- [x] 2.4 Implementar `StartSimulationAction` usando `TrackingSimulatorClient` | CA: action dispara simulación para todos los servicios registrados y retorna confirmación con `data`, `message`, `status` | Dep: 2.2 | Prioridad: alta
- [x] 2.5 Implementar health check del simulador vía cliente interno | CA: endpoint backend reporta `up/down` sin exponer detalles internos | Dep: 2.2 | Prioridad: media
- [x] 2.6 Implementar `StopSimulationAction` usando `TrackingSimulatorClient` | CA: action detiene la simulación global activa y retorna confirmación con `data`, `message`, `status` | Dep: 2.2 | Prioridad: alta

## 3. Backend - Modelo de lectura de servicios y tracking

- [x] 3.1 Definir rutas para `GET /api/services`, `GET /api/services/tracking`, `GET /api/services/{id}`, `GET /api/services/{id}/tracking` | CA: rutas públicas del módulo quedan disponibles con protección y versionado actual | Dep: 1.1 | Prioridad: alta
- [x] 3.2 Implementar Actions de consulta de servicios y tracking | CA: actions resuelven listado, detalle, tracking histórico por servicio y tracking agregado global | Dep: 3.1 | Prioridad: alta
- [x] 3.3 Crear DTOs (`GenerateServicesInputDto`, `StartSimulationInputDto`, `StopSimulationInputDto`, `ServiceWithTrackingsDto`, `ServiceDto`, `TrackingPointDto`) | CA: `GenerateServicesInputDto` contiene solo `count`; `StartSimulationInputDto` y `StopSimulationInputDto` no exponen selección por ids; `ServiceWithTrackingsDto` define `id`, `name`, `start_time`, `end_time`, `polyline`, `trackings`; `TrackingPointDto` define `latitude`, `longitude`, `created_at`; `ServiceDto` refleja datos de servicio; el resto encapsula contratos sin filtrar modelos internos | Dep: 2.3, 2.4, 2.6, 3.2 | Prioridad: media
- [x] 3.4 Crear `ServiceResource`, `TrackingResource` y recurso agregado por servicio para normalizar serialización | CA: salida contiene campos contractuales y omite atributos internos, incluyendo colección `trackings` anidada por servicio | Dep: 3.2, 3.3 | Prioridad: alta
- [x] 3.6 Crear respuesta agregada para `GET /api/services/tracking` (servicios con trackings anidados) | CA: endpoint retorna colección de servicios con `id`, `name`, `start_time`, `end_time`, `polyline` y `trackings` (cada item con `latitude`, `longitude`, `created_at`) | Dep: 3.2, 3.3, 3.4 | Prioridad: alta
- [x] 3.5 Unificar respuestas JSON con patrón `data`, `message`, `status` en endpoints del módulo | CA: todas las respuestas exitosas y de error respetan el mismo shape | Dep: 1.5, 2.3, 2.4, 2.6, 3.4 | Prioridad: alta

## 4. Tracking-simulator - Contrato objetivo para fase siguiente

- [x] 4.1 Documentar endpoints internos que deberá exponer el simulador para generación y ejecución | CA: contrato request/response acordado y trazable en OpenSpec | Dep: 2.1, 2.3, 2.4 | Prioridad: media
- [x] 4.2 Definir contrato de persistencia para `services` y `tracking` alineado con backend | CA: campos mínimos y semántica temporal documentados y estables | Dep: 4.1 | Prioridad: media
- [x] 4.3 Definir política de errores remotos y códigos HTTP esperados desde simulator | CA: catálogo de errores backend-simulator documentado para mapping consistente | Dep: 4.1 | Prioridad: media

## 5. Infraestructura - Conectividad y configuración

- [x] 5.1 Añadir variable de entorno `TRACKING_SIMULATOR_BASE_URL` en backend | CA: backend puede resolver la URL del simulador por entorno sin hardcode | Dep: ninguna | Prioridad: alta
- [x] 5.2 Ajustar Docker Compose para conectividad interna backend -> simulator | CA: comunicación HTTP interna funcional por nombre de servicio/red Docker | Dep: 5.1 | Prioridad: alta
- [x] 5.3 Documentar configuración operativa mínima para levantar integración local | CA: README/guía técnica incluye variables, puertos y orden de arranque | Dep: 5.2 | Prioridad: media

## 6. Frontend - Consumo futuro sin acoplamiento

- [x] 6.1 Publicar contrato de endpoints backend para consumo frontend (sin implementación UI) | CA: frontend dispone de especificación clara y no usa URL del simulator | Dep: 3.5 | Prioridad: media
- [x] 6.2 Definir lineamientos de integración Axios/Pinia para fase de implementación | CA: lineamientos técnicos documentados con manejo uniforme de `data/message/status` | Dep: 6.1 | Prioridad: baja
- [x] 6.3 Definir estrategia de polling global cada 30 segundos usando `GET /api/services/tracking` | CA: frontend evita consultas 1 por 1 de tracking por servicio y refresca el mapa con endpoint agregado | Dep: 3.6, 6.1 | Prioridad: media
