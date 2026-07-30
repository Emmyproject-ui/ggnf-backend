<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'service'     => 'GGNF Laravel API',
        'status'      => 'running',
        'version'     => '1.0.0',
        'environment' => config('app.env'),
        'docs'        => 'API is accessible at /api/*',
        'health'      => url('/health'),
    ]);
});

Route::get('/health', function () {
    $dbStatus = 'disconnected';
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $dbStatus = 'connected';
    } catch (\Exception $e) {
        $dbStatus = 'error: ' . $e->getMessage();
    }

    return response()->json([
        'status' => 'ok',
        'service' => 'GGNF Backend',
        'timestamp' => now()->toIso8601String(),
        'database' => $dbStatus,
    ], 200);
});

