## 1. Base del microservicio tracking-simulator

- [ ] 1.1 Definir la estructura DDD del simulador separando Presentation, Application, Domain e Infrastructure.
- [ ] 1.2 Configurar la conexion a MySQL 8 y las variables de entorno del simulador.

## 2. Persistencia de servicios y tracking

- [ ] 2.1 Crear la migracion de `services` con `id`, `name`, `start_time`, `end_time` y `polyline`.
- [ ] 2.2 Crear la migracion de `tracking` con `id`, `service_id`, `latitude`, `longitude` y `created_at`.
- [ ] 2.3 Definir las relaciones de dominio o Eloquent entre Service y Tracking.

## 3. Generacion de rutas y servicios ficticios

- [ ] 3.1 Implementar `GoogleMapsRouteProvider` como proveedor externo encapsulado.
- [ ] 3.2 Implementar el caso de uso de generacion de servicios ficticios.
- [ ] 3.3 Implementar el decodificador y codificador de polyline.

## 4. Simulacion y tracking en tiempo real

- [ ] 4.1 Implementar el caso de uso de avance de tracking por tick.
- [ ] 4.2 Implementar el estado central de simulacion activa o detenida.
- [ ] 4.3 Implementar el job diferido o recursivo con intervalo de 30 segundos.

## 5. API interna del simulador

- [ ] 5.1 Crear el controller interno para generar servicios.
- [ ] 5.2 Crear el controller interno para iniciar simulacion.
- [ ] 5.3 Crear el controller interno para detener simulacion.
- [ ] 5.4 Crear el endpoint de salud del simulador.

## 6. Contrato de salida para backend

- [ ] 6.1 Validar que `GET /api/services` devuelva servicios compatibles con `ServiceResource`.
- [ ] 6.2 Validar que `GET /api/services/{id}/tracking` devuelva puntos compatibles con `TrackingResource`.
- [ ] 6.3 Validar que `GET /api/services/tracking` devuelva servicios con `trackings` anidados.

## 7. Documentacion e infraestructura

- [ ] 7.1 Documentar variables de entorno, arranque y flujo de simulacion en el README del simulador.
- [ ] 7.2 Ajustar Docker Compose o la configuracion local para incluir el worker de colas necesario.