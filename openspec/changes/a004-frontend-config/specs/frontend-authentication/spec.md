## ADDED Requirements

### Requirement: Usuario puede iniciar sesión
El sistema SHALL permitir a los usuarios autenticarse mediante email y password. La autenticación debe ser persistida para mantener la sesión activa entre recargas de página.

#### Scenario: Login exitoso
- **WHEN** el usuario ingresa credenciales válidas (email y password) y hace clic en "Iniciar sesión"
- **THEN** el sistema envía credenciales al endpoint `/api/auth/login`, recibe token JWT, almacena el token en localStorage, actualiza el estado de autenticación, y redirige al dashboard

#### Scenario: Login con credenciales inválidas
- **WHEN** el usuario ingresa credenciales incorrectas y hace clic en "Iniciar sesión"
- **THEN** el sistema muestra mensaje de error indicando que las credenciales son inválidas

#### Scenario: Sesión persistente tras recargar página
- **WHEN** el usuario recarga la página having iniciado sesión previamente
- **THEN** el sistema verifica la existencia del token en localStorage, restaura el estado de autenticación, y mantiene al usuario en la ruta actual

### Requirement: Usuario puede registrarse
El sistema SHALL permitir a nuevos usuarios crear una cuenta mediante name, email y password.

#### Scenario: Registro exitoso
- **WHEN** el usuario completa el formulario de registro con datos válidos y hace clic en "Registrarse"
- **THEN** el sistema envía los datos al endpoint `/api/auth/register`, recibe token JWT y datos del usuario, almacena el token, y redirige al dashboard

#### Scenario: Registro con email existente
- **WHEN** el usuario intenta registrarse con un email que ya existe en el sistema
- **THEN** el sistema muestra mensaje de error indicando que el email ya está registrado

### Requirement: Usuario puede cerrar sesión
El sistema SHALL permitir a los usuarios autenticados cerrar su sesión de manera segura.

#### Scenario: Logout exitoso
- **WHEN** el usuario hace clic en el botón "Cerrar sesión"
- **THEN** el sistema envía solicitud al endpoint `/api/auth/logout`, elimina el token de localStorage, limpia el estado de autenticación, y redirige a la página de login

### Requirement: Acceso no autorizado redirige a login
El sistema SHALL proteger las rutas del dashboard y redirigir automáticamente a usuarios no autenticados.

#### Scenario: Acceso a ruta protegida sin autenticación
- **WHEN** un usuario no autenticado intenta acceder directamente a `/dashboard` o cualquier ruta que requiera autenticación
- **THEN** el sistema redirige automáticamente a `/login`

#### Scenario: Acceso a login/register cuando ya está autenticado
- **WHEN** un usuario autenticado intenta acceder a `/login` o `/register`
- **THEN** el sistema redirige automáticamente al dashboard

### Requirement: Token expirado maneja logout automático
El sistema SHALL detectar cuando el token de autenticación expira y cerrar sesión automáticamente.

#### Scenario: Token expira (respuesta 401)
- **WHEN** el sistema recibe una respuesta 401 del API
- **THEN** el sistema elimina el token de localStorage, limpia el estado de autenticación, y redirige a la página de login