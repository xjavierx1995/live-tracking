## Why

El modulo 1 necesita un microservicio dedicado para generar servicios ficticios y producir tracking GPS continuo sin mezclar esa logica con el backend. El backend ya define el contrato de lectura y orquestacion, pero aun falta implementar el simulador que alimente esos endpoints con datos reales de simulacion.

## What Changes

- Implementar `tracking-simulator` como microservicio Laravel independiente con arquitectura DDD.
- Crear y persistir las tablas `services` y `tracking` para almacenar servicios generados y posiciones historicas.
- Implementar la generacion de servicios ficticios con horarios y polyline codificada.
- Implementar `GoogleMapsRouteProvider` como dependencia externa encapsulada dentro del simulador para obtener o construir la ruta base.
- Implementar la simulacion continua del movimiento del vehiculo con pequenas desviaciones GPS y persistencia incremental del tracking.
- Exponer endpoints internos para generar servicios, iniciar simulacion, detener simulacion y consultar salud.
- Mantener el contrato de salida alineado con el backend, especialmente `GET /api/services/tracking` con trackings anidados.
- Usar un mecanismo simple de reprogramacion cada 30 segundos mientras la simulacion siga activa.

## Capabilities

### New Capabilities
- `tracking-simulator-simulation`: dominio y ejecucion de la simulacion de servicios y tracking GPS.

### Modified Capabilities
- Ninguna. Los contratos del backend ya estan definidos y este cambio implementa el servicio que los alimenta.

## Impact

- Servicio afectado: `tracking-simulator`.
- Servicio consumidor: `backend`, que continuara siendo el unico punto de entrada para el frontend.
- API afectada: endpoints internos del simulador y respuesta agregada de servicios con trackings anidados.
- Base de datos: nuevas tablas `services` y `tracking` en el simulador.
- Infraestructura: Docker Compose debe permitir comunicacion backend-simulador y un worker de colas activo para tracking continuo.
- Documentacion: el README del simulador debe explicar variables de entorno, arranque y flujo de simulacion.