## ADDED Requirements

### Requirement: El simulador SHALL persistir servicios ficticios
El simulador SHALL generar y almacenar servicios ficticios con `id`, `name`, `start_time`, `end_time` y `polyline` en la tabla `services`.

#### Scenario: Generacion exitosa de servicios
- **WHEN** el backend solicita generar una cantidad valida de servicios
- **THEN** el simulador crea los registros correspondientes y devuelve un resumen estable de la operacion

#### Scenario: Servicios sin horarios manuales
- **WHEN** se generan servicios nuevos
- **THEN** el simulador asigna automaticamente `start_time` y `end_time` segun su logica interna y no requiere que el backend los envíe

### Requirement: El simulador SHALL obtener o construir una ruta base mediante un proveedor encapsulado
El simulador SHALL encapsular la obtencion o construccion de rutas base mediante un proveedor externo, idealmente `GoogleMapsRouteProvider`, para producir la polyline del servicio.

#### Scenario: Ruta base disponible
- **WHEN** el simulador necesita construir la ruta de un servicio
- **THEN** el proveedor devuelve una polyline codificada utilizable para la simulacion

#### Scenario: Falla del proveedor de rutas
- **WHEN** el proveedor externo no responde o retorna error
- **THEN** el simulador responde con un error controlado y no expone detalles internos del proveedor

### Requirement: El simulador SHALL ejecutar tracking continuo mientras la simulacion este activa
El simulador SHALL generar puntos GPS de forma continua mientras la simulacion permanezca activa, reprogramando la ejecucion cada 30 segundos.

#### Scenario: Tracking activo
- **WHEN** la simulacion esta activa
- **THEN** el simulador genera un nuevo tick de tracking, persiste nuevos puntos y reprograma la ejecucion para continuar

#### Scenario: Tracking detenido
- **WHEN** la simulacion se detiene
- **THEN** el simulador deja de reprogramar nuevos ticks y finaliza la ejecucion activa sin borrar el historico

### Requirement: El simulador SHALL persistir historico completo de tracking
El simulador SHALL almacenar cada punto GPS en la tabla `tracking` con `service_id`, `latitude`, `longitude` y `created_at` sin sobrescribir registros previos.

#### Scenario: Nuevo punto de tracking
- **WHEN** un vehiculo simulado avanza por la ruta
- **THEN** el simulador persiste un nuevo registro de tracking asociado al servicio correspondiente

#### Scenario: Sincronizacion con historico existente
- **WHEN** un servicio ya tiene puntos previos
- **THEN** los nuevos puntos se agregan al historico manteniendo el orden temporal

### Requirement: El simulador SHALL exponer endpoints internos para orquestacion por backend
El simulador SHALL exponer endpoints internos para generar servicios, iniciar simulacion, detener simulacion y verificar salud, consumidos exclusivamente por el backend.

#### Scenario: Generar servicios via API interna
- **WHEN** el backend invoca `POST /api/simulator/services/generate`
- **THEN** el simulador crea servicios y retorna un payload compatible con el contrato interno acordado

#### Scenario: Iniciar simulacion via API interna
- **WHEN** el backend invoca `POST /api/simulator/simulation/start`
- **THEN** el simulador activa el estado de simulacion y dispara el primer tick de tracking

#### Scenario: Detener simulacion via API interna
- **WHEN** el backend invoca `POST /api/simulator/simulation/stop`
- **THEN** el simulador desactiva el estado de simulacion y detiene la reprogramacion de ticks

#### Scenario: Consultar salud del simulador
- **WHEN** el backend invoca `GET /api/simulator/health`
- **THEN** el simulador responde con disponibilidad actual sin exponer trazas internas

### Requirement: El simulador MUST devolver datos compatibles con el read model del backend
El simulador MUST retornar servicios y trackings con la forma esperada por el backend, incluyendo el agregado de servicios con trackings anidados para `GET /api/services/tracking`.

#### Scenario: Consulta agregada de tracking
- **WHEN** el backend consulta el agregado de servicios con trackings
- **THEN** el simulador devuelve objetos con `id`, `name`, `start_time`, `end_time`, `polyline` y `trackings` anidados

#### Scenario: Consulta de tracking por servicio
- **WHEN** el backend consulta el tracking historico de un servicio especifico
- **THEN** el simulador devuelve puntos con `latitude`, `longitude` y `created_at` compatibles con el resource del backend