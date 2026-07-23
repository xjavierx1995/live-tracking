## 1. Configuración Inicial del Proyecto (en proyecto existente `frontend/`)

- [ ] 1.1 Instalar dependencias: quasar, @quasar/extras, quasar-vite-plugin, pinia, axios, leaflet, @vue-leaflet/vue-leaflet, vue-router
- [ ] 1.2 Configurar vite.config.ts con Quasar plugin
- [ ] 1.3 Configurar variables de entorno (.env con VITE_API_URL)
- [ ] 1.4 Configurar Quasar plugins en src/main.ts (router, pinia)
- [ ] 1.5 Crear estructura de directorios Feature-Based

## 2. Tipos e Interfaces TypeScript

- [ ] 2.1 Crear archivo frontend/src/types/index.ts con interfaces del dominio
- [ ] 2.2 Definir interfaces: User, Service, TrackingPoint, ServiceWithTrackings
- [ ] 2.3 Definir interfaces: AuthResponse, ApiResponse, SimulatorHealth

## 3. Servicios HTTP

- [ ] 3.1 Crear frontend/src/services/api.ts con instancia Axios base
- [ ] 3.2 Configurar interceptor de request para Authorization header
- [ ] 3.3 Configurar interceptor de response para manejo de errores 401
- [ ] 3.4 Crear frontend/src/services/authService.ts (login, register, me, logout)
- [ ] 3.5 Crear frontend/src/services/serviceService.ts (list, listWithTracking, getById)
- [ ] 3.6 Crear frontend/src/services/simulatorService.ts (health, generateServices, start, stop)

## 4. Stores de Pinia

- [ ] 4.1 Crear frontend/src/stores/authStore.ts con estado de usuario y token
- [ ] 4.2 Implementar método login en authStore
- [ ] 4.3 Implementar método register en authStore
- [ ] 4.4 Implementar método logout en authStore
- [ ] 4.5 Implementar método fetchUser en authStore
- [ ] 4.6 Persistir token en localStorage

## 5. Router y Guards

- [ ] 5.1 Configurar frontend/src/router/index.ts con rutas
- [ ] 5.2 Crear rutas: /login, /register, /dashboard
- [ ] 5.3 Definir meta: { requiresAuth: true } y { guest: true }
- [ ] 5.4 Implementar beforeEach guard para rutas protegidas
- [ ] 5.5 Implementar redirect de /login/register cuando ya autenticado

## 6. Páginas de Autenticación

- [ ] 6.1 Crear frontend/src/layouts/AuthLayout.vue
- [ ] 6.2 Crear frontend/src/pages/LoginPage.vue con formulario
- [ ] 6.3 Crear frontend/src/pages/RegisterPage.vue con formulario
- [ ] 6.4 Implementar validación de formulario
- [ ] 6.5 Conectar con authStore.login() y authStore.register()
- [ ] 6.6 Agregar link de navegación entre login y register
- [ ] 6.7 Mostrar mensajes de error en login/register

## 7. Layout Principal y Dashboard

- [ ] 7.1 Crear frontend/src/layouts/MainLayout.vue con header y sidebar
- [ ] 7.2 Crear frontend/src/pages/DashboardPage.vue
- [ ] 7.3 Implementar header con nombre de usuario y botón logout
- [ ] 7.4 Configurar sidebar de 300px
- [ ] 7.5 Hacer layout responsive para desktop/tablet/móvil

## 8. Integración de Mapas Leaflet

- [ ] 8.1 Configurar Leaflet en el proyecto (importar CSS)
- [ ] 8.2 Crear frontend/src/components/map/MapView.vue
- [ ] 8.3 Implementar visualización de polyline de ruta
- [ ] 8.4 Implementar markers para tracking points
- [ ] 8.5 Crear componente ServiceMarker.vue
- [ ] 8.6 Centrar mapa en ruta seleccionada

## 9. Gestión de Servicios

- [ ] 9.1 Crear composable frontend/src/composables/useServices.ts
- [ ] 9.2 Crear frontend/src/components/services/ServiceList.vue
- [ ] 9.3 Crear frontend/src/components/services/ServiceCard.vue
- [ ] 9.4 Implementar selección de servicio
- [ ] 9.5 Mostrar trackings del servicio seleccionado en el mapa
- [ ] 9.6 Implementar polling o refresh manual de servicios

## 10. Controles del Simulador

- [ ] 10.1 Crear composable frontend/src/composables/useSimulator.ts
- [ ] 10.2 Crear componente de controles (generar, start, stop)
- [ ] 10.3 Implementar verificación de health del simulador
- [ ] 10.4 Implementar generación de servicios
- [ ] 10.5 Implementar inicio y detención de simulación
- [ ] 10.6 Mostrar estado visual del simulador (activo/inactivo)
- [ ] 10.7 Habilitar/deshabilitar botones según estado

## 11. Verificación y Testing Manual

- [ ] 11.1 Probar flujo de registro de usuario
- [ ] 11.2 Probar flujo de login
- [ ] 11.3 Probar acceso a rutas protegidas sin autenticación
- [ ] 11.4 Probar logout
- [ ] 11.5 Probar carga de servicios en el dashboard
- [ ] 11.6 Probar selección de servicio y visualización en mapa
- [ ] 11.7 Probar generación de servicios simulados
- [ ] 11.8 Probar inicio y parada de simulación
- [ ] 11.9 Verificar manejo de errores (401, 500, etc.)
- [ ] 11.10 Verificar responsive design