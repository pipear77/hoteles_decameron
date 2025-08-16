<?php

use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\HotelRoomConfigurationController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rutas públicas para registro y autenticación.
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Grupo de rutas que requieren autenticación con Sanctum.
Route::middleware('auth:sanctum')->group(function () {
    // Rutas para los usuarios.
    Route::get('/user', function (Request $request) {
        return $request->user()->load('role');
    });
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::apiResource('users', UserController::class)->except(['store']);
    // La ruta para actualizar el rol es una acción no estándar, por lo que se mantiene aparte.
    Route::patch('/users/{user}/roles', [UserController::class, 'updateRole']);

    // Rutas para las Acomodaciones usando el método apiResource.
    Route::apiResource('accommodations', AccommodationController::class);

    // Rutas para los Tipos de Habitación usando el método apiResource.
    Route::apiResource('room_types', RoomTypeController::class);

    // Rutas para los Hoteles usando el método apiResource.
    Route::apiResource('hotels', HotelController::class);

    Route::apiResource('hotels.room-configurations', HotelRoomConfigurationController::class);
});
