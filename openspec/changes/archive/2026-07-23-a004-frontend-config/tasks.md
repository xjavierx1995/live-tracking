## 1. Configuración Inicial del Proyecto (en proyecto existente `frontend/`)

- [x] 1.1 Instalar dependencias: quasar, @quasar/extras, quasar-vite-plugin, pinia, axios, leaflet, @vue-leaflet/vue-leaflet, vue-router
- [x] 1.2 Configurar vite.config.ts con Quasar plugin
- [x] 1.3 Configurar variables de entorno (.env con VITE_API_URL)
- [x] 1.4 Configurar Quasar plugins en src/main.ts (router, pinia)
- [x] 1.5 Crear estructura de directorios Feature-Based

## 2. Tipos e Interfaces TypeScript

- [x] 2.1 Crear archivo frontend/src/types/index.ts con interfaces del dominio
- [x] 2.2 Definir interfaces: User, Service, TrackingPoint, ServiceWithTrackings
- [x] 2.3 Definir interfaces: AuthResponse, ApiResponse, SimulatorHealth

## 3. Servicios HTTP

- [x] 3.1 Crear frontend/src/services/api.ts con instancia Axios base
- [x] 3.2 Configurar interceptor de request para Authorization header
- [x] 3.3 Configurar interceptor de response para manejo de errores 401
- [x] 3.4 Crear frontend/src/services/authService.ts (login, register, me, logout)
- [x] 3.5 Crear frontend/src/services/serviceService.ts (list, listWithTracking, getById)
- [x] 3.6 Crear frontend/src/services/simulatorService.ts (health, generateServices, start, stop)

## 4. Stores de Pinia

- [x] 4.1 Crear frontend/src/stores/authStore.ts con estado de usuario y token
- [x] 4.2 Implementar método login en authStore
- [x] 4.3 Implementar método register en authStore
- [x] 4.4 Implementar método logout en authStore
- [x] 4.5 Implementar método fetchUser en authStore
- [x] 4.6 Persistir token en localStorage

## 5. Router y Guards

- [x] 5.1 Configurar frontend/src/router/index.ts con rutas
- [x] 5.2 Crear rutas: /login, /register, /dashboard
- [x] 5.3 Definir meta: { requiresAuth: true } y { guest: true }
- [x] 5.4 Implementar beforeEach guard para rutas protegidas
- [x] 5.5 Implementar redirect de /login/register cuando ya autenticado

## 6. Páginas de Autenticación

- [x] 6.1 Crear frontend/src/layouts/AuthLayout.vue
- [x] 6.2 Crear frontend/src/pages/LoginPage.vue con formulario
- [x] 6.3 Crear frontend/src/pages/RegisterPage.vue con formulario
- [x] 6.4 Implementar validación de formulario
- [x] 6.5 Conectar con authStore.login() y authStore.register()
- [x] 6.6 Agregar link de navegación entre login y register
- [x] 6.7 Mostrar mensajes de error en login/register

## 7. Layout Principal y Dashboard

- [x] 7.1 Crear frontend/src/layouts/MainLayout.vue con header y sidebar
- [x] 7.2 Crear frontend/src/pages/DashboardPage.vue
- [x] 7.3 Implementar header con nombre de usuario y botón logout
- [x] 7.4 Configurar sidebar de 300px
- [x] 7.5 Hacer layout responsive para desktop/tablet/móvil

## 8. Integración de Mapas Leaflet

- [x] 8.1 Configurar Leaflet en el proyecto (importar CSS)
- [x] 8.2 Crear frontend/src/components/map/MapView.vue
- [x] 8.3 Implementar visualización de polyline de ruta
- [x] 8.4 Implementar markers para tracking points
- [x] 8.5 Crear componente ServiceMarker.vue
- [x] 8.6 Centrar mapa en ruta seleccionada

## 9. Gestión de Servicios

- [x] 9.1 Crear composable frontend/src/composables/useServices.ts
- [x] 9.2 Crear frontend/src/components/services/ServiceList.vue
- [x] 9.3 Crear frontend/src/components/services/ServiceCard.vue
- [x] 9.4 Implementar selección de servicio
- [x] 9.5 Mostrar trackings del servicio seleccionado en el mapa
- [x] 9.6 Implementar polling o refresh manual de servicios

## 10. Controles del Simulador

- [x] 10.1 Crear composable frontend/src/composables/useSimulator.ts
- [x] 10.2 Crear componente de controles (generar, start, stop)
- [x] 10.3 Implementar verificación de health del simulador
- [x] 10.4 Implementar generación de servicios
- [x] 10.5 Implementar inicio y detención de simulación
- [x] 10.6 Mostrar estado visual del simulador (activo/inactivo)
- [x] 10.7 Habilitar/deshabilitar botones según estado

## 11. Verificación y Testing Manual

- [x] 11.1 Probar flujo de registro de usuario
- [x] 11.2 Probar flujo de login
- [x] 11.3 Probar acceso a rutas protegidas sin autenticación
- [x] 11.4 Probar logout
- [x] 11.5 Probar carga de servicios en el dashboard
- [x] 11.6 Probar selección de servicio y visualización en mapa
- [x] 11.7 Probar generación de servicios simulados
- [x] 11.8 Probar inicio y parada de simulación
- [x] 11.9 Verificar manejo de errores (401, 500, etc.)
- [x] 11.10 Verificar responsive design