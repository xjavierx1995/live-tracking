## ADDED Requirements

### Requirement: Automated frontend dependency installation via Dockerfile
The system SHALL provide a frontend Dockerfile that automatically installs npm dependencies and runs the Vite dev server without manual intervention.

#### Scenario: Install pnpm globally
- **WHEN** the Docker image builds
- **THEN** it runs `npm install -g pnpm`

#### Scenario: Create non-root user with UID/GID 1000
- **WHEN** the Docker image builds
- **THEN** it uses the existing `node` user (UID 1000) in node:alpine
- **AND** creates `/app` directory owned by `node:node`

#### Scenario: Copy package files first for cache layer
- **WHEN** the Docker image builds
- **THEN** it copies `package.json` and `pnpm-lock.yaml` before the rest of the source
- **AND** runs `pnpm install --frozen-lockfile --ignore-scripts` as the node user
- **AND** this layer is cached unless lock file changes

#### Scenario: Copy remaining source and start Vite dev server
- **WHEN** dependencies are installed
- **THEN** it copies the remaining frontend source code
- **AND** runs `pnpm dev --host` as the node user
- **AND** exposes port 5173

#### Scenario: Install curl for health checks
- **WHEN** the Docker image builds
- **THEN** it installs `curl` via `apk add --no-cache curl`
- **AND** the health check `curl -f http://localhost:5173` works