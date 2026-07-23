## ADDED Requirements

### Requirement: El backend SHALL exponer endpoint para generar servicios del simulador
El backend SHALL exponer `POST /api/simulator/services/generate` como contrato estable para solicitar la creación de servicios en el tracking-simulator.

#### Scenario: Generación exitosa de servicios
- **WHEN** un cliente autenticado envía un payload válido que contiene únicamente `count`
- **THEN** el backend valida la entrada, delega al tracking-simulator y responde con `status=true`, `message` descriptivo y `data` con el resumen de servicios creados

#### Scenario: Campos de horario no permitidos en request
- **WHEN** un cliente autenticado envía `start_time` o `end_time` en `POST /api/simulator/services/generate`
- **THEN** el backend rechaza la solicitud con HTTP `422` porque esos campos son asignados automáticamente por el tracking-simulator

#### Scenario: Validación de entrada inválida
- **WHEN** un cliente autenticado envía una cantidad fuera de rango o un payload inválido
- **THEN** el backend responde con HTTP `422`, `status=false`, `message` de error y `data` nulo o estructura de errores validada

### Requirement: El backend SHALL exponer endpoint para iniciar simulación
El backend SHALL exponer `POST /api/simulator/simulation/start` para iniciar la simulación sin revelar detalles internos del tracking-simulator.

#### Scenario: Inicio de simulación exitoso
- **WHEN** un cliente autenticado invoca `POST /api/simulator/simulation/start` sin parámetros de selección
- **THEN** el backend delega al tracking-simulator la ejecución de tracking para todos los servicios registrados y devuelve confirmación con `status=true`, `message` de éxito y `data` con metadatos de ejecución

#### Scenario: Selección manual de servicios no permitida
- **WHEN** un cliente autenticado envía `service_ids` u otro criterio de subset en `POST /api/simulator/simulation/start`
- **THEN** el backend rechaza la solicitud con HTTP `422` porque el contrato solo permite ejecución global para todos los servicios registrados

#### Scenario: Error remoto al iniciar simulación
- **WHEN** el tracking-simulator responde con error o no está disponible
- **THEN** el backend responde con un error controlado, `status=false`, `message` claro para cliente y un código HTTP coherente con el fallo

### Requirement: El backend SHALL exponer endpoint para detener simulación
El backend SHALL exponer `POST /api/simulator/simulation/stop` para detener la simulación activa sin revelar detalles internos del tracking-simulator.

#### Scenario: Detención exitosa de simulación activa
- **WHEN** un cliente autenticado invoca `POST /api/simulator/simulation/stop` sin parámetros y existe una simulación en ejecución
- **THEN** el backend delega al tracking-simulator la detención global y responde con `status=true`, `message` de éxito y `data` con metadatos de detención

#### Scenario: Solicitud de detención sin simulación activa
- **WHEN** un cliente autenticado invoca `POST /api/simulator/simulation/stop` y no hay simulación en ejecución
- **THEN** el backend responde de forma estable con `status=true` y `message` informativo indicando que no había ejecución activa

#### Scenario: Parámetros de selección no permitidos en stop
- **WHEN** un cliente autenticado envía `service_ids` u otros criterios de selección en `POST /api/simulator/simulation/stop`
- **THEN** el backend rechaza la solicitud con HTTP `422` porque el contrato solo permite detención global sin parámetros

#### Scenario: Error remoto al detener simulación
- **WHEN** el tracking-simulator responde con error o no está disponible durante la detención
- **THEN** el backend responde con un error controlado, `status=false`, `message` claro para cliente y un código HTTP coherente con el fallo

### Requirement: El backend MUST incluir endpoint de salud del simulador
El backend MUST exponer `GET /api/simulator/health` para verificar disponibilidad del tracking-simulator desde el punto de vista del backend.

#### Scenario: Simulator disponible
- **WHEN** el backend puede contactar al tracking-simulator y este responde correctamente
- **THEN** el backend responde con HTTP `200`, `status=true` y `data.simulator="up"`

#### Scenario: Simulator no disponible
- **WHEN** el backend no puede contactar al tracking-simulator o recibe timeout
- **THEN** el backend responde con estado de error controlado y `data.simulator="down"` sin exponer trazas internas

### Requirement: El backend MUST encapsular la integración HTTP en un cliente dedicado
Las llamadas al tracking-simulator MUST ejecutarse mediante un cliente HTTP interno dedicado que centralice URL base, headers, timeout, retry y mapeo de errores.

#### Scenario: Configuración unificada de integración
- **WHEN** cualquier caso de uso del módulo simulador invoca al tracking-simulator
- **THEN** la llamada utiliza la configuración centralizada del cliente y no configuración ad-hoc en controladores o acciones

#### Scenario: Mapeo estable de errores remotos
- **WHEN** el tracking-simulator responde con errores 4xx/5xx o timeout
- **THEN** el cliente interno transforma el error a una respuesta backend consistente con `data`, `message`, `status`

### Requirement: Los endpoints del simulador en backend MUST requerir autenticación
Los endpoints de orquestación del módulo simulador MUST estar protegidos por autenticación Sanctum cuando formen parte de la API de negocio consumida por frontend.

#### Scenario: Solicitud autenticada
- **WHEN** un cliente invoca un endpoint del módulo simulador con token válido
- **THEN** el backend permite la ejecución normal del caso de uso

#### Scenario: Solicitud no autenticada
- **WHEN** un cliente invoca un endpoint protegido sin token válido
- **THEN** el backend responde con HTTP `401` y no ejecuta llamadas al tracking-simulator
