## ADDED Requirements

### Requirement: Centralized environment configuration
The system SHALL provide a single root `.env.example` file containing all environment variables for the entire Docker stack, using prefixed variable names (BACKEND_*, SIMULATOR_*, MYSQL_*, FRONTEND_*, NGINX_*, COMPOSE_PROJECT_NAME, APP_ENV).

#### Scenario: Root .env.example exists with all required variables
- **WHEN** a developer runs `cp .env.example .env` in a fresh clone
- **THEN** the `.env` file contains all variables needed for docker-compose to start all services

#### Scenario: Docker compose interpolates variables correctly
- **WHEN** docker compose reads the `.env` file
- **THEN** all service environment variables are populated from the root variables with correct prefixes