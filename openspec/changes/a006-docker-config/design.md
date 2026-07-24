## Context

La configuración actual de Docker tiene los siguientes problemas:
- **6+ pasos manuales** post `docker compose up`: copiar `.env`, `composer install`, `npm install`, `key:generate`, `migrate`
- **No existe `.env.example` a nivel raíz** - variables dispersas en cada servicio
- **Frontend falla** al no tener `node_modules` instalados
- **Nginx no expone la API del simulator** - frontend no puede acceder a `/api/simulator/*`
- **Sin healthchecks** - contenedores inician en orden arbitrario sin garantizar que dependencias estén listas
- **Permisos de archivos** en volúmenes bind-mount causan problemas con usuario root

Arquitectura actual: Monorepo con 3 servicios (Frontend Vue 3, Backend Laravel, Simulator Laravel DDD) + MySQL + Nginx en Docker Compose.

## Goals / Non-Goals

**Goals:**
- `docker compose up -d --build` → aplicación 100% funcional sin intervención manual
- Archivo `.env.example` raíz único como fuente de verdad
- Healthchecks en todos los servicios con `depends_on: condition: service_healthy`
- Entrypoints idempotentes que manejen instalación, migraciones, permisos
- Nginx como proxy unificado exponiendo backend (`/api/*`) y simulator (`/api/simulator/*`)
- Usuario no-root (UID/GID configurable) para evitar problemas de permisos
- Supervisord en backend/simulator para gestionar php-fpm + queue workers

**Non-Goals:**
- Cambios en lógica de negocio de backend/simulator/frontend
- Migración a Kubernetes o producción
- Cambios en APIs públicas (solo infraestructura)
- Configuración de CI/CD

## Decisions

### 1. Root `.env.example` como única fuente de verdad
**Decisión**: Un solo archivo `.env.example` en la raíz con todas las variables prefijadas (`BACKEND_*`, `SIMULATOR_*`, `MYSQL_*`, `FRONTEND_*`, `NGINX_*`). Docker Compose interpola y pasa a cada contenedor.
**Rationale**: Elimina duplicación, evita drift entre archivos, simplifica onboarding (un solo `cp .env.example .env`).
**Alternativa considerada**: Mantener `.env.example` por servicio - descartada por duplicación y mantenimiento.

### 2. Supervisord en backend/simulator vs contenedores separados para queue workers
**Decisión**: Un contenedor por servicio (backend, simulator) con supervisord gestionando php-fpm + queue worker.
**Rationale**: Simplifica docker-compose (menos contenedores), comparte código/ficheros, reduce overhead. Queue worker es ligero.
**Alternativa considerada**: Contenedor separado `backend-worker` y `simulator-worker` - descartada por complejidad adicional.

### 3. Entrypoint scripts idempotentes
**Decisión**: Scripts bash que verifican existencia antes de actuar (`if [ ! -f .env ]`, `if [ ! -d vendor ]`, `if [ -z "$APP_KEY" ]`).
**Rationale**: Re-ejecutables sin efectos secundarios, seguros en reinicios, compatibles con bind-mounts.
**Alternativa considerada**: `composer install` en Dockerfile - descartada porque bind-mount sobreescribe vendor.

### 4. Healthchecks con `depends_on: condition: service_healthy`
**Decisión**: Healthchecks en mysql (mysqladmin ping), backend (curl `/api/test`), simulator (curl `/api/simulator/health`), nginx (curl `/health`).
**Rationale**: Garantiza orden de arranque correcto, evita race conditions migraciones vs DB.
**Alternativa considerada**: `depends_on` sin condition - descartada por fallos intermitentes.

### 5. Nginx proxy unificado con rutas específicas para simulator
**Decisión**: 
- `/` → frontend (Vite dev server)
- `/api/` → backend Laravel
- `/api/simulator/` → simulator Laravel
- `/api/services/` → simulator Laravel (read model)
**Rationale**: Frontend consume solo `/api/*`; backend actúa como gateway y reenvía a simulator internamente. Nginx expone simulator directamente para healthchecks y debugging.
**Alternativa considerada**: Backend proxee todo a simulator - descartada por acoplamiento y latencia.

### 6. Usuario no-root con UID/GID build args
**Decisión**: `ARG UID=1000 GID=1000`, crear usuario `appuser`, `chown` directorios trabajo.
**Rationale**: Seguridad (no root), evita problemas permisos en bind-mounts (host UID 1000 coincide con container).
**Alternativa considerada**: Usuario root - descartada por seguridad y permisos.

### 7. Frontend multi-stage build (Node build → Nginx serve)
**Decisión**: Stage 1: `node:24-alpine` para `pnpm install` + `pnpm build`; Stage 2: `nginx:alpine` sirviendo `dist/`.
**Rationale**: Imagen final ligera (solo nginx + assets estáticos), mejor rendimiento producción, sin dependencias Node en runtime.
**Alternativa considerada**: `pnpm dev` en contenedor Node - descartada para producción (solo dev).

## Risks / Trade-offs

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|--------------|---------|------------|
| Entrypoint falla en migraciones si DB no lista | Media | Alto | Healthcheck MySQL + `depends_on: condition: service_healthy` + retry loop (30 intentos x 2s) en entrypoint |
| Permisos archivos (volumen montado) | Alta | Medio | Usuario `appuser` con UID/GID configurable via build args; `chown` en Dockerfile y entrypoint |
| Cache Docker invalida `pnpm install` | Baja | Medio | Copiar `package.json` + `pnpm-lock.yaml` antes del COPY general; `--frozen-lockfile` |
| Supervisord no reinicia queue worker al morir | Baja | Alto | `autorestart=true` en supervisord.conf; logs en `/var/log/supervisor/` |
| Variables entorno no se interpolan | Baja | Alto | Usar `${VAR:-default}` en docker-compose; verificar con `docker compose config` |
| Nginx no resuelve nombres servicios | Baja | Medio | Todos en misma red `live-network`; Docker DNS resuelve nombres de servicio |
| Frontend HMR no funciona en Docker | Media | Bajo | `CHOKIDAR_USEPOLLING=true` en frontend; `proxy_read_timeout 300s` en nginx |

## Migration Plan

1. **Crear archivos nuevos**: `.env.example` (root), `docker/backend/entrypoint.sh`, `docker/frontend/entrypoint.sh`, `docker/backend/supervisord.conf`
2. **Actualizar archivos existentes**: `docker-compose.yml`, `docker/backend/Dockerfile`, `docker/frontend/Dockerfile`, `docker/nginx/nginx.conf`, `backend/.env.example`, `tracking-simulator/.env.example`
3. **Añadir healthcheck endpoints**: Backend `GET /api/test`, Simulator `GET /api/simulator/health`
4. **Test local**: `cp .env.example .env && docker compose up -d --build`
5. **Verificar**: Frontend en `:80`, Backend API en `/api/test`, Simulator health en `/api/simulator/health`, DB migrada, APP_KEY generada, queue workers corriendo
6. **Rollback**: `docker compose down -v` elimina volúmenes; restaurar archivos anteriores desde git

## Open Questions

- ¿Simulador necesita `ORS_API_KEY` obligatorio o opcional? (Actual: opcional con default vacío)
- ¿Frontend en producción usa `nginx` sirviendo `dist/` o `Vite dev server`? (Actual: dev server para desarrollo local)
- ¿Queue workers necesitan `supervisorctl` access para debugging? (Actual: logs en `/var/log/supervisor/`)