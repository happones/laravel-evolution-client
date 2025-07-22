<?php
// src/EvolutionServiceProvider.php

namespace Happones\LaravelEvolutionClient;

use Happones\LaravelEvolutionClient\Services\EvolutionService;
use Illuminate\Support\ServiceProvider;

class EvolutionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/evolution.php',
            'evolution'
        );

        // Register the main class to use with the facade
        $this->app->singleton('evolution', function ($app) {
            return new EvolutionApiClient(
                new EvolutionService(
                    config('evolution.base_url'),
                    config('evolution.api_key'),
                    config('evolution.timeout')
                ),
                config('evolution.default_instance')
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/evolution.php' => config_path('evolution.php'),
        ], 'evolution-config');

        // Register commands if we're in console
        if ($this->app->runningInConsole()) {
            // $this->commands([
            //     // Register commands here in the future
            // ]);
        }
    }
}
