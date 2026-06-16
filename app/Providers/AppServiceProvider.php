<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Auth\CustomPasswordBrokerManager;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->extend('auth.password', function ($service, $app) {
            return new \App\Auth\CustomPasswordBrokerManager($app);
        });
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Log::info('APP SERVICE PROVIDER CARGADO');

        Event::listen(Login::class, function ($event) {
            Log::info('LOGIN DETECTADO');
        });
    }
}
