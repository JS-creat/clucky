<?php

namespace App\Auth;

use Illuminate\Auth\Passwords\PasswordBrokerManager;

class CustomPasswordBrokerManager extends PasswordBrokerManager
{
    protected function createTokenRepository(array $config)
    {
        return new CustomPasswordTokenRepository(
            $this->app['db']->connection(),
            $this->app['hash'],
            $config['table'],
            $this->app['config']['app.key'],
            $config['expire'],
            $config['throttle'] ?? 0
        );
    }
}
