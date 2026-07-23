# Capability: backend-services-tracking-read-model

## Purpose

Define stable backend read contracts for services and tracking, including a global aggregated endpoint that returns services with nested tracking points for frontend monitoring.

## Requirements

### Requirement: El backend SHALL exponer listado de servicios para consumo frontend
El backend SHALL exponer `GET /api/services` como contrato estable para consultar servicios disponibles del modulo de simulacion.

#### Scenario: Consulta exitosa de servicios
- **WHEN** un cliente autenticado solicita el listado de servicios
- **THEN** el backend responde con HTTP `200` y una estructura `{ data: [services], message, status }` usando un recurso de salida normalizado

#### Scenario: Sin servicios disponibles
- **WHEN** no existen servicios en la fuente de datos
- **THEN** el backend responde con HTTP `200`, `status=true` y `data` como coleccion vacia

### Requirement: El backend SHALL exponer detalle de servicio por identificador
El backend SHALL exponer `GET /api/services/{id}` para consultar un servicio especifico usando un contrato estable.

#### Scenario: Servicio encontrado
- **WHEN** un cliente autenticado consulta un `id` existente
- **THEN** el backend responde con HTTP `200`, `status=true` y `data` con `id`, `name`, `start_time`, `end_time` y `polyline`

#### Scenario: Servicio no encontrado
- **WHEN** un cliente autenticado consulta un `id` inexistente
- **THEN** el backend responde con HTTP `404`, `status=false` y `message` descriptivo

### Requirement: El backend SHALL exponer tracking historico por servicio
El backend SHALL exponer `GET /api/services/{id}/tracking` para obtener el historico de posiciones asociadas a un servicio.

#### Scenario: Tracking disponible para el servicio
- **WHEN** un cliente autenticado consulta tracking de un servicio con posiciones registradas
- **THEN** el backend responde con HTTP `200`, `status=true` y `data` como coleccion de puntos con `latitude`, `longitude`, `created_at`

#### Scenario: Servicio valido sin tracking aun
- **WHEN** un cliente autenticado consulta tracking de un servicio que aun no tiene posiciones
- **THEN** el backend responde con HTTP `200`, `status=true` y `data` como coleccion vacia

### Requirement: El backend SHALL exponer tracking agregado por servicio para monitoreo global
El backend SHALL exponer `GET /api/services/tracking` para devolver en una sola respuesta una coleccion de servicios con sus trackings anidados (relacion 1:N), orientada a vistas globales de monitoreo.

#### Scenario: Consulta agregada de tracking exitosa
- **WHEN** un cliente autenticado consulta `GET /api/services/tracking`
- **THEN** el backend responde con HTTP `200`, `status=true` y `data` como coleccion de objetos de servicio con `id`, `name`, `start_time`, `end_time`, `polyline` y `trackings` (array de objetos con `latitude`, `longitude`, `created_at`)

#### Scenario: Polling global cada 30 segundos
- **WHEN** el frontend consulta `GET /api/services/tracking` cada `30` segundos
- **THEN** el backend entrega respuestas estables para refresco continuo con datos agrupados por servicio, sin exigir consultas 1 por 1 por servicio

#### Scenario: Endpoint sin parametros de entrada
- **WHEN** un cliente autenticado consulta `GET /api/services/tracking` enviando parametros de filtro no soportados
- **THEN** el backend ignora esos parametros o responde `422` segun politica definida, manteniendo contrato sin parametros de entrada

### Requirement: El backend MUST normalizar respuestas mediante recursos y DTOs
Las respuestas de servicios y tracking MUST pasar por recursos y DTOs para evitar exponer atributos internos no contractuales.

#### Scenario: Respuesta de servicio normalizada
- **WHEN** el backend serializa un servicio para endpoints de listado o detalle
- **THEN** solo incluye campos aprobados por contrato y mantiene estructura estable

#### Scenario: Respuesta de tracking normalizada
- **WHEN** el backend serializa puntos de tracking
- **THEN** emite una estructura homogenea de servicios con trackings anidados, compatible con el frontend sin depender del modelo interno

### Requirement: El backend MUST mantener formato de respuesta consistente
Todos los endpoints de consulta de servicios y tracking MUST responder con las claves `data`, `message` y `status`.

#### Scenario: Respuesta exitosa
- **WHEN** el backend completa exitosamente cualquier consulta de servicios o tracking
- **THEN** responde con `status=true`, `message` de exito y `data` con el resultado

#### Scenario: Respuesta con error
- **WHEN** ocurre un error de validacion, negocio o integracion al consultar servicios o tracking
- **THEN** el backend responde con `status=false`, `message` claro y `data` nulo o estructura de error definida
