## Why

El backend ya cuenta con la base técnica mínima de Laravel Sanctum, pero todavía no expone un flujo de autenticación API consumible por el frontend. Sin endpoints formales de registro, login, usuario autenticado y logout, el backend no puede actuar como punto único de entrada ni ofrecer una autenticación stateless estable basada en bearer tokens.

## What Changes

- Incorporar un módulo de autenticación API en el backend con endpoints públicos para registro e inicio de sesión.
- Exponer endpoints protegidos para consultar el usuario autenticado y cerrar sesión revocando únicamente el token actual.
- Formalizar validación de entradas con FormRequest y respuestas JSON estables mediante Resources o payloads consistentes.
- Añadir pruebas feature del flujo auth para cubrir registro, login, acceso autenticado, logout y errores esperados.

## Capabilities

### New Capabilities
- `backend-api-authentication`: Autenticación API stateless en el backend usando Laravel Sanctum bearer tokens para register, login, me y logout.

### Modified Capabilities
- Ninguna.

## Impact

- Servicio afectado: backend Laravel.
- API afectada: nuevos endpoints bajo `/api/auth` y uso formal de `auth:sanctum` para rutas privadas.
- Código afectado: controllers API, actions, form requests, resources y pruebas feature del backend.
- Dependencias y contratos: reutiliza Sanctum ya instalado, la tabla `users` y `personal_access_tokens`, sin impacto en frontend ni tracking-simulator más allá del contrato API que el frontend consumirá.