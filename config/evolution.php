<?php

// config/evolution.php

return [
    /*
    |--------------------------------------------------------------------------
    | Evolution API Base URL
    |--------------------------------------------------------------------------
    |
    | This is the base URL for the Evolution API endpoints. This should be
    | the URL of your Evolution API server.
    |
    */
    'base_url' => env('EVOLUTION_API_URL', 'http://localhost:8080'),

    /*
    |--------------------------------------------------------------------------
    | Evolution API Key
    |--------------------------------------------------------------------------
    |
    | This is your API key which is used to authenticate with the Evolution API.
    | You can get this from your Evolution API configuration.
    |
    */
    'api_key' => env('EVOLUTION_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Default Instance Name
    |--------------------------------------------------------------------------
    |
    | The default instance name to use when none is provided.
    |
    */
    'default_instance' => env('EVOLUTION_DEFAULT_INSTANCE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | This value determines the maximum number of seconds to wait for a response
    | from the Evolution API server.
    |
    */
    'timeout' => env('EVOLUTION_API_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Webhook URL
    |--------------------------------------------------------------------------
    |
    | The URL where Evolution API will send webhook events.
    |
    */
    'webhook_url' => env('EVOLUTION_WEBHOOK_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Webhook Events
    |--------------------------------------------------------------------------
    |
    | The events that should trigger the webhook.
    |
    */
    'webhook_events' => [
        'message',
        'message.ack',
        'status.instance',
        // Add more events as needed
    ],

    /*
    |--------------------------------------------------------------------------
    | Inbound Webhook Handling
    |--------------------------------------------------------------------------
    |
    | Configuration for receiving webhook callbacks from Evolution API. When the
    | route is enabled, the package registers a POST endpoint that parses the
    | payload into a WebhookEvent and dispatches Laravel events you can listen to
    | (see Happones\LaravelEvolutionClient\Events\EvolutionWebhookReceived).
    |
    */
    'webhook' => [
        'route' => [
            'enabled' => env('EVOLUTION_WEBHOOK_ROUTE_ENABLED', false),
            'path' => env('EVOLUTION_WEBHOOK_ROUTE_PATH', 'evolution/webhook'),
            'name' => 'evolution.webhook',
            'middleware' => ['api'],
        ],

        /*
        | Shared secret used to authenticate inbound webhooks. Evolution API does
        | not sign its requests, so configure the same value as a custom header on
        | the webhook, e.g.:
        |
        |   Evolution::instance('sales')->instance->setWebhook(
        |       url: route('evolution.webhook'),
        |       headers: ['X-Evolution-Secret' => config('evolution.webhook.secret')],
        |   );
        |
        | Requests without the matching header are rejected with 401. Leave empty
        | to disable the check (not recommended for public endpoints).
        */
        'secret' => env('EVOLUTION_WEBHOOK_SECRET', ''),
        'secret_header' => env('EVOLUTION_WEBHOOK_SECRET_HEADER', 'X-Evolution-Secret'),

        /*
        | When queue.enabled is true, the built-in webhook controller pushes a
        | ProcessEvolutionWebhook job instead of parsing/dispatching inline, so
        | the HTTP response returns immediately and Evolution stops retrying.
        | Leave it false to process synchronously within the request. Either way,
        | your own event listeners may still implement ShouldQueue independently.
        */
        'queue' => [
            'enabled' => env('EVOLUTION_WEBHOOK_QUEUE_ENABLED', false),
            'connection' => env('EVOLUTION_WEBHOOK_QUEUE_CONNECTION', null),
            'queue' => env('EVOLUTION_WEBHOOK_QUEUE_NAME', null),
        ],
    ],
];
