# Capability: backend-api-authentication

## Purpose

Define stable, stateless backend API authentication contracts for register, login, current user retrieval, and logout using Sanctum bearer tokens.

## Requirements

### Requirement: API registration issues a bearer token
The backend SHALL expose a `POST /api/auth/register` endpoint that creates a valid user account and returns a Sanctum bearer token for immediate API use.

#### Scenario: Successful registration
- **WHEN** a client submits a valid name, unique email, password, and matching password confirmation to `POST /api/auth/register`
- **THEN** the backend creates the user, issues a new Sanctum personal access token, and responds with HTTP `201` and a payload containing `data.user` and `data.token`

#### Scenario: Registration validation failure
- **WHEN** a client submits invalid registration data such as a duplicated email, malformed email, missing name, or unconfirmed password
- **THEN** the backend responds with HTTP `422` and validation errors without creating a user or issuing a token

### Requirement: API login returns a new bearer token
The backend SHALL expose a `POST /api/auth/login` endpoint that authenticates a user by email and password and returns a new Sanctum bearer token.

#### Scenario: Successful login
- **WHEN** a client submits valid credentials to `POST /api/auth/login`
- **THEN** the backend responds with HTTP `200` and a payload containing `data.user` and `data.token`

#### Scenario: Invalid credentials
- **WHEN** a client submits an unknown email or incorrect password to `POST /api/auth/login`
- **THEN** the backend responds with HTTP `401` and does not issue a token

### Requirement: Authenticated clients can retrieve the current user
The backend SHALL expose a `GET /api/auth/me` endpoint protected by `auth:sanctum` that returns the authenticated user through a controlled response resource.

#### Scenario: Authenticated request to me endpoint
- **WHEN** a client sends a valid bearer token to `GET /api/auth/me`
- **THEN** the backend responds with HTTP `200` and a `data` payload containing the authenticated user's public fields required by the contract

#### Scenario: Unauthenticated request to me endpoint
- **WHEN** a client calls `GET /api/auth/me` without a valid bearer token
- **THEN** the backend responds with HTTP `401`

### Requirement: Logout revokes only the current token
The backend SHALL expose a `POST /api/auth/logout` endpoint protected by `auth:sanctum` that revokes only the current access token used in the request.

#### Scenario: Successful logout
- **WHEN** an authenticated client calls `POST /api/auth/logout` with a valid bearer token
- **THEN** the backend deletes the current access token and responds with HTTP `200` and a confirmation message

#### Scenario: Reusing a revoked token
- **WHEN** a client attempts to call a protected endpoint with a token revoked by `POST /api/auth/logout`
- **THEN** the backend responds with HTTP `401`

### Requirement: Authentication responses use stable API contracts
The backend MUST return authentication responses using stable JSON structures that do not expose unintended internal model attributes.

#### Scenario: Successful auth response shape
- **WHEN** a client completes registration or login successfully
- **THEN** the backend returns `data.user` with only contract-approved user fields and `data.token` as the issued bearer token

#### Scenario: Successful me response shape
- **WHEN** a client retrieves the authenticated user from `GET /api/auth/me`
- **THEN** the backend returns the user under `data` without exposing hidden or unrelated internal attributes
