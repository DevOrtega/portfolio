<?php

use Illuminate\Support\Facades\Route;

// Health check endpoint para Fly.io
Route::get('/up', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
});

Route::get('/', function () {
    return view('welcome');
});

// Capturar todas las rutas y servir la aplicación Vue
// Esto permite que Vue Router maneje las rutas en el lado del cliente
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
