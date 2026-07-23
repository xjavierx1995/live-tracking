# Live Tracking

Sistema de monitoreo en tiempo real de una flota de autobuses. Arquitectura monorepo con tres servicios: Frontend (Vue 3), Backend (Laravel) y Tracking Simulator (microservicio Laravel).

## Arquitectura

```
Frontend (Vue 3 + Quasar)  →  Backend API (Laravel)  →  Tracking Simulator (Laravel)
        :5173                      :8000                      :8001
                                                              │
                                                           MySQL
                                                           :3307
```

- **Frontend** — Dashboard de monitoreo con mapa Leaflet, decodifica polylines para dibujar rutas.
- **Backend** — API REST (Laravel + Sanctum). Expone servicios y simulation endpoints al frontend.
- **Tracking Simulator** — Microservicio (Laravel, DDD). Genera servicios ficticios y posiciones GPS en tiempo real usando OpenRouteService.

## Prerrequisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (con Docker Compose v2)
- Git

## Levantar el proyecto

### 1. Clonar e instalar dependencias del frontend (una sola vez)

```bash
git clone <repo-url> live-tracking
cd live-tracking

# Instalar dependencias del frontend
cd frontend
pnpm install
cd ..
```

### 2. Copiar archivos de entorno

```bash
# Backend
cp backend/.env.example backend/.env

# Tracking Simulator
cp tracking-simulator/.env.example tracking-simulator/.env
```

### 3. Levantar todo con Docker

```bash
docker compose up -d --build
```

Esto levanta:

| Servicio | Puerto | Descripción |
|---|---|---|
| nginx | `http://localhost:80` | Proxy inverso (frontend + backend API) |
| frontend | `:5173` | Vue 3 + Quasar (dev server) |
| backend | `:8000` | Laravel API |
| simulator | `:8001` | Tracking Simulator API |
| simulator-worker | — | Worker de colas (queue:work) |
| mysql | `:3307` | MySQL 8.4 |

### 4. Configurar base de datos

```bash
# Esperar a que MySQL esté listo (~10s después del boot)
docker compose exec backend php artisan migrate --force
docker compose exec simulator php artisan migrate --force
```

### 5. Generar APP_KEY (una sola vez)

```bash
docker compose exec backend php artisan key:generate
docker compose exec simulator php artisan key:generate
```

### 6. Verificar que todo funciona

```bash
# Health check del simulator
curl http://localhost:8001/api/simulator/health

# Test del backend
curl http://localhost/api/test

# Frontend
# Abrir http://localhost en el navegador
```

## Uso

### Registrar usuario

```bash
curl -X POST http://localhost/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Admin","email":"admin@test.com","password":"password"}'
```

### Login

```bash
curl -X POST http://localhost/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"password"}'
```

El token devuelto se usa en los headers: `Authorization: Bearer <token>`

### Generar servicios

```bash
curl -X POST http://localhost/api/simulator/services/generate \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"count": 10}'
```

### Iniciar/detener simulación

```bash
# Iniciar
curl -X POST http://localhost/api/simulator/simulation/start \
  -H "Authorization: Bearer <token>"

# Detener
curl -X POST http://localhost/api/simulator/simulation/stop \
  -H "Authorization: Bearer <token>"
```

### Ver servicios y tracking

```bash
# Listar servicios
curl http://localhost/api/services -H "Authorization: Bearer <token>"

# Tracking de un servicio
curl http://localhost/api/services/1/tracking -H "Authorization: Bearer <token>"
```

## Variables de entorno importantes

### Tracking Simulator (`tracking-simulator/.env`)

| Variable | Descripción | Default |
|---|---|---|
| `ORS_API_KEY` | API key de OpenRouteService | (vacío → fallback sintético) |
| `ORS_BASE_URL` | URL base de ORS | `https://api.openrouteservice.org` |
| `SIMULATION_TICK_SECONDS` | Intervalo de generación GPS | `30` |
| `SIMULATOR_ROUTE_CENTER_LAT` | Centro del área de simulación | `4.7110` |
| `SIMULATOR_ROUTE_CENTER_LNG` | Centro del área de simulación | `-74.0721` |
| `SIMULATOR_ROUTE_RADIUS_METERS` | Radio del área de simulación | `5000` |

### Backend (`backend/.env`)

| Variable | Descripción | Default |
|---|---|---|
| `TRACKING_SIMULATOR_BASE_URL` | URL interna del simulator | `http://simulator:8001` |

## Generación de rutas

El Tracking Simulator usa **OpenRouteService** (basado en OpenStreetMap) para generar rutas reales:

- Con `ORS_API_KEY` configurada: consulta la API de ORS y retorna polylines reales.
- Sin `ORS_API_KEY`: usa un fallback sintético que genera una ruta interpolada con curvatura artificial.

Para obtener rutas reales, obtener una API key gratuita en [openrouteservice.org](https://openrouteservice.org/dev/#/signup).

## Desarrollo local (sin Docker)

Si prefieres correr sin Docker:

```bash
# Requiere MySQL local en puerto 3306
# Ajustar DB_HOST, DB_PORT en ambos .env

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

## Estructura del monorepo

```
live-tracking/
├── docker/                    # Dockerfiles y configs
│   ├── backend/Dockerfile
│   ├── frontend/Dockerfile
│   └── nginx/default.conf
├── docker-compose.yml
├── frontend/                  # Vue 3 + Quasar + Vite + Pinia
│   └── src/
│       ├── boot/              # Plugins (Axios)
│       ├── components/        # Componentes globales
│       ├── pages/             # Dashboard, Tracking
│       ├── stores/            # Pinia stores
│       ├── services/          # API endpoints
│       ├── router/            # Rutas
│       ├── composables/       # Compartido (useTracking)
│       ├── types/             # Interfaces, types, enums
│       └── utils/             # Utilidades reutilizables
├── backend/                   # Laravel + Sanctum
│   └── app/
│       ├── Http/Controllers/  # API controllers
│       ├── Services/          # TrackingSimulatorClient
│       ├── Models/            # Eloquent models
│       └── ...
├── tracking-simulator/        # Microservicio (DDD)
│   └── app/
│       ├── Application/       # Use cases, contracts
│       ├── Domain/            # Entities, value objects
│       ├── Infrastructure/    # Route providers, polyline codec
│       └── Presentation/      # HTTP controllers, API routes
└── openspec/                  # Especificaciones de cambios
```

## Modelo de datos

```
services: id, name, start_time, end_time, polyline
tracking: id, service_id, latitude, longitude, created_at
```

## Comandos útiles

```bash
# Logs
docker compose logs -f backend
docker compose logs -f simulator

# Reiniciar un servicio
docker compose restart backend

# Detener todo
docker compose down

# Limpiar datos (volumen MySQL)
docker compose down -v
```
