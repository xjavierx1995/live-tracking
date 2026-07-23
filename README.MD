## Estructura del repositorio.

  El siguiente proyecto tiene una estructura monorepo la cual costa de tres servicios (Frontend, Backend y un microservicio)

  El *frontend* esta estructurado de la siguiente manera (Vue 3, quasar, Vite, Pinia, Vue Router, Axios, leaflet):

    se utilizara la libreria `npm install @mapbox/polyline` para decodificar polylines.


  ```
  src/

    boot/ (configuracion para plugins. Ej. Axios.ts)

    components/ (componentes globales de la aplicacion) (ej. CustomModal.vue, CustomButton.vue)

    pages/ (pantallas del sistema. Ej. DashboardPage.vue, TrackingPage.vue) 

    stores/ (Pinia para gestionar el estado de la aplicacion. Ej. auth-store.ts)

    services/ (Aqui van todos los endpoints. Ej. tracking.service.ts)

    router/ (rutas accesibles de la aplicacion)

    composables/ (funciones compartidas. Ej. useTracking.ts)

    types/ (archivos de interface, types y enum)

    utils/ (metodos que realizan una funcion en especifico y pueden ser reutilizables en el proyecto)
  ```

  El *microservicio* esta estructurado de la siguiente manera (arquitectura DDD):

  ```
  Application/
    Commands/

    Handlers/

    UseCases/


  Domain/

    Entities/

    Repositories/

    ValueObjects/

    Services/

  Infrastructure/

    Persistence/

    Repositories/

    PolylineDecoder/

    GPSGenerator/

  Presentation/

    Console/

    Commands/

    Controllers/

  ```
  
  El *backend* tendra la estrucutura base que brinda laravel (Laravel 13, Sanctum, MySQL) y se encargara de la comunicacion entre el microservicio y el frontend.:

  se utilizara GoogleMapsRouteProvider para generar la polylines.

  ```
    app/

      Http/ 

      Controllers/

      Actions/

      Services/

      Repositories/

      Models/

      DTO/

      Resources/
  ```

  Docker files para levantar los tres servicios (Frontend, Backend y Microservicio) y la base de datos MySQL.

  ``` 
    nginx

    php-backend

    php-simulator

    mysql

    frontend

    phpmyadmin (opcional)

  ```

## Flujo de comunicacion entre los servicios.

  ```

    Vue
      |
    Axios
      |
    Backend API
      |
    Tracking Simulator
      |
    MySQL

  ```

## Modulos de la aplicacion.

  Módulo 1 – Simulador
  ```text

    Este módulo será el encargado de generar toda la información necesaria para alimentar el sistema de
    monitorización en tiempo real. Su finalidad será simular el comportamiento de una flota de autobuses, creando
    servicios ficticios y generando posiciones GPS que posteriormente serán consumidas por el módulo Live
    Tracking.
  ```

  Módulo 2 – Live Tracking
  ```text
    Este módulo representa el dashboard principal de monitorización y permitirá visualizar el estado de todos los servicios en tiempo real.
  ```

## Modelo de Datos

  ```text
  services: id, name, start_time, end_time, polyline.
  tracking: id, service_id, latitude, longitude, created_at.
  ```