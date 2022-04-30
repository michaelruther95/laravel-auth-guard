<?php

namespace Michaelruther95\LaravelAuthGuard;

use Illuminate\Support\ServiceProvider;
use Michaelruther95\LaravelAuthGuard\Services\Authenticator;
use Michaelruther95\LaravelAuthGuard\Services\PasswordReset;

class MainServiceProvider extends ServiceProvider {

    public function boot () {
        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');
    }

    public function register () {
        
        $this->app->singleton(Authenticator::class, function () {
            return new Authenticator();
        });

        $this->app->singleton(PasswordReset::class, function () {
            return new PasswordReset();
        });
    
    }

}