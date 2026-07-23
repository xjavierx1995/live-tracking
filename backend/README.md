## Simulator Integration

### Environment

Configure the simulator base URL in backend environment:

```env
TRACKING_SIMULATOR_BASE_URL=http://simulator:8001
```

### API Endpoints Exposed by Backend

All module endpoints are protected by Sanctum and return `{ data, message, status }`:

- `POST /api/simulator/services/generate`
- `POST /api/simulator/simulation/start`
- `POST /api/simulator/simulation/stop`
- `GET /api/simulator/health`
- `GET /api/services`
- `GET /api/services/{id}`
- `GET /api/services/{id}/tracking`
- `GET /api/services/tracking`

### Local Docker Run Order

1. Start containers: `docker compose up -d --build`
2. Install backend dependencies (first run): `docker compose exec backend composer install`
3. Create backend env file (first run): `docker compose exec backend cp .env.example .env`
4. Generate app key: `docker compose exec backend php artisan key:generate`
5. Run migrations: `docker compose exec backend php artisan migrate`

Backend reaches simulator through the Docker network using service name `simulator`.
