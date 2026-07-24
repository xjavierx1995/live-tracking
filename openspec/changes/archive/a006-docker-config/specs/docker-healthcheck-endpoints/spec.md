## ADDED Requirements

### Requirement: Backend health check endpoint
The system SHALL expose a health check endpoint at `GET /api/test` in the backend that returns 200 OK for Docker health checks.

#### Scenario: Backend health check returns success
- **WHEN** a request is made to `GET /api/test`
- **THEN** the endpoint returns HTTP 200 with JSON: `{"message": "Test route works!"}`
- **AND** the response is fast (no database queries required)

### Requirement: Simulator health check endpoint
The system SHALL expose a health check endpoint at `GET /api/simulator/health` in the simulator that returns simulator status for Docker health checks.

#### Scenario: Simulator health check returns status
- **WHEN** a request is made to `GET /api/simulator/health`
- **THEN** the endpoint returns HTTP 200 with JSON containing:
  - `simulator`: "up" (always)
  - `simulation_active`: boolean indicating if simulation is running
- **AND** the response is wrapped in standard API format: `{"data": {...}, "message": "...", "status": true}`

#### Scenario: Health check endpoints work through nginx proxy
- **WHEN** Docker health check runs `curl -f http://localhost:8000/api/test` (backend) or `curl -f http://localhost:8001/api/simulator/health` (simulator)
- **THEN** the request succeeds with exit code 0
- **AND** the container is marked healthy