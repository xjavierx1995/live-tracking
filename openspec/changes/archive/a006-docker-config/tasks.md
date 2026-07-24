## 1. Root Environment Configuration

- [x] 1.1 Create root `.env.example` with all prefixed variables (BACKEND_*, SIMULATOR_*, MYSQL_*, FRONTEND_*, NGINX_*)
- [x] 1.2 Update `backend/.env.example` to reference BACKEND_* variables from root
- [x] 1.3 Update `tracking-simulator/.env.example` to reference SIMULATOR_* variables from root

## 2. Docker Compose Configuration

- [x] 2.1 Update `docker-compose.yml` with all service definitions from design
- [x] 2.2 Add healthchecks to all services (mysql, backend, simulator, simulator-worker, frontend, nginx)
- [x] 2.3 Add `depends_on` with `condition: service_healthy` for proper startup ordering
- [x] 2.4 Configure environment variables using `${VAR:-default}` interpolation from root .env
- [x] 2.5 Add `expose` for simulator port 8001 (internal only)
- [x] 2.6 Add volumes and networks configuration
- [x] 2.7 Verify with `docker compose config` that all variables interpolate correctly

## 3. Backend Dockerfile & Entrypoint

- [x] 3.1 Update `docker/backend/Dockerfile` with non-root user (UID/GID build args)
- [x] 3.2 Add system dependencies (git, unzip, zip, curl, libzip-dev, libpng-dev, libonig-dev, libxml2-dev, supervisor)
- [x] 3.3 Install PHP extensions (pdo_mysql, mbstring, zip, exif, pcntl)
- [x] 3.4 Copy Composer from composer:2 image
- [x] 3.5 Create `appuser` with configurable UID/GID and chown /var/www/html
- [x] 3.6 Copy `entrypoint.sh` and `supervisord.conf` to image
- [x] 3.7 Set ENTRYPOINT to `entrypoint.sh` and CMD to supervisord

## 4. Backend Entrypoint Script

- [x] 4.1 Create `docker/backend/entrypoint.sh` with shebang and `set -e`
- [x] 4.2 Copy `.env.example` to `.env` if not exists
- [x] 4.3 Run `composer install` only if vendor missing or composer.lock changed
- [x] 4.4 Generate `APP_KEY` if empty in .env
- [x] 4.5 Wait for MySQL connectivity (retry loop 30x2s)
- [x] 4.6 Run `php artisan migrate --force --no-interaction`
- [x] 4.7 Clear and cache config/route/view
- [x] 4.8 Make executable (`chmod +x`)

## 5. Supervisord Configuration

- [x] 5.1 Create `docker/backend/supervisord.conf`
- [x] 5.2 Configure `[supervisord]` with nodaemon, user=appuser, logfile, pidfile
- [x] 5.3 Add `[program:php-fpm]` with autostart, autorestart
- [x] 5.4 Add `[program:queue-worker]` with `php artisan queue:work --tries=1 --sleep=1`, numprocs=1, autorestart

## 6. Frontend Dockerfile

- [x] 6.1 Update `docker/frontend/Dockerfile` with non-root user (UID/GID build args)
- [x] 6.2 Add `apk add --no-cache git` (and curl for healthcheck)
- [x] 6.3 Enable corepack and prepare pnpm latest
- [x] 6.4 Create appuser and chown /app
- [x] 6.5 Copy `package.json` and `pnpm-lock.yaml` first (cache layer)
- [x] 6.6 Run `pnpm install --frozen-lockfile` as appuser
- [x] 6.7 Copy remaining frontend source
- [x] 6.8 Set `CHOKIDAR_USEPOLLING=true` for HMR in Docker
- [x] 6.9 Expose 5173, CMD `pnpm dev --host`

## 7. Nginx Configuration

- [x] 7.1 Update `docker/nginx/default.conf`
- [x] 7.2 Add `/health` endpoint returning 200 "healthy"
- [x] 7.3 Configure `/` proxy to `frontend:5173` with WebSocket support (Upgrade headers, timeouts 300s)
- [x] 7.4 Configure `/api/` proxy to `backend:8000/`
- [x] 7.5 Configure `/api/simulator/` proxy to `simulator:8001/`
- [x] 7.6 Configure `/api/services/` proxy to `simulator:8001/`
- [x] 7.7 Set proxy headers (Host, X-Real-IP, X-Forwarded-For, X-Forwarded-Proto)
- [x] 7.8 Set `client_max_body_size 10M` for uploads

## 8. Health Check Endpoints

- [x] 8.1 Add `GET /api/test` route in backend (returns 200 OK)
- [x] 8.2 Add `GET /api/simulator/health` route in simulator (returns 200 OK)
- [x] 8.3 Verify both endpoints return proper JSON response for healthchecks

## 9. Integration Testing

- [x] 9.1 Run `cp .env.example .env` in fresh clone
- [x] 9.2 Run `docker compose up -d --build`
- [x] 9.3 Verify all containers healthy: `docker compose ps`
- [x] 9.4 Verify frontend accessible at `http://localhost`
- [x] 9.5 Verify backend API at `http://localhost/api/test`
- [x] 9.6 Verify simulator health at `http://localhost/api/simulator/health`
- [x] 9.7 Verify services API at `http://localhost/api/services/`
- [x] 9.8 Verify database migrations ran in both backend and simulator
- [x] 9.9 Verify APP_KEY generated in both Laravel apps (check .env files)
- [x] 9.10 Verify vendor/ and node_modules/ installed
- [x] 9.11 Verify queue workers running (check supervisor logs)

## 10. Verification & Cleanup

- [x] 10.1 Test full restart: `docker compose down -v && docker compose up -d --build`
- [x] 10.2 Verify idempotency: restart containers, no duplicate migrations/keys
- [x] 10.3 Verify permissions: files in volumes owned by appuser (UID 1000)
- [x] 10.4 Document any required manual steps in README (should be just `cp .env.example .env`)