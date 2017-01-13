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
        //include __DIR__.'/routes.php';

        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadViewsFrom(__DIR__ . '/views', 'crud');
        $this->loadTranslationsFrom( __DIR__.'/../lang', 'crud');
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
        //$this->loadMigrationsFrom(__DIR__ . '/../migrations');

        $this->publishes([
            __DIR__ . '/../config/crud.php' => config_path('crud.php'),
            __DIR__ . '/../config/elfinder.php' => config_path('elfinder.php'),
            //__DIR__ . '/../../../vendor/barryvdh/config/elfinder.php' => config_path('elfinder.php'),

        ], 'config');

        //\Zofe\Rapyd\RapydServiceProvider->public_path()

        $this->publishes([
            __DIR__.'/../public/assets' => public_path('packages/wbe/crud/assets'),
            __DIR__.'/../public/rapyd' => public_path('packages/zofe/rapyd/assets'),
        ], 'public');

        $this->registerHelper(__DIR__.'/helpers.php');

        //assets
        //$this->publishes([__DIR__.'/../public/assets' => public_path('packages/zofe/rapyd/assets')], 'assets');

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

    /**
     * Register helpers file
     */
    public function registerHelper($fn)
    {
        // Load the helpers in app/Http/helpers.php
        if (file_exists($file = $fn))
        {
            require $file;
        } else die('no helper found: ' . $file);
    }
}
