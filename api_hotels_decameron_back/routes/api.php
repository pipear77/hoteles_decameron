<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\HotelRoomConfigurationController;
use Illuminate\Support\Facades\Route;

// Rutas públicas. Solo el login es necesario aquí para obtener el token.
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Grupo de rutas que requieren autenticación con Sanctum.
Route::middleware('auth:sanctum')->group(function () {
    // La ruta de logout se protege para asegurar que solo un usuario autenticado pueda cerrar sesión.
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Rutas para los Hoteles usando el método apiResource para seguir el enfoque RESTful.
    Route::apiResource('hotels', HotelController::class);

    // Rutas para las configuraciones de habitaciones, anidadas bajo el recurso hotels
    // para reflejar la relación uno a muchos.
    Route::apiResource('hotels.room-configurations', HotelRoomConfigurationController::class);
});
