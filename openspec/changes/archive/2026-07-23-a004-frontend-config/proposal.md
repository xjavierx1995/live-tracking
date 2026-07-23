## Why

El proyecto live-tracking actualmente cuenta con un backend Laravel y un simulador de tracking, pero carece de una interfaz de usuario frontend. Se necesita implementar un cliente web que permita a los usuarios autenticarse, visualizar los servicios de tracking en un mapa interactivo y controlar el simulador de datos. Esta necesidad surge de la siguiente fase del proyecto donde se debe proporcionar una experiencia de usuario completa para monitorear y gestionar los servicios de tracking en tiempo real.

## What Changes

- **Instalar dependencias en proyecto existente**: El proyecto frontend ya existe en `frontend/`. Se deben instalar Quasar, Pinia, Axios, Leaflet, vue-leaflet y vue-router sobre el proyecto Vue 3 + Vite + TypeScript existente.
- **Sistema de autenticación**: Implementación de login y registro con manejo de tokens JWT mediante Axios interceptors.
- **Gestión de estado**: Configuración de Pinia para almacenar estado global de autenticación y servicios.
- **Integración de mapas**: Implementación de visualización de rutas y puntos de tracking usando Leaflet.
- **Comunicación con API**: Creación de servicios HTTP estructurados para consumir los endpoints del backend.
- **Router con guards**: Configuración de rutas protegidas y redirecciones basadas en estado de autenticación.
- **Persistencia de sesión**: Uso de localStorage para mantener el token de autenticación entre sesiones.

## Capabilities

### New Capabilities

- **frontend-authentication**: Sistema de autenticación de usuarios (login, registro, logout) consumiendo la API del backend Laravel.
- **frontend-dashboard**: Panel principal con visualización de servicios en mapa Leaflet y controles del simulador.
- **frontend-service-management**: Listado y visualización de servicios con sus respectivos trackings en tiempo real.
- **frontend-simulator-control**: Controles para generar servicios y gestionar la simulación (start/stop).

### Modified Capabilities

- Ninguno. Este es el primer change relacionado con el frontend, por lo que no hay capacidades existentes que modificar.

## Impact

- **Proyecto existente**: El frontend ya existe en `frontend/` con Vue 3 + Vite + TypeScript. Se extiende con las nuevas funcionalidades.
- **Nuevo código**: Módulos, componentes, páginas, stores y servicios en `frontend/src/`.
- **Dependencias añadidas**: Quasar, Pinia, Axios, Leaflet, vue-leaflet, vue-router.
- **Variables de entorno**: Configuración existente `.env` actualizada con `VITE_API_URL` apuntando al backend.
- **API dependencies**: El frontend consumirá los siguientes endpoints del backend:
  - `/api/auth/login`, `/api/auth/register`, `/api/auth/logout`, `/api/auth/me`
  - `/api/services`, `/api/services/tracking`, `/api/services/{id}`, `/api/services/{id}/tracking`
  - `/api/simulator/health`, `/api/simulator/services/generate`, `/api/simulator/simulation/start`, `/api/simulator/simulation/stop`
- **Infraestructura**: No se modifica. El frontend se conectará al backend existente via HTTP.