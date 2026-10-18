<?php

// src/EvolutionServiceProvider.php

namespace Happones\LaravelEvolutionClient;

use Happones\LaravelEvolutionClient\Http\Controllers\WebhookController;
use Happones\LaravelEvolutionClient\Http\Middleware\VerifyEvolutionWebhook;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use Happones\LaravelEvolutionClient\Webhook\WebhookProcessor;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class EvolutionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/evolution.php',
            'evolution'
        );

        // Register the main class to use with the facade. The client is stateless:
        // instance() returns a new client, so sharing it across jobs/requests is safe.
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

        // Register the webhook processor (parses inbound payloads + dispatches events)
        $this->app->singleton(WebhookProcessor::class, function ($app) {
            return new WebhookProcessor($app->make(Dispatcher::class));
        });
        $this->app->alias(WebhookProcessor::class, 'evolution.webhook');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/evolution.php' => config_path('evolution.php'),
        ], 'evolution-config');

        // Register the inbound webhook route when enabled
        if (config('evolution.webhook.route.enabled')) {
            Route::middleware([
                ...(array) config('evolution.webhook.route.middleware', ['api']),
                VerifyEvolutionWebhook::class,
            ])
                ->post(
                    config('evolution.webhook.route.path', 'evolution/webhook'),
                    WebhookController::class
                )
                ->name(config('evolution.webhook.route.name', 'evolution.webhook'));
        }

        // Register commands if we're in console
        if ($this->app->runningInConsole()) {
            // $this->commands([
            //     // Register commands here in the future
            // ]);
        }
    }
}
