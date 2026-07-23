## Context

El proyecto live-tracking cuenta con un backend Laravel que expone una API REST para autenticación, gestión de servicios y control del simulador de tracking. También existe un Tracking Simulator que genera posiciones GPS y permite gestionar simulaciones. 

**El proyecto frontend ya existe** en `frontend/` con Vue 3 + Vite + TypeScript vanilla. Este change extiende el proyecto existente instalando las dependencias necesarias (Quasar, Pinia, Axios, Leaflet) y creando los módulos, componentes y funcionalidades requeridas.

El frontend debe ser una aplicación Quasar (Vue 3 + TypeScript) que:
- Permita a los usuarios autenticarse en el sistema.
- Visualice los servicios de tracking en un mapa interactivo.
- Muestre los puntos de tracking en tiempo real.
- Proporcione controles para gestionar el simulador de datos.

## Goals / Non-Goals

**Goals:**
- Implementar una aplicación frontend completa con Vue 3 + Quasar + TypeScript.
- Crear sistema de autenticación (login, registro, logout) con manejo de tokens JWT.
- Desarrollar dashboard con mapa Leaflet para visualización de rutas y tracking.
- Implementar listado de servicios con sus trackings asociados.
- Agregar controles para generar servicios y gestionar la simulación.
- Configurar routing con guards para rutas protegidas.

**Non-Goals:**
- Modificar el backend o el Tracking Simulator existentes.
- Implementar funcionalidades de administración de usuarios.
- Integrar con servicios externos de mapas (Google Maps) más allá de Leaflet.
- Implementar testing unitario o de integración en esta fase.
- Desplegar a producción (solo desarrollo local).

## Decisions

### Decisión 1: Quasar como framework UI

**Alternativas consideradas:** Vue Native, Vuetify, Ionic
**Decisión:** Quasar (Vue 3 + TypeScript)

**Rationale:** El contexto del proyecto indica explícitamente el uso de Quasar. Proporciona componentes material design listos para usar, router integrado, y excelente integración con Vite. Además, permite crear aplicaciones responsive con un solo codebase.

### Decisión 2: Pinia para gestión de estado

**Alternativas consideradas:** Vuex, Provide/Inject
**Decisión:** Pinia

**Rationale:** Es la solución oficial recomendada para Vue 3. API intuitiva, tipado completo con TypeScript, y modular. El contexto del proyecto indica su uso.

### Decisión 3: Axios con interceptors para HTTP

**Alternativas consideradas:** Fetch API, Vue Resource
**Decisión:** Axios con interceptors

**Rationale:** Axios proporciona una API más declarativa y manejo de interceptors para automáticamente añadir el token de autenticación a las peticiones y manejar errores 401. Es el estándar en el contexto del proyecto.

### Decisión 4: Leaflet para mapas

**Alternativas consideradas:** Google Maps JS API, Mapbox GL JS
**Decisión:** Leaflet + @vue-leaflet/vue-leaflet

**Rationale:** Open source, gratuito, sin necesidad de API key, y suficiente para los requerimientos de visualización de polylines y markers. El contexto del proyecto indica su uso.

### Decisión 5: Estructura de directorios Feature-Based

**Alternativas considerada:** Estructura por tipo de archivo
**Decisión:** Feature-Based con módulos

**Rationale:** El contexto del proyecto especifica arquitectura Feature Based. Facilita la escalabilidad y el mantenimiento al agrupar funcionalidad relacionada.

### Decisión 6: Persistencia de token en localStorage

**Alternativas consideradas:** Cookies HTTP-only, Session Storage
**Decisión:** localStorage

**Rationale:** Simplifica la implementación inicial. El backend utiliza Sanctum que maneja cookies. Sin embargo, para una SPA, localStorage es el approach estándar cuando se usa bearer tokens. Se implementa interceptor para automáticamente añadir el token a las peticiones.

## Risks / Trade-offs

- **[Riesgo]** Seguridad de tokens en localStorage → **Mitigación:** Implementar HttpOnly cookies sería más seguro, pero requiere cambios en backend. Para desarrollo local, localStorage es aceptable.

- **[Riesgo]** Dependencia del backend para todas las funcionalidades → **Mitigación:** El frontend solo consume la API del backend, siguiendo la arquitectura especificada.

- **[Riesgo]** Latencia en actualizaciones de tracking → **Mitigación:** Implementar polling o WebSocket en fases futuras. Por ahora, actualización manual o interval refresh.

- **[Trade-off]** Simplicidad vs Features: Se implementa funcionalidad core primero, dejando testing y optimizaciones para iteraciones posteriores.

## Migration Plan

1. **Fase 1 - Instalación de dependencias**: Instalar Quasar, Pinia, Axios, Leaflet, vue-leaflet, vue-router en el proyecto existente `frontend/`.
2. **Fase 2 - Configuración base**: Configurar Quasar plugins, variables de entorno, Axios, Router con guards.
3. **Fase 3 - Tipos**: Crear interfaces TypeScript para todas las respuestas del API en `frontend/src/types/`.
4. **Fase 4 - Autenticación**: Implementar pages login/register, stores auth, guards de router.
5. **Fase 5 - Dashboard**: Crear layout principal, integrar Leaflet, listar servicios.
6. **Fase 6 - Simulator**: Agregar controles para generar servicios y gestionar simulación.
7. **Verificación**: Testing manual de todos los flujos.

**Rollback:** Desinstalar las dependencias añadidas y eliminar los archivos creados en `frontend/src/`. No hay cambios en backend que requieran rollback.

## Open Questions

- ¿Se requiere implementar WebSocket para actualización en tiempo real de los tracking points, o es suficiente con polling?
- ¿Se necesita algún mecanismo de caché para los datos de servicios?
- ¿Se permitirá el acceso público a ciertas rutas del dashboard o todo será protegido?