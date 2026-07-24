## Purpose

Define health checks for all Docker services (mysql, backend, simulator, simulator-worker, frontend, nginx) and use `depends_on` with `condition: service_healthy` to ensure proper startup order.

## ADDED Requirements

### Requirement: Service health checks with dependency ordering
The system SHALL define health checks for all Docker services (mysql, backend, simulator, simulator-worker, frontend, nginx) and use `depends_on` with `condition: service_healthy` to ensure proper startup order.

#### Scenario: MySQL health check passes before dependent services start
- **WHEN** the mysql container starts
- **THEN** it runs `mysqladmin ping` every 10s and becomes healthy within 30s
- **AND** backend and simulator services wait for mysql to be healthy before starting

#### Scenario: Backend health check validates API availability
- **WHEN** the backend container starts
- **THEN** it runs `curl -f http://localhost:8000/api/test` every 15s
- **AND** becomes healthy when the endpoint returns 200 OK
- **AND** nginx and simulator-worker wait for backend to be healthy

#### Scenario: Simulator health check validates simulator API
- **WHEN** the simulator container starts
- **THEN** it runs `curl -f http://localhost:8001/api/simulator/health` every 15s
- **AND** becomes healthy when the endpoint returns 200 OK with simulator status
- **AND** nginx and simulator-worker wait for simulator to be healthy

#### Scenario: Frontend health check validates Vite dev server
- **WHEN** the frontend container starts
- **THEN** it runs `curl -f http://localhost:5173` every 15s
- **AND** becomes healthy when Vite dev server responds
- **AND** nginx waits for frontend to be healthy

#### Scenario: Nginx health check validates proxy
- **WHEN** the nginx container starts
- **THEN** it runs `curl -f http://localhost/health` every 15s
- **AND** becomes healthy when the health endpoint returns 200 OK