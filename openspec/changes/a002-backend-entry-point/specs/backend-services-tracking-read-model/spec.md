## ADDED Requirements

### Requirement: El backend SHALL exponer listado de servicios para consumo frontend
El backend SHALL exponer `GET /api/services` como contrato estable para consultar servicios disponibles del módulo de simulación.

#### Scenario: Consulta exitosa de servicios
- **WHEN** un cliente autenticado solicita el listado de servicios
- **THEN** el backend responde con HTTP `200` y una estructura `{ data: [services], message, status }` usando un recurso de salida normalizado

#### Scenario: Sin servicios disponibles
- **WHEN** no existen servicios en la fuente de datos
- **THEN** el backend responde con HTTP `200`, `status=true` y `data` como colección vacía

### Requirement: El backend SHALL exponer detalle de servicio por identificador
El backend SHALL exponer `GET /api/services/{id}` para consultar un servicio específico usando un contrato estable.

#### Scenario: Servicio encontrado
- **WHEN** un cliente autenticado consulta un `id` existente
- **THEN** el backend responde con HTTP `200`, `status=true` y `data` con `id`, `name`, `start_time`, `end_time` y `polyline`

#### Scenario: Servicio no encontrado
- **WHEN** un cliente autenticado consulta un `id` inexistente
- **THEN** el backend responde con HTTP `404`, `status=false` y `message` descriptivo

### Requirement: El backend SHALL exponer tracking histórico por servicio
El backend SHALL exponer `GET /api/services/{id}/tracking` para obtener el histórico de posiciones asociadas a un servicio.

#### Scenario: Tracking disponible para el servicio
- **WHEN** un cliente autenticado consulta tracking de un servicio con posiciones registradas
- **THEN** el backend responde con HTTP `200`, `status=true` y `data` como colección de puntos con `service_id`, `latitude`, `longitude`, `created_at`

#### Scenario: Servicio válido sin tracking aún
- **WHEN** un cliente autenticado consulta tracking de un servicio que aún no tiene posiciones
- **THEN** el backend responde con HTTP `200`, `status=true` y `data` como colección vacía

### Requirement: El backend SHALL exponer tracking agregado de servicios para monitoreo global
El backend SHALL exponer `GET /api/services/tracking` para devolver en una sola respuesta un arreglo plano de filas que combinan datos de `services` y `tracking`, orientado a vistas globales de monitoreo.

#### Scenario: Consulta agregada de tracking exitosa
- **WHEN** un cliente autenticado consulta `GET /api/services/tracking`
- **THEN** el backend responde con HTTP `200`, `status=true` y `data` como colección de objetos con `service_id`, `tracking_id`, `name`, `start_time`, `end_time`, `polyline`, `latitude` y `longitude`

#### Scenario: Polling global cada 30 segundos
- **WHEN** el frontend consulta `GET /api/services/tracking` cada `30` segundos
- **THEN** el backend entrega respuestas estables para refresco continuo sin exigir consultas 1 por 1 por servicio

#### Scenario: Endpoint sin parámetros de entrada
- **WHEN** un cliente autenticado consulta `GET /api/services/tracking` enviando parámetros de filtro no soportados
- **THEN** el backend ignora esos parámetros o responde `422` según política definida, manteniendo contrato sin parámetros de entrada

### Requirement: El backend MUST normalizar respuestas mediante recursos y DTOs
Las respuestas de servicios y tracking MUST pasar por recursos y DTOs para evitar exponer atributos internos no contractuales.

#### Scenario: Respuesta de servicio normalizada
- **WHEN** el backend serializa un servicio para endpoints de listado o detalle
- **THEN** solo incluye campos aprobados por contrato y mantiene estructura estable

#### Scenario: Respuesta de tracking normalizada
- **WHEN** el backend serializa puntos de tracking
- **THEN** emite una estructura homogénea compatible con el frontend sin depender del modelo interno

### Requirement: El backend MUST mantener formato de respuesta consistente
Todos los endpoints de consulta de servicios y tracking MUST responder con las claves `data`, `message` y `status`.

#### Scenario: Respuesta exitosa
- **WHEN** el backend completa exitosamente cualquier consulta de servicios o tracking
- **THEN** responde con `status=true`, `message` de éxito y `data` con el resultado

#### Scenario: Respuesta con error
- **WHEN** ocurre un error de validación, negocio o integración al consultar servicios o tracking
- **THEN** el backend responde con `status=false`, `message` claro y `data` nulo o estructura de error definida
