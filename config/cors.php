<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', '_ignition/*', 'health', 'up'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter([
        // ── Production ───────────────────────────────────────────
        'https://ggnfngo.vercel.app',
        env('FRONTEND_URL'),

        // ── Local development ────────────────────────────────────
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        'http://localhost:3001',
        'http://127.0.0.1:3001',
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'http://localhost:8000',
        'http://127.0.0.1:8000',
        'http://localhost:8080',
        'http://127.0.0.1:8080',
        'http://localhost',
        'http://127.0.0.1',
        'http://[::1]',
    ]),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 86400,

    // Pure stateless Bearer token auth — no cookies needed.
    // Setting true here can block cross-origin requests that don't send credentials.
    'supports_credentials' => false,

];
