# Capability: backend-simulator-entrypoint

## Purpose

Define stable backend orchestration contracts for simulator operations (generate services, start/stop simulation, and health checks) through authenticated API endpoints.

## Requirements

### Requirement: El backend SHALL exponer endpoint para generar servicios del simulador
El backend SHALL exponer `POST /api/simulator/services/generate` como contrato estable para solicitar la creacion de servicios en el tracking-simulator.

#### Scenario: Generacion exitosa de servicios
- **WHEN** un cliente autenticado envia un payload valido que contiene unicamente `count`
- **THEN** el backend valida la entrada, delega al tracking-simulator y responde con `status=true`, `message` descriptivo y `data` con el resumen de servicios creados

#### Scenario: Campos de horario no permitidos en request
- **WHEN** un cliente autenticado envia `start_time` o `end_time` en `POST /api/simulator/services/generate`
- **THEN** el backend rechaza la solicitud con HTTP `422` porque esos campos son asignados automaticamente por el tracking-simulator

#### Scenario: Validacion de entrada invalida
- **WHEN** un cliente autenticado envia una cantidad fuera de rango o un payload invalido
- **THEN** el backend responde con HTTP `422`, `status=false`, `message` de error y `data` nulo o estructura de errores validada

### Requirement: El backend SHALL exponer endpoint para iniciar simulacion
El backend SHALL exponer `POST /api/simulator/simulation/start` para iniciar la simulacion sin revelar detalles internos del tracking-simulator.

#### Scenario: Inicio de simulacion exitoso
- **WHEN** un cliente autenticado invoca `POST /api/simulator/simulation/start` sin parametros de seleccion
- **THEN** el backend delega al tracking-simulator la ejecucion de tracking para todos los servicios registrados y devuelve confirmacion con `status=true`, `message` de exito y `data` con metadatos de ejecucion

#### Scenario: Seleccion manual de servicios no permitida
- **WHEN** un cliente autenticado envia `service_ids` u otro criterio de subset en `POST /api/simulator/simulation/start`
- **THEN** el backend rechaza la solicitud con HTTP `422` porque el contrato solo permite ejecucion global para todos los servicios registrados

#### Scenario: Error remoto al iniciar simulacion
- **WHEN** el tracking-simulator responde con error o no esta disponible
- **THEN** el backend responde con un error controlado, `status=false`, `message` claro para cliente y un codigo HTTP coherente con el fallo

### Requirement: El backend SHALL exponer endpoint para detener simulacion
El backend SHALL exponer `POST /api/simulator/simulation/stop` para detener la simulacion activa sin revelar detalles internos del tracking-simulator.

#### Scenario: Detencion exitosa de simulacion activa
- **WHEN** un cliente autenticado invoca `POST /api/simulator/simulation/stop` sin parametros y existe una simulacion en ejecucion
- **THEN** el backend delega al tracking-simulator la detencion global y responde con `status=true`, `message` de exito y `data` con metadatos de detencion

#### Scenario: Solicitud de detencion sin simulacion activa
- **WHEN** un cliente autenticado invoca `POST /api/simulator/simulation/stop` y no hay simulacion en ejecucion
- **THEN** el backend responde de forma estable con `status=true` y `message` informativo indicando que no habia ejecucion activa

#### Scenario: Parametros de seleccion no permitidos en stop
- **WHEN** un cliente autenticado envia `service_ids` u otros criterios de seleccion en `POST /api/simulator/simulation/stop`
- **THEN** el backend rechaza la solicitud con HTTP `422` porque el contrato solo permite detencion global sin parametros

#### Scenario: Error remoto al detener simulacion
- **WHEN** el tracking-simulator responde con error o no esta disponible durante la detencion
- **THEN** el backend responde con un error controlado, `status=false`, `message` claro para cliente y un codigo HTTP coherente con el fallo

### Requirement: El backend MUST incluir endpoint de salud del simulador
El backend MUST exponer `GET /api/simulator/health` para verificar disponibilidad del tracking-simulator desde el punto de vista del backend.

#### Scenario: Simulator disponible
- **WHEN** el backend puede contactar al tracking-simulator y este responde correctamente
- **THEN** el backend responde con HTTP `200`, `status=true` y `data.simulator="up"`

#### Scenario: Simulator no disponible
- **WHEN** el backend no puede contactar al tracking-simulator o recibe timeout
- **THEN** el backend responde con estado de error controlado y `data.simulator="down"` sin exponer trazas internas

### Requirement: El backend MUST encapsular la integracion HTTP en un cliente dedicado
Las llamadas al tracking-simulator MUST ejecutarse mediante un cliente HTTP interno dedicado que centralice URL base, headers, timeout, retry y mapeo de errores.

#### Scenario: Configuracion unificada de integracion
- **WHEN** cualquier caso de uso del modulo simulador invoca al tracking-simulator
- **THEN** la llamada utiliza la configuracion centralizada del cliente y no configuracion ad-hoc en controladores o acciones

#### Scenario: Mapeo estable de errores remotos
- **WHEN** el tracking-simulator responde con errores 4xx/5xx o timeout
- **THEN** el cliente interno transforma el error a una respuesta backend consistente con `data`, `message`, `status`

### Requirement: Los endpoints del simulador en backend MUST requerir autenticacion
Los endpoints de orquestacion del modulo simulador MUST estar protegidos por autenticacion Sanctum cuando formen parte de la API de negocio consumida por frontend.

#### Scenario: Solicitud autenticada
- **WHEN** un cliente invoca un endpoint del modulo simulador con token valido
- **THEN** el backend permite la ejecucion normal del caso de uso

#### Scenario: Solicitud no autenticada
- **WHEN** un cliente invoca un endpoint protegido sin token valido
- **THEN** el backend responde con HTTP `401` y no ejecuta llamadas al tracking-simulator
