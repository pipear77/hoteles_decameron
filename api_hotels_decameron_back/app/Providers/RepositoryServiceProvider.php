<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\AccommodationRepository;
use App\Repositories\AccommodationRepositoryInterface;
use App\Repositories\HotelRepository;
use App\Repositories\HotelRepositoryInterface;
use App\Repositories\HotelRoomRepository;
use App\Repositories\HotelRoomRepositoryInterface;
use App\Repositories\RoleRepository;
use App\Repositories\RoleRepositoryInterface;
use App\Repositories\RoomTypeRepository;
use App\Repositories\RoomTypeRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use App\Services\AccommodationService;
use App\Services\AccommodationServiceInterface;
use App\Services\HotelRoomService;
use App\Services\HotelRoomServiceInterface;
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
     * Register services.
     */
    public function register(): void
    {
        // Vinculaciones de Repositorios
        $this->app->bind(AccommodationRepositoryInterface::class, AccommodationRepository::class);
        $this->app->bind(HotelRepositoryInterface::class, HotelRepository::class);
        $this->app->bind(HotelRoomRepositoryInterface::class, HotelRoomRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(RoomTypeRepositoryInterface::class, RoomTypeRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        // Vinculaciones de Servicios
        $this->app->bind(AccommodationServiceInterface::class, AccommodationService::class);
        $this->app->bind(HotelServiceInterface::class, HotelService::class);
        $this->app->bind(HotelRoomServiceInterface::class, HotelRoomService::class);
        $this->app->bind(RoleServiceInterface::class, RoleService::class);
        $this->app->bind(RoomTypeServiceInterface::class, RoomTypeService::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
