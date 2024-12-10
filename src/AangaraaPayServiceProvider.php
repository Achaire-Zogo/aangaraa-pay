<?php

namespace Aangaraa\Pay;

use Illuminate\Support\ServiceProvider;
use Aangaraa\Pay\Services\AangaraaPayService;

class AangaraaPayServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/aangaraa-pay.php', 'aangaraa-pay'
        );

        $this->app->singleton('aangaraa-pay', function ($app) {
            return new AangaraaPayService();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/aangaraa-pay.php' => config_path('aangaraa-pay.php'),
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'aangaraa-pay');

        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
