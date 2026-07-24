# Live Tracking

Sistema de monitoreo en tiempo real de una flota de autobuses. Arquitectura monorepo con tres servicios: Frontend (Vue 3), Backend (Laravel) y Tracking Simulator (microservicio Laravel).

## Arquitectura

```
Frontend (Vue 3 + Quasar)  →  Nginx Proxy (:80)  →  Backend API (Laravel)  →  Tracking Simulator (Laravel)
                                                              │
                                                         MySQL
                                                       (:3307)
```

- **Frontend** — Dashboard de monitoreo con mapa Leaflet, decodifica polylines para dibujar rutas.
- **Backend** — API REST (Laravel + Sanctum). Expone servicios y simulation endpoints al frontend.
- **Tracking Simulator** — Microservicio (Laravel, DDD). Genera servicios ficticios y posiciones GPS en tiempo real usando OpenRouteService.

## Prerrequisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (con Docker Compose v2)
- Git

## Levantar el proyecto (solo Docker)

### 1. Clonar y configurar

```bash
git clone <repo-url> live-tracking
cd live-tracking

# Copiar archivo de entorno raíz (ÚNICO paso manual)
cp .env.example .env
```

### 2. Levantar todo

```bash
docker compose up -d --build
```

Esto levanta todos los servicios automáticamente:

| Servicio | Acceso | Descripción |
|---|---|---|
| **nginx** | `http://localhost` | Proxy inverso (frontend + backend API unificado) |
| **frontend** | `http://localhost:5173` | Vue 3 + Quasar (dev server) |
| **backend** | `http://localhost/api/*` | Laravel API (tras nginx) |
| **simulator** | `http://localhost/api/simulator/*` | Tracking Simulator API (tras nginx) |
| **simulator-worker** | — | Worker de colas (queue:work) |
| **mysql** | `localhost:3307` | MySQL 8.4 |

> **Nota**: La primera vez tardará varios minutos en construir las imágenes y descargar dependencias. Ejecuciones posteriores usan caché y son mucho más rápidas.

### 3. Verificar que todo funciona

```bash
# Health checks
curl http://localhost/health              # nginx
curl http://localhost/api/test            # backend
curl http://localhost/api/simulator/health # simulator
curl http://localhost/api/services/       # services (vacío al inicio)

# Ver estado de contenedores
docker compose ps
```

### 4. Abrir el dashboard

Abrir **`http://localhost`** en el navegador.

---

## Configuración opcional

Edita `.env` si necesitas personalizar:

```bash
# Simulador - OpenRouteService (rutas reales)
ORS_API_KEY=tu_api_key_aqui              # vacío = rutas sintéticas
SIMULATION_TICK_SECONDS=30               # intervalo GPS

# Base de datos
MYSQL_ROOT_PASSWORD=root
MYSQL_DATABASE=live_tracking
```

Para obtener rutas reales, consigue una API key gratuita en [openrouteservice.org](https://openrouteservice.org/dev/#/signup).

---

## Desarrollo local (sin Docker)

Si prefieres correr sin Docker (requiere MySQL local en puerto 3306):

```bash
# Backend
cd backend
composer install
php artisan migrate
php artisan serve --port=8000

# Tracking Simulator
cd tracking-simulator
composer install
php artisan migrate
php artisan serve --port=8001

# Frontend
cd frontend
pnpm install
pnpm dev
```

---

## Estructura del monorepo

```
live-tracking/
├── .env.example              # Variables raíz (copiar a .env)
├── docker/                   # Dockerfiles y configs
│   ├── backend/
│   │   ├── Dockerfile
│   │   ├── entrypoint.sh     # Auto-setup: composer, key, migrate
│   │   ├── supervisord.conf  # php-fpm + queue worker
│   │   └── supervisord-simulator.conf
│   ├── frontend/Dockerfile
│   └── nginx/default.conf    # Proxy unificado
├── docker-compose.yml
├── frontend/                 # Vue 3 + Quasar + Vite + Pinia
├── backend/                  # Laravel + Sanctum
├── tracking-simulator/       # Microservicio DDD
└── openspec/                 # Especificaciones de cambios
```

---

## Comandos útiles

```bash
# Logs en tiempo real
docker compose logs -f backend
docker compose logs -f simulator

# Reiniciar un servicio
docker compose restart backend

# Detener todo
docker compose down

# Limpiar todo (incluye volúmenes MySQL → borra BD)
docker compose down -v

# Ejecutar comandos artisan
docker compose exec backend php artisan migrate
docker compose exec simulator php artisan migrate

# Ver workers de colas
docker compose exec backend supervisorctl status
docker compose exec simulator supervisorctl status
```

---
```