<?php

namespace App\Providers;

use App\Observers\UserObserver;
use Careminate\Databases\Model;
use Careminate\Container\ServiceContainer;
use Careminate\ServiceProviders\ServiceProvider;
use Careminate\Databases\Drivers\SQLiteConnection;
use Careminate\Databases\Contracts\DatabaseConnectionInterface;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
       
         // Register observers here
         Model::observe(UserObserver::class);
         // Register Connections
         ServiceContainer::singleton(DatabaseConnectionInterface::class, fn() => new SQLiteConnection());
    }
}