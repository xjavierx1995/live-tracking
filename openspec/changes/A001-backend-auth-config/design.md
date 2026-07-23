## Context

El servicio backend ya dispone de Laravel Sanctum, del trait `HasApiTokens` en el modelo `User`, de la tabla `personal_access_tokens` y de una ruta protegida con `auth:sanctum`. Sin embargo, todavía no existe un módulo de autenticación API formal ni contratos REST estables para que el frontend se autentique contra el backend como único punto de entrada.

Este cambio afecta únicamente al servicio backend. El frontend seguirá siendo consumidor exclusivo de la API del backend y el tracking-simulator no participará en la autenticación de usuarios finales. La solución debe ser stateless y basada en bearer tokens para evitar dependencia de cookies, CSRF y configuración stateful entre dominios.

## Goals / Non-Goals

**Goals:**
- Exponer endpoints REST bajo `/api/auth` para `register`, `login`, `me` y `logout`.
- Emitir tokens personales de Sanctum reutilizables por el frontend mediante el header `Authorization: Bearer <token>`.
- Mantener controllers delgados, validación en `FormRequest` y lógica de negocio en `Actions`.
- Devolver payloads JSON estables para facilitar la integración del frontend.
- Cubrir el flujo con pruebas feature de autenticación API.

**Non-Goals:**
- Implementar autenticación stateful por cookies o flujo SPA con `/sanctum/csrf-cookie`.
- Incorporar roles, permisos, recuperación de contraseña, email verification o social login.
- Modificar frontend, tracking-simulator o contratos ajenos a auth.

## Decisions

### 1. Usar Sanctum en modo bearer token API-only
Se emitirá un token personal nuevo en `register` y `login` usando `createToken()`. Esta opción encaja con el backend como API Gateway, evita coupling con cookies y reduce complejidad operativa en Docker, Nginx y Vite.

Alternativas consideradas:
- Sanctum stateful SPA con cookies: descartado porque introduce CSRF, dominios stateful y una complejidad innecesaria para este primer flujo API.
- JWT externo: descartado porque Sanctum ya está instalado y cubre el caso de uso con menor costo de integración.

### 2. Mantener separación por capas en backend
La implementación seguirá una estructura con `AuthController`, `RegisterRequest`, `LoginRequest`, `RegisterUserAction`, `LoginUserAction`, `LogoutUserAction` y `AuthUserResource`. El controller solo orquesta request, action y resource; la lógica de creación de usuarios, autenticación y revocación de tokens queda encapsulada en acciones.

Alternativas consideradas:
- Resolver todo en controller: descartado porque rompe la convención de controllers delgados y dificulta pruebas unitarias.
- Crear servicios genéricos más amplios desde el inicio: descartado en esta iteración por aumentar la abstracción sin necesidad demostrada.

### 3. Definir contratos REST explícitos y estables
Los endpoints públicos serán `POST /api/auth/register` y `POST /api/auth/login`. Los protegidos serán `GET /api/auth/me` y `POST /api/auth/logout`, todos bajo `routes/api.php`, usando `auth:sanctum` para los privados.

Contratos propuestos:
- `POST /api/auth/register`: recibe `name`, `email`, `password`, `password_confirmation`; responde `201` con `{ data: { user, token } }`.
- `POST /api/auth/login`: recibe `email`, `password`; responde `200` con `{ data: { user, token } }`.
- `GET /api/auth/me`: responde `200` con `{ data: user }`.
- `POST /api/auth/logout`: responde `200` con `{ message: "Logout successful" }`.

Para errores de credenciales inválidas se devolverá `401`, mientras que errores de validación quedarán en `422` mediante `FormRequest`.

### 4. Revocar únicamente el token actual en logout
`LogoutUserAction` eliminará solo el token asociado a la petición autenticada mediante `currentAccessToken()->delete()`. Esto mantiene sesiones independientes por dispositivo o cliente y evita cerrar sesiones ajenas por accidente.

Alternativas consideradas:
- Revocar todos los tokens del usuario: descartado para no romper sesiones paralelas ni sorprender al frontend en flujos multi-dispositivo.

### 5. Añadir pruebas feature como contrato ejecutable
Se crearán pruebas que cubran registro exitoso, login exitoso, rechazo de credenciales inválidas, acceso a `me` autenticado, rechazo de `me` sin token y revocación del token en logout. Estas pruebas fijan el contrato REST y reducen regresiones al evolucionar auth.

## Risks / Trade-offs

- [Ambigüedad entre modo stateful y bearer token] → Mantener el alcance explícitamente en API-only y documentar que `statefulApi()` no define el flujo principal de autenticación.
- [Exposición accidental de atributos del usuario] → Responder mediante `AuthUserResource` en lugar de serializar el modelo crudo.
- [Contratos de error inconsistentes] → Apoyarse en `FormRequest`, códigos HTTP definidos y pruebas feature sobre respuestas clave.
- [Abuso del endpoint de login] → Dejar documentado rate limiting como siguiente mejora inmediata si no se implementa en esta iteración.
- [Sesiones múltiples por usuario] → Aceptar como trade-off deseado para permitir un token por cliente y revocación granular.

## Migration Plan

1. Añadir los nuevos endpoints bajo `/api/auth` sin retirar de inmediato `/api/user` ni la ruta `/api/test`.
2. Implementar controller, actions, requests, resource y pruebas en el backend.
3. Validar el flujo completo con pruebas feature y consumo mediante bearer token.
4. Migrar luego al frontend hacia los endpoints formales de auth.

Rollback:
- El cambio es aditivo; si falla, basta con retirar las rutas `/api/auth` y las clases nuevas sin requerir rollback de datos, ya que Sanctum y sus tablas ya existen.

## Open Questions

- Si el registro público debe quedar habilitado en la primera entrega o desactivarse más adelante por decisión de producto.
- Si el rate limiting de `login` debe incluirse en esta implementación o en la siguiente iteración inmediata.