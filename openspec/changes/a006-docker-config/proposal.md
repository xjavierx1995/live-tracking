## Why

La configuración actual de Docker requiere 6+ pasos manuales post-`docker compose up` (copiar `.env`, `composer install`, `npm install`, `key:generate`, `migrate`). No existe archivo `.env.example` a nivel raíz, el frontend falla al no tener `node_modules`, y nginx no expone la API del simulator. El objetivo es lograr `docker compose up -d --build` → aplicación funcionando al 100% sin intervención manual.

## What Changes

- **Nuevo**: Archivo `.env.example` a nivel raíz con todas las variables de entorno necesarias
- **Modificado**: `docker-compose.yml` - healthchecks, `depends_on` con `condition: service_healthy`, variables de entorno desde `.env`, puertos expuestos, volúmenes
- **Modificado**: `docker/backend/Dockerfile` - instalar dependencias, generar key, migrar, permisos en entrypoint
- **Modificado**: `docker/frontend/Dockerfile` - instalar dependencias npm, build Vite, servir con nginx
- **Modificado**: `docker/nginx/nginx.conf` - proxy para backend API y simulator API
- **Nuevo**: `docker/backend/entrypoint.sh` - entrypoint para backend/simulator (composer install, key:generate, migrate, permissions)
- **Nuevo**: `docker/frontend/entrypoint.sh` - entrypoint para frontend (npm install, build)
- **Modificado**: `backend/.env.example` y `tracking-simulator/.env.example` - variables sincronizadas con root `.env.example`
- **Nuevo**: Healthcheck endpoints en backend (`/api/test`) y simulator (`/api/simulator/health`)
- **Modificado**: Nginx expone simulator API en `/api/simulator/*` para que frontend no necesite conocer puertos internos

## Capabilities

### New Capabilities
- `docker-root-env`: Archivo `.env.example` raíz con todas las variables de entorno centralizadas
- `docker-compose-healthchecks`: Healthchecks en mysql, backend, simulator, simulator-worker con `depends_on: condition: service_healthy`
- `docker-backend-entrypoint`: Entrypoint script que automatiza composer install, key:generate, migrate, permisos
- `docker-frontend-entrypoint`: Entrypoint script que automatiza npm install y build Vite
- `docker-nginx-unified-proxy`: Nginx config que expone backend API en `/api/*` y simulator API en `/api/simulator/*`
- `docker-healthcheck-endpoints`: Endpoints de healthcheck en backend (`/api/test`) y simulator (`/api/simulator/health`)

### Modified Capabilities
- `docker-compose`: Configuración completa actualizada con healthchecks, variables de entorno, puertos, volúmenes
- `docker-backend-dockerfile`: Dockerfile actualizado con entrypoint, permisos, dependencias
- `docker-frontend-dockerfile`: Dockerfile actualizado con multi-stage build, nginx, entrypoint
- `backend-env-config`: Variables de entorno sincronizadas con root `.env.example`
- `simulator-env-config`: Variables de entorno sincronizadas con root `.env.example`

## Impact

**Servicios afectados**: Todos (mysql, backend, simulator, simulator-worker, frontend, nginx)
**Archivos modificados**: `docker-compose.yml`, `docker/backend/Dockerfile`, `docker/frontend/Dockerfile`, `docker/nginx/nginx.conf`, `backend/.env.example`, `tracking-simulator/.env.example`
**Archivos nuevos**: `.env.example` (root), `docker/backend/entrypoint.sh`, `docker/frontend/entrypoint.sh`, `docker/backend/Dockerfile` (entrypoint), healthcheck endpoints en backend/simulator
**APIs expuestas**: Nginx expondrá `/api/*` (backend) y `/api/simulator/*` (simulator) en puerto 80
**Breaking changes**: Ninguno - cambio solo en infraestructura/Docker, no en APIs públicas