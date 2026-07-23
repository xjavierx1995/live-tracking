<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
        'directions_url' => env('GOOGLE_MAPS_DIRECTIONS_URL', 'https://maps.googleapis.com/maps/api/directions/json'),
    ],

    'simulator' => [
        'tick_seconds' => (int) env('SIMULATION_TICK_SECONDS', 30),
        'route_center_lat' => (float) env('SIMULATOR_ROUTE_CENTER_LAT', 4.7110),
        'route_center_lng' => (float) env('SIMULATOR_ROUTE_CENTER_LNG', -74.0721),
        'route_radius_meters' => (int) env('SIMULATOR_ROUTE_RADIUS_METERS', 5000),
    ],

];
