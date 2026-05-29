<?php

namespace App\Providers;

use App\Listeners\SetUserRoleFromEmail;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\ServiceProvider;
use App\Filament\Auth\LoginResponse;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
            $this->app->bind(LoginResponseContract::class, LoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Event::listen(
            Login::class,
            SetUserRoleFromEmail::class
        );
    }
}
