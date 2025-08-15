<?php

use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\HotelRoomConfigurationController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rutas públicas que no requieren autenticación
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Rutas protegidas por el middleware de autenticación de Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Rutas accesibles para todos los usuarios autenticados, ya sea 'admin' o 'user'.
    Route::middleware('ability:user:view,admin:all')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user()->load('role');
        });
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Rutas de visualización (GET) para los recursos
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::get('/accommodations', [AccommodationController::class, 'index']);
        Route::get('/accommodations/{accommodation}', [AccommodationController::class, 'show']);
        Route::get('/room_types', [RoomTypeController::class, 'index']);
        Route::get('/room_types/{room_type}', [RoomTypeController::class, 'show']);
        Route::get('/hotels', [HotelController::class, 'index']);
        Route::get('/hotels/{hotel}', [HotelController::class, 'show']);
        Route::get('hotels/{hotelId}/configurations', [HotelRoomConfigurationController::class, 'index']);
        Route::get('hotels/{hotelId}/configurations/{hotelRoomConfigurationId}', [HotelRoomConfigurationController::class, 'show']);
    });

    // Rutas de administración (solo para 'admin')
    Route::middleware('ability:admin:all')->group(function () {
        // Rutas de gestión de usuarios
        Route::patch('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        Route::patch('/users/{user}/roles', [UserController::class, 'updateRole']);

        // Rutas de recursos para Acomodaciones
        Route::post('/accommodations', [AccommodationController::class, 'store']);
        Route::put('/accommodations/{accommodation}', [AccommodationController::class, 'update']);
        Route::delete('/accommodations/{accommodation}', [AccommodationController::class, 'destroy']);

        // Rutas de recursos para Tipos de Habitación
        Route::post('/room_types', [RoomTypeController::class, 'store']);
        Route::put('/room_types/{room_type}', [RoomTypeController::class, 'update']);
        Route::delete('/room_types/{room_type}', [RoomTypeController::class, 'destroy']);

        // Rutas de recursos para Hoteles
        Route::post('/hotels', [HotelController::class, 'store']);
        Route::put('/hotels/{hotel}', [HotelController::class, 'update']);
        Route::delete('/hotels/{hotel}', [HotelController::class, 'destroy']);

        // Rutas anidadas para la configuración de habitaciones
        Route::post('hotels/{hotelId}/configurations', [HotelRoomConfigurationController::class, 'store']);
        Route::put('hotels/{hotelId}/configurations/{hotelRoomConfigurationId}', [HotelRoomConfigurationController::class, 'update']);
        Route::delete('hotels/{hotelId}/configurations/{hotelRoomConfigurationId}', [HotelRoomConfigurationController::class, 'destroy']);
    });
});
