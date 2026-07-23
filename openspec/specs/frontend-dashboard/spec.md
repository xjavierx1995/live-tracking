# Frontend Dashboard Spec

## Purpose
Proporcionar la interfaz de monitorización en tiempo real con mapa interactivo Leaflet y layout responsive.

## Requirements

### Requirement: Dashboard muestra mapa interactivo
El sistema SHALL mostrar un mapa Leaflet en el dashboard que permita visualizar las rutas de los servicios y sus puntos de tracking.

#### Scenario: Mapa carga correctamente
- **WHEN** el usuario accede al dashboard
- **THEN** el sistema carga el componente Leaflet Map centrado en una ubicación por defecto

#### Scenario: Mapa muestra polyline de servicio seleccionado
- **WHEN** el usuario selecciona un servicio de la lista lateral
- **THEN** el sistema dibuja la polyline de la ruta del servicio en el mapa

#### Scenario: Mapa muestra markers de tracking points
- **WHEN** el usuario selecciona un servicio que tiene puntos de tracking
- **THEN** el sistema muestra markers en las coordenadas de cada tracking point

### Requirement: Dashboard muestra información del usuario
El sistema SHALL mostrar en el header la información del usuario autenticado y opciones de logout.

#### Scenario: Header muestra nombre de usuario
- **WHEN** el usuario autenticado accede al dashboard
- **THEN** el sistema muestra el nombre del usuario en el header

#### Scenario: Logout desde header
- **WHEN** el usuario hace clic en "Cerrar sesión" en el header
- **THEN** el sistema ejecuta el logout y redirige a login

### Requirement: Dashboard tiene layout responsive
El sistema SHALL proporcionar un layout que funcione en diferentes tamaños de pantalla.

#### Scenario: Layout en escritorio
- **WHEN** el usuario accede desde escritorio (ancho > 1024px)
- **THEN** el sistema muestra sidebar de 300px y mapa ocupando el resto

#### Scenario: Layout en tablet
- **WHEN** el usuario accede desde tablet (ancho 768px - 1024px)
- **THEN** el sistema muestra sidebar colapsable y mapa en el espacio restante

#### Scenario: Layout en móvil
- **WHEN** el usuario accede desde móvil (ancho < 768px)
- **THEN** el sistema muestra solo el mapa con menú de hamburguesa para acceder a la lista de servicios
