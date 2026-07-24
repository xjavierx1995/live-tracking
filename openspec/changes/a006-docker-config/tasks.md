## 1. Root Environment Configuration

- [ ] 1.1 Create root `.env.example` with all prefixed variables (BACKEND_*, SIMULATOR_*, MYSQL_*, FRONTEND_*, NGINX_*)
- [ ] 1.2 Update `backend/.env.example` to reference BACKEND_* variables from root
- [ ] 1.3 Update `tracking-simulator/.env.example` to reference SIMULATOR_* variables from root

## 2. Docker Compose Configuration

- [ ] 2.1 Update `docker-compose.yml` with all service definitions from design
- [ ] 2.2 Add healthchecks to all services (mysql, backend, simulator, simulator-worker, frontend, nginx)
- [ ] 2.3 Add `depends_on` with `condition: service_healthy` for proper startup ordering
- [ ] 2.4 Configure environment variables using `${VAR:-default}` interpolation from root .env
- [ ] 2.5 Add `expose` for simulator port 8001 (internal only)
- [ ] 2.6 Add volumes and networks configuration
- [ ] 2.7 Verify with `docker compose config` that all variables interpolate correctly

## 3. Backend Dockerfile & Entrypoint

- [ ] 3.1 Update `docker/backend/Dockerfile` with non-root user (UID/GID build args)
- [ ] 3.2 Add system dependencies (git, unzip, zip, curl, libzip-dev, libpng-dev, libonig-dev, libxml2-dev, supervisor)
- [ ] 3.3 Install PHP extensions (pdo_mysql, mbstring, zip, exif, pcntl)
- [ ] 3.4 Copy Composer from composer:2 image
- [ ] 3.5 Create `appuser` with configurable UID/GID and chown /var/www/html
- [ ] 3.6 Copy `entrypoint.sh` and `supervisord.conf` to image
- [ ] 3.7 Set ENTRYPOINT to `entrypoint.sh` and CMD to supervisord

## 4. Backend Entrypoint Script

- [ ] 4.1 Create `docker/backend/entrypoint.sh` with shebang and `set -e`
- [ ] 4.2 Copy `.env.example` to `.env` if not exists
- [ ] 4.3 Run `composer install` only if vendor missing or composer.lock changed
- [ ] 4.4 Generate `APP_KEY` if empty in .env
- [ ] 4.5 Wait for MySQL connectivity (retry loop 30x2s)
- [ ] 4.6 Run `php artisan migrate --force --no-interaction`
- [ ] 4.7 Clear and cache config/route/view
- [ ] 4.8 Make executable (`chmod +x`)

## 5. Supervisord Configuration

- [ ] 5.1 Create `docker/backend/supervisord.conf`
- [ ] 5.2 Configure `[supervisord]` with nodaemon, user=appuser, logfile, pidfile
- [ ] 5.3 Add `[program:php-fpm]` with autostart, autorestart
- [ ] 5.4 Add `[program:queue-worker]` with `php artisan queue:work --tries=1 --sleep=1`, numprocs=1, autorestart

## 6. Frontend Dockerfile

- [ ] 6.1 Update `docker/frontend/Dockerfile` with non-root user (UID/GID build args)
- [ ] 6.2 Add `apk add --no-cache git`
- [ ] 6.3 Enable corepack and prepare pnpm latest
- [ ] 6.4 Create appuser and chown /app
- [ ] 6.5 Copy `package.json` and `pnpm-lock.yaml` first (cache layer)
- [ ] 6.6 Run `pnpm install --frozen-lockfile` as appuser
- [ ] 6.7 Copy remaining frontend source
- [ ] 6.8 Set `CHOKIDAR_USEPOLLING=true` for HMR in Docker
- [ ] 6.9 Expose 5173, CMD `pnpm dev --host`

## 7. Nginx Configuration

- [ ] 7.1 Update `docker/nginx/default.conf`
- [ ] 7.2 Add `/health` endpoint returning 200 "healthy"
- [ ] 7.3 Configure `/` proxy to `frontend:5173` with WebSocket support (Upgrade headers, timeouts 300s)
- [ ] 7.4 Configure `/api/` proxy to `backend:8000/`
- [ ] 7.5 Configure `/api/simulator/` proxy to `simulator:8001/`
- [ ] 7.6 Configure `/api/services/` proxy to `simulator:8001/`
- [ ] 7.7 Set proxy headers (Host, X-Real-IP, X-Forwarded-For, X-Forwarded-Proto)
- [ ] 7.8 Set `client_max_body_size 10M` for uploads

## 8. Health Check Endpoints

- [ ] 8.1 Add `GET /api/test` route in backend (returns 200 OK)
- [ ] 8.2 Add `GET /api/simulator/health` route in simulator (returns 200 OK)
- [ ] 8.3 Verify both endpoints return proper JSON response for healthchecks

## 9. Integration Testing

- [ ] 9.1 Run `cp .env.example .env` in fresh clone
- [ ] 9.2 Run `docker compose up -d --build`
- [ ] 9.3 Verify all containers healthy: `docker compose ps`
- [ ] 9.4 Verify frontend accessible at `http://localhost`
- [ ] 9.5 Verify backend API at `http://localhost/api/test`
- [ ] 9.6 Verify simulator health at `http://localhost/api/simulator/health`
- [ ] 9.7 Verify services API at `http://localhost/api/services/`
- [ ] 9.8 Verify database migrations ran in both backend and simulator
- [ ] 9.9 Verify APP_KEY generated in both Laravel apps (check .env files)
- [ ] 9.10 Verify vendor/ and node_modules/ installed
- [ ] 9.11 Verify queue workers running (check supervisor logs)

## 10. Verification & Cleanup

- [ ] 10.1 Test full restart: `docker compose down -v && docker compose up -d --build`
- [ ] 10.2 Verify idempotency: restart containers, no duplicate migrations/keys
- [ ] 10.3 Verify permissions: files in volumes owned by appuser (UID 1000)
- [ ] 10.4 Document any required manual steps in README (should be just `cp .env.example .env`)