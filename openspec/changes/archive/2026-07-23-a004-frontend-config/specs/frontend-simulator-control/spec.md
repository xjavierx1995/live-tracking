## ADDED Requirements

### Requirement: Verificar estado del simulador
El sistema SHALL permitir al usuario verificar si el simulador está funcionando.

#### Scenario: Simuladores activo
- **WHEN** el usuario accede al dashboard y el simulador está ejecutándose
- **THEN** el sistema muestra indicador visual de que el simulador está activo

#### Scenario: Simuladores inactivo
- **WHEN** el usuario accede al dashboard y el simulador no está ejecutándose
- **THEN** el sistema muestra indicador visual de que el simulador está inactivo

### Requirement: Generar servicios simulados
El sistema SHALL permitir al usuario generar una cantidad específica de servicios simulados.

#### Scenario: Generación exitosa de servicios
- **WHEN** el usuario ingresa una cantidad y hace clic en "Generar servicios"
- **THEN** el sistema envía la solicitud al endpoint `/api/simulator/services/generate` y muestra mensaje de éxito

#### Scenario: Generación con cantidad inválida
- **WHEN** el usuario intenta generar servicios con cantidad menor a 1 o no numérica
- **THEN** el sistema muestra mensaje de error indicando que la cantidad debe ser mayor a 0

### Requirement: Iniciar simulación
El sistema SHALL permitir al usuario iniciar la simulación de tracking.

#### Scenario: Inicio exitoso de simulación
- **WHEN** el usuario hace clic en "Iniciar simulación"
- **THEN** el sistema envía solicitud al endpoint `/api/simulator/simulation/start`, actualiza el indicador de estado, y habilita el botón de detener

#### Scenario: Error al iniciar simulación
- **WHEN** la solicitud a `/api/simulator/simulation/start` falla
- **THEN** el sistema muestra mensaje de error indicando la falla

### Requirement: Detener simulación
El sistema SHALL permitir al usuario detener la simulación en curso.

#### Scenario: Detención exitosa de simulación
- **WHEN** el usuario hace clic en "Detener simulación"
- **THEN** el sistema envía solicitud al endpoint `/api/simulator/simulation/stop`, actualiza el indicador de estado, y deshabilita el botón de detener

#### Scenario: Botón iniciar deshabilitado durante simulación activa
- **WHEN** la simulación está en curso
- **THEN** el sistema deshabilita el botón de iniciar y habilita el botón de detener

#### Scenario: Botón detener deshabilitado cuando no hay simulación activa
- **WHEN** no hay simulación en curso
- **THEN** el sistema deshabilita el botón de detener y habilita el botón de iniciar

### Requirement: Controles del simulador visibles solo para usuarios autenticados
El sistema SHALL mostrar los controles del simulador únicamente a usuarios autenticados.

#### Scenario: Controles visibles para usuario autenticado
- **WHEN** un usuario autenticado está en el dashboard
- **THEN** el sistema muestra los controles del simulador (generar, iniciar, detener)

#### Scenario: Controles no visibles para usuario no autenticado
- **WHEN** un usuario no autenticado intenta acceder a los controles del simulador
- **THEN** el sistema redirige a login antes de acceder a los controles