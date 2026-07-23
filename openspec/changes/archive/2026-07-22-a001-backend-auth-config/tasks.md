## 1. Estructura del módulo auth

- [x] 1.1 Crear `AuthController` en la capa API del backend y definir los métodos `register`, `login`, `me` y `logout`
- [x] 1.2 Crear `RegisterRequest` y `LoginRequest` con reglas de validación y códigos de error esperados para auth API
- [x] 1.3 Crear `AuthUserResource` y acordar el payload JSON final para respuestas de autenticación

## 2. Lógica de negocio y rutas

- [x] 2.1 Implementar `RegisterUserAction` para crear usuarios válidos y emitir tokens Sanctum
- [x] 2.2 Implementar `LoginUserAction` para validar credenciales y emitir un token nuevo por sesión
- [x] 2.3 Implementar `LogoutUserAction` para revocar únicamente el token actual
- [x] 2.4 Registrar las rutas `POST /api/auth/register`, `POST /api/auth/login`, `GET /api/auth/me` y `POST /api/auth/logout` en `routes/api.php` usando `auth:sanctum` en las privadas

## 3. Contrato y validación del flujo

- [x] 3.1 Asegurar respuestas HTTP consistentes para éxito, validación fallida y credenciales inválidas según el contrato definido
- [x] 3.2 Mantener o retirar de forma controlada la ruta temporal `/api/user` según compatibilidad durante la transición

## 4. Pruebas feature

- [x] 4.1 Crear pruebas feature para registro exitoso y validaciones de registro
- [x] 4.2 Crear pruebas feature para login exitoso y rechazo de credenciales inválidas
- [x] 4.3 Crear pruebas feature para `GET /api/auth/me` con y sin token válido
- [x] 4.4 Crear pruebas feature para `POST /api/auth/logout` verificando revocación del token actual y rechazo de reutilización