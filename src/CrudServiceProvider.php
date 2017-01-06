<?php

namespace Wbe\Crud;

use Illuminate\Support\ServiceProvider;

class CrudServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // load routes
        include __DIR__.'/routes.php';

        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadViewsFrom(__DIR__ . '/views', 'crud');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // load routes
        /*include __DIR__.'/routes.php';

        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadViewsFrom(__DIR__ . '/views/', 'crud');*/
    }
}
