<?php

namespace App\Providers;

use App\Repositories\AccommodationRepository;
use App\Repositories\AccommodationRepositoryInterface;
use App\Repositories\HotelRepository;
use App\Repositories\HotelRepositoryInterface;
use App\Repositories\HotelRoomConfigurationRepository;
use App\Repositories\HotelRoomConfigurationRepositoryInterface;
use App\Repositories\RoomTypeRepository;
use App\Repositories\RoomTypeRepositoryInterface;
use App\Services\HotelRoomConfigurationService;
use App\Services\HotelRoomConfigurationServiceInterface;
use App\Services\HotelService;
use App\Services\HotelServiceInterface;
use App\Services\UserService;
use App\Services\UserServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Vinculación de interfaces a implementaciones concretas
        $this->app->bind(
            HotelServiceInterface::class,
            HotelService::class
        );

        $this->app->bind(
            HotelRepositoryInterface::class,
            HotelRepository::class
        );

        $this->app->bind(
            AccommodationRepositoryInterface::class,
            AccommodationRepository::class
        );

        $this->app->bind(
            RoomTypeRepositoryInterface::class,
            RoomTypeRepository::class
        );

        // CORRECCIÓN: Se añade la vinculación que faltaba para el repositorio de configuraciones de habitaciones.
        // Esto resuelve el error de dependencia en la cadena de inyección.
        $this->app->bind(
            HotelRoomConfigurationRepositoryInterface::class,
            HotelRoomConfigurationRepository::class
        );

        $this->app->bind(
            HotelRoomConfigurationServiceInterface::class,
            HotelRoomConfigurationService::class
        );

        $this->app->bind(UserServiceInterface::class, UserService::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
