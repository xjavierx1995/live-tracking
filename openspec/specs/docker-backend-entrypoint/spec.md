## Purpose

Provide an entrypoint script (`docker/backend/entrypoint.sh`) that automatically handles Laravel application setup on container startup without manual intervention.

## ADDED Requirements

### Requirement: Automated Laravel setup via entrypoint script
The system SHALL provide an entrypoint script (`docker/backend/entrypoint.sh`) that automatically handles Laravel application setup on container startup without manual intervention.

#### Scenario: Copy .env.example to .env if missing
- **WHEN** the container starts and `.env` does not exist
- **THEN** the script copies `.env.example` to `.env`

#### Scenario: Install Composer dependencies if vendor missing or lock file changed
- **WHEN** the container starts and `vendor` directory doesn't exist OR `composer.lock` is newer than `vendor/composer/installed.json`
- **THEN** the script runs `composer install --no-interaction --prefer-dist --optimize-autoloader`
- **AND** skips installation if dependencies are already up to date

#### Scenario: Generate APP_KEY if empty
- **WHEN** the container starts and `APP_KEY` in `.env` is empty
- **THEN** the script runs `php artisan key:generate --force`

#### Scenario: Wait for MySQL connectivity with retry
- **WHEN** the container starts
- **THEN** the script retries MySQL connection up to 30 times with 2s intervals
- **AND** fails if MySQL doesn't become available within 60 seconds

#### Scenario: Run database migrations automatically
- **WHEN** MySQL is available
- **THEN** the script runs `php artisan migrate --force --no-interaction`
- **AND** migrations are idempotent (safe to re-run)

#### Scenario: Optimize Laravel configuration caches
- **WHEN** migrations complete
- **THEN** the script runs `config:clear`, `route:clear`, `view:clear`, `config:cache`, `route:cache`, `view:cache`

#### Scenario: Entrypoint is executable and uses exec for supervisord
- **WHEN** the Dockerfile builds
- **THEN** `entrypoint.sh` has execute permissions (`chmod +x`)
- **AND** the script ends with `exec "$@"` to replace itself with supervisord