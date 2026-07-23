## ADDED Requirements

### Requirement: Listado de servicios con trackings
El sistema SHALL mostrar una lista de todos los servicios disponibles con sus puntos de tracking asociados.

#### Scenario: Carga de servicios exitosa
- **WHEN** el usuario accede al dashboard
- **THEN** el sistema obtiene la lista de servicios con tracking del endpoint `/api/services/tracking` y los muestra en el sidebar

#### Scenario: Lista vacía de servicios
- **WHEN** no existen servicios en el sistema
- **THEN** el sistema muestra un mensaje indicando que no hay servicios disponibles

#### Scenario: Error al cargar servicios
- **WHEN** la solicitud a `/api/services/tracking` falla
- **THEN** el sistema muestra un mensaje de error y permite reintentar la carga

### Requirement: Selección de servicio
El sistema SHALL permitir al usuario seleccionar un servicio de la lista para ver sus detalles en el mapa.

#### Scenario: Click en servicio de la lista
- **WHEN** el usuario hace clic en un servicio de la lista lateral
- **THEN** el sistema marca el servicio como seleccionado, centra el mapa en la ruta del servicio, y dibuja la polyline

#### Scenario: Servicio seleccionado muestra sus trackings
- **WHEN** un servicio con trackings es seleccionado
- **THEN** el sistema muestra markers en las coordenadas de los tracking points y dibuja la polyline de la ruta

### Requirement: Actualización de servicios en tiempo real
El sistema SHALL permitir actualizar la lista de servicios para ver nuevos datos de tracking.

#### Scenario: Refresh manual de servicios
- **WHEN** el usuario hace clic en el botón de actualizar o dopo de un intervalo de tiempo
- **THEN** el sistema vuelve a cargar los servicios con sus trackings actualizados

### Requirement: Información de servicio en tooltip
El sistema SHALL mostrar información detallada del servicio al hacer hover o click en el marker.

#### Scenario: Hover sobre marker de servicio
- **WHEN** el usuario hace hover sobre un marker del mapa
- **THEN** el sistema muestra un tooltip con el nombre del servicio y hora de creación

#### Scenario: Click en marker de servicio
- **WHEN** el usuario hace clic en un marker del mapa
- **THEN** el sistema muestra un popup con información detallada del servicio (nombre, hora de inicio, hora de fin, número de trackings)