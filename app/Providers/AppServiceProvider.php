<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // FORZAR HTTPS EN PRODUCCIÓN
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Log::info('APP SERVICE PROVIDER CARGADO');

        Event::listen(Login::class, function ($event) {
            Log::info('LOGIN DETECTADO');
        });
    }
}
