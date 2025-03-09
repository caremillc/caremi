<?php 
namespace App\Providers;

use League\Container\Container;
use Careminate\ServiceProviders\LocaleService;

class LocaleServiceProvider
{
    protected $container;

    // Inject the container into the provider
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function register()
    {
        // Register LocaleService in the container
        $this->container->add(LocaleService::class, function () {
            return new LocaleService();
        });
    }

    public function boot()
    {
        // Any bootstrapping logic if needed
    }
}
