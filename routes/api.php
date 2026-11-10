<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

Route::prefix('v1')->group(function () {
    
    // Endpoint de salud (Health Check)
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'service' => 'ContactoApi',
            'timestamp' => now()->toIso8601String(),
        ], 200);
    });

    // Rutas públicas de Autenticación
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Rutas protegidas que requieren Token (Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
    });

});