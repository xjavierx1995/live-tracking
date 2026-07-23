## 1. Base del microservicio tracking-simulator

- [x] 1.1 Definir la estructura DDD del simulador separando Presentation, Application, Domain e Infrastructure.
- [x] 1.2 Configurar la conexion a MySQL 8 y las variables de entorno del simulador.

## 2. Persistencia de servicios y tracking

- [x] 2.1 Crear la migracion de `services` con `id`, `name`, `start_time`, `end_time` y `polyline`.
- [x] 2.2 Crear la migracion de `tracking` con `id`, `service_id`, `latitude`, `longitude` y `created_at`.
- [x] 2.3 Definir las relaciones de dominio o Eloquent entre Service y Tracking.

## 3. Generacion de rutas y servicios ficticios

- [x] 3.1 Implementar `GoogleMapsRouteProvider` como proveedor externo encapsulado.
- [x] 3.2 Implementar el caso de uso de generacion de servicios ficticios.
- [x] 3.3 Implementar el decodificador y codificador de polyline.

## 4. Simulacion y tracking en tiempo real

- [x] 4.1 Implementar el caso de uso de avance de tracking por tick.
- [x] 4.2 Implementar el estado central de simulacion activa o detenida.
- [x] 4.3 Implementar el job diferido o recursivo con intervalo de 30 segundos.

## 5. API interna del simulador

- [x] 5.1 Crear el controller interno para generar servicios.
- [x] 5.2 Crear el controller interno para iniciar simulacion.
- [x] 5.3 Crear el controller interno para detener simulacion.
- [x] 5.4 Crear el endpoint de salud del simulador.

## 6. Contrato de salida para backend

- [x] 6.1 Validar que `GET /api/services` devuelva servicios compatibles con `ServiceResource`.
- [x] 6.2 Validar que `GET /api/services/{id}/tracking` devuelva puntos compatibles con `TrackingResource`.
- [x] 6.3 Validar que `GET /api/services/tracking` devuelva servicios con `trackings` anidados.

## 7. Documentacion e infraestructura

- [x] 7.1 Documentar variables de entorno, arranque y flujo de simulacion en el README del simulador.
- [x] 7.2 Ajustar Docker Compose o la configuracion local para incluir el worker de colas necesario.