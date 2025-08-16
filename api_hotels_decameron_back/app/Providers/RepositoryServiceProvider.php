<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\AccommodationRepository;
use App\Repositories\AccommodationRepositoryInterface;
use App\Repositories\HotelRepository;
use App\Repositories\HotelRepositoryInterface;
use App\Repositories\HotelRoomConfigurationRepository;
use App\Repositories\HotelRoomConfigurationRepositoryInterface;
use App\Repositories\RoleRepository;
use App\Repositories\RoleRepositoryInterface;
use App\Repositories\RoomTypeRepository;
use App\Repositories\RoomTypeRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use App\Services\AccommodationService;
use App\Services\AccommodationServiceInterface;
use App\Services\HotelRoomConfigurationService;
use App\Services\HotelRoomConfigurationServiceInterface;
use App\Services\HotelService;
use App\Services\HotelServiceInterface;
use App\Services\RoleService;
use App\Services\RoleServiceInterface;
use App\Services\RoomTypeService;
use App\Services\RoomTypeServiceInterface;
use App\Services\UserService;
use App\Services\UserServiceInterface;


class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Registra las vinculaciones de los repositorios.
     *
     * @return void
     */
    public function register(): void
    {
        // Vinculaciones para el dominio de Hoteles
        $this->app->bind(
            HotelRepositoryInterface::class,
            HotelRepository::class
        );

        // Vinculaciones para el dominio de Acomodaciones
        $this->app->bind(
            AccommodationRepositoryInterface::class,
            AccommodationRepository::class
        );

        // Vinculaciones para el dominio de Tipos de Habitación
        $this->app->bind(
            RoomTypeRepositoryInterface::class,
            RoomTypeRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
