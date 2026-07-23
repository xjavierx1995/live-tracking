## Context

El backend ya actua como punto unico de entrada para el frontend y define un read model estable para servicios y tracking, incluido el agregado `GET /api/services/tracking` con trackings anidados. El simulador debe producir exactamente ese shape, pero sin exponer su dominio interno ni acoplarse al frontend.

El microservicio `tracking-simulator` sera responsable de crear servicios ficticios, construir o recuperar la ruta base, generar una polyline codificada, decodificarla durante la ejecucion y emitir puntos GPS con pequenas desviaciones para simular movimiento realista.

## Goals / Non-Goals

**Goals:**
- Separar la simulacion de tracking del backend.
- Persistir servicios y tracking historico en el simulador.
- Mantener compatibilidad con el contrato de lectura ya definido en backend.
- Encapsular la integracion con Google Maps dentro del simulador.
- Mantener tracking continuo mientras la simulacion este activa.

**Non-Goals:**
- Implementar frontend.
- Cambiar el read model del backend.
- Exponer el simulador al frontend.
- Resolver analitica avanzada, telemetria historica o multiprocesamiento de flotas en esta iteracion.

## Decisions

### 1. El simulador sera el dueño del dominio de simulacion
La generacion de servicios, la polyline, el ruido GPS y la persistencia del tracking viviran en `tracking-simulator`. El backend solo orquesta y consume resultados.

Alternativas consideradas:
- Hacer que el backend genere rutas y el simulador solo persista: descartado porque mezcla responsabilidades y duplica logica de dominio.
- Mover el tracking a una cola controlada por backend: descartado porque el dominio de tracking debe pertenecer al simulador.

### 2. GoogleMapsRouteProvider residira en infraestructura del simulador
La integracion con Google Maps debe encapsularse detras de un provider de infraestructura. El dominio no debe conocer la API externa.

Alternativas consideradas:
- Poner Google Maps en backend: descartado porque desplaza logica de rutas fuera del microservicio propietario del dominio.
- Generar solo rutas sinteticas sin provider: viable a corto plazo, pero no cumple con la intencion de usar una fuente de rutas consistente si el negocio requiere recorridos realistas.

### 3. El tracking continuo se resolvera con reprogramacion simple cada 30 segundos
Se usara un job recursivo con delay de 30 segundos que verifique el estado de simulacion en cada tick y se auto-reprograme mientras siga activa.

Alternativas consideradas:
- Loop infinito dentro de una request: descartado por fragilidad operativa y mal encaje con HTTP.
- Cron complejo o scheduler centralizado: descartado por ser mas costoso que la necesidad actual.

### 4. La persistencia usara tablas `services` y `tracking` con historico aditivo
Cada punto GPS se insertara como un nuevo registro. No se sobrescribira el historico, porque el backend necesita visualizar el recorrido acumulado.

Alternativas consideradas:
- Guardar solo el ultimo punto por servicio: descartado porque rompe el objetivo de historico completo.
- Guardar puntos en JSON dentro de `services`: descartado porque dificulta consultas, agregados y crecimiento futuro.

### 5. El contrato de salida seguira el shape que el backend ya espera
El simulador devolvera `id`, `name`, `start_time`, `end_time`, `polyline` y `trackings` anidados para el agregado global, y puntos simples para la consulta por servicio.

Alternativas consideradas:
- Devolver entidades internas del simulador: descartado por exponer detalles de infraestructura y romper el read model del backend.

## Risks / Trade-offs

- [Desalineacion de contrato] → Mitigacion: usar el read model del backend como referencia y validar el shape de salida desde el simulador.
- [Reprogramacion recursiva y worker inactivo] → Mitigacion: documentar la necesidad de worker de colas en Docker Compose y health checks del simulador.
- [Proveedor externo inestable] → Mitigacion: encapsular errores de Google Maps y responder con errores controlados.
- [Carrera entre start/stop] → Mitigacion: usar un estado centralizado de simulacion y validar antes de cada tick.

## Migration Plan

1. Crear las migraciones de `services` y `tracking`.
2. Implementar el dominio y la infraestructura basica del simulador.
3. Agregar `GoogleMapsRouteProvider` y el generador de polyline.
4. Implementar los endpoints internos y la simulacion continua.
5. Validar que el backend pueda consumir el agregado `GET /api/services/tracking` sin transformaciones adicionales.

Rollback:
- Revertir la migracion de la rama de OpenSpec si la implementacion no llega a aplicarse.
- Si la implementacion ya fue desplegada, desactivar el worker y revertir las migraciones del simulador.

## Open Questions

- Si la simulacion deberia usar una base de datos compartida con el backend o una instancia/esquema propio dentro de la misma red Docker.
  Si, se utilizara una base de datos compartida para simplificar la infraestructura.
- Si `services` necesita un campo de estado desde la primera iteracion para coordinar mejor start/stop.
  no start activara el job para que genere tracking, stop lo desactivara y health solo consultara el estado.
- Si la generacion de servicios debe ser deterministica para pruebas o pseudoaleatoria para simular escenarios reales.
  sera pseudoaleatoria, para realismo, sin embargo se debe tener en cuenta que este es un proyecto para una prueba tecnica.