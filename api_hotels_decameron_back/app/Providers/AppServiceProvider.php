<?php

namespace App\Providers;

use App\Services\UserService;
use App\Services\UserServiceInterface;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\HotelRepositoryInterface::class,
            \App\Repositories\HotelRepository::class
        );

        $this->app->bind(
            \App\Services\HotelServiceInterface::class,
            \App\Services\HotelService::class
        );

        // Esto le dice a Laravel: "Cuando veas la interfaz 'UserServiceInterface',
        // usa la clase concreta 'UserService' para resolverla."
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
