<?php

namespace Michaelruther95\LaravelAuthGuard;

use Illuminate\Support\ServiceProvider;
use Michaelruther95\LaravelAuthGuard\Services\Authenticator;

class MainServiceProvider extends ServiceProvider {

    public function register () {
        
        $this->app->singleton(Authenticator::class, function () {
            return new Authenticator();
        });
    
    }

}