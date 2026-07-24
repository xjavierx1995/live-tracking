## ADDED Requirements

### Requirement: Nginx as unified reverse proxy for all services
The system SHALL configure nginx to serve as the single entry point (port 80) for all services, routing requests to the appropriate backend.

#### Scenario: Health check endpoint
- **WHEN** a request is made to `GET /health`
- **THEN** nginx returns `200 OK` with body "healthy" and Content-Type text/plain
- **AND** no access log is written

#### Scenario: Frontend served at root path
- **WHEN** a request is made to `GET /` or any non-API path
- **THEN** nginx proxies to `http://frontend:5173`
- **AND** WebSocket upgrade headers are passed for Vite HMR
- **AND** proxy timeouts are set to 300s for long-lived HMR connections

#### Scenario: Backend API proxied to /api/
- **WHEN** a request is made to `GET /api/...`
- **THEN** nginx proxies to `http://backend:8000` (full path preserved)
- **AND** proxy headers (Host, X-Real-IP, X-Forwarded-For, X-Forwarded-Proto) are set
- **AND** client_max_body_size is set to 10M for file uploads

#### Scenario: Simulator API proxied to /api/simulator/
- **WHEN** a request is made to `GET /api/simulator/...`
- **THEN** nginx proxies to `http://simulator:8001` (full path preserved)
- **AND** proxy headers are set

#### Scenario: Simulator read model (services) proxied to /api/services/
- **WHEN** a request is made to `GET /api/services/...`
- **THEN** nginx proxies to `http://simulator:8001` (full path preserved)
- **AND** proxy headers are set
- **AND** this allows frontend to access simulator read model through unified API