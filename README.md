# Laravel Evolution Client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/happones/laravel-evolution-client.svg?style=flat-square)](https://packagist.org/packages/happones/laravel-evolution-client)
[![run-tests](https://github.com/happones/laravel-evolution-client/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/happones/laravel-evolution-client/actions/workflows/run-tests.yml)
[![Fix PHP code style issues](https://github.com/happones/laravel-evolution-client/actions/workflows/fix-php-code-style-issues.yml/badge.svg?branch=main)](https://github.com/happones/laravel-evolution-client/actions/workflows/fix-php-code-style-issues.yml)
[![PHPStan](https://github.com/happones/laravel-evolution-client/actions/workflows/phpstan.yml/badge.svg?branch=main)](https://github.com/happones/laravel-evolution-client/actions/workflows/phpstan.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/happones/laravel-evolution-client.svg?style=flat-square)](https://packagist.org/packages/happones/laravel-evolution-client)

A Laravel client for the Evolution API, providing simple integration with WhatsApp for messaging, group management, and more.

## Features

- Complete WhatsApp functionality through Evolution API
- Send and receive messages (text, media, buttons, lists, polls)
- Create and manage groups
- Manage contacts and labels
- Handle webhook events
- Simple and clean Laravel integration

## Requirements

- PHP 8.3+
- Laravel 11, 12 or 13
- An [Evolution API v2](https://doc.evolution-api.com/) server

## Installation

You can install the package via composer:

```bash
composer require happones/laravel-evolution-client
```

You can publish the configuration file with:

```bash
php artisan vendor:publish --tag="evolution-config"
```

This is the content of the published configuration file:

```php
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
];
```

## Usage

### Configuring the .env

```
EVOLUTION_API_URL=http://your-evolution-api.com
EVOLUTION_API_KEY=your-api-key
EVOLUTION_DEFAULT_INSTANCE=default
```

### Using the Facade

```php
use Happones\LaravelEvolutionClient\Facades\Evolution;

// Check QR Code
$qrCode = Evolution::getQrCode();

// Check if connected
$connected = Evolution::isConnected();

// Send text message
$result = Evolution::sendText('5511999999999', 'Hello, this is a test message!');
```

### Using Different Instances

```php
use Happones\LaravelEvolutionClient\Facades\Evolution;

// Use a specific instance
$result = Evolution::instance('my-instance')->sendText('5511999999999', 'Hello!');

// Keep a client bound to one instance
$sales = Evolution::instance('sales');
$sales->message->sendText('5511999999999', 'Hi from sales');
```

`instance()` returns a **new** client bound to that instance and never mutates the shared
container singleton, so it is safe in queue workers and Laravel Octane: one job or request
can never inherit another's instance name. Always chain from `instance()` (or keep the
returned client) instead of calling it for its side effect.

Phone numbers are sent as bare digits with country code (e.g. `5511999999999`), as
Evolution API v2 expects; any value that already contains `@` (`...@g.us`,
`...@s.whatsapp.net`, `...@lid`) is passed through unchanged.

### Linking a Device (QR or Pairing Code)

```php
use Happones\LaravelEvolutionClient\Facades\Evolution;

// QR code
$qr = Evolution::instance('sales')->getQrCode();

// Pairing code: the user types it in WhatsApp > Linked devices > Link with phone number
$code = Evolution::instance('sales')->getPairingCode('5215512345678'); // e.g. "WZYEH1YY"

// Or create the instance already pointing at the owner's number
Evolution::instance('sales')->instance->createInstance('sales', number: '5215512345678');
```

### Working with Chats

```php
use Happones\LaravelEvolutionClient\Facades\Evolution;

// List all chats
$chats = Evolution::instance('my-instance')->chat->all();

// Find a specific chat
$chat = Evolution::instance('my-instance')->chat->find('5511999999999');

// Get messages from a chat (20 per page, page 1)
$messages = Evolution::instance('my-instance')->chat->messages('5511999999999', 20, 1);

// Delete a message for everyone
Evolution::instance('my-instance')->chat->deleteMessageForEveryone($messageId, '5511999999999@s.whatsapp.net', fromMe: true);

// Mark a chat as read
Evolution::instance('my-instance')->chat->markAsRead('5511999999999');
```

### Working with Groups

```php
use Happones\LaravelEvolutionClient\Facades\Evolution;

// List all groups
$groups = Evolution::instance('my-instance')->group->all();

// Create a new group
$newGroup = Evolution::instance('my-instance')->group->create('Group Name', [
    '5511999999999',
    '5511888888888',
]);

// Add participants to a group
Evolution::instance('my-instance')->group->addParticipants($groupId, [
    '5511777777777',
]);

// Promote to admin
Evolution::instance('my-instance')->group->promoteToAdmin($groupId, '5511999999999');
```

### Sending Different Types of Messages

```php
use Happones\LaravelEvolutionClient\Facades\Evolution;
use Happones\LaravelEvolutionClient\Models\Button;
use Happones\LaravelEvolutionClient\Models\ListRow;
use Happones\LaravelEvolutionClient\Models\ListSection;

// Send text
Evolution::instance('my-instance')->message->sendText('5511999999999', 'Hello, how are you?');

// Send text with delay and link preview
Evolution::instance('my-instance')->message->sendText('5511999999999', 'Check out this website: https://example.com', false, 1000, true);

// Send image
Evolution::instance('my-instance')->message->sendImage('5511999999999', 'https://example.com/image.jpg', 'Image caption');

// Send document
Evolution::instance('my-instance')->message->sendDocument('5511999999999', 'https://example.com/document.pdf', 'filename.pdf', 'Check out this document');

// Send location
Evolution::instance('my-instance')->message->sendLocation('5511999999999', -23.5505, -46.6333, 'São Paulo', 'Paulista Avenue, 1000');

// Send contact
Evolution::instance('my-instance')->message->sendContact('5511999999999', 'Contact Name', '5511888888888');

// Send poll
Evolution::instance('my-instance')->message->sendPoll('5511999999999', 'What is your favorite color?', 1, ['Blue', 'Green', 'Red', 'Yellow']);

// Send list
$rows1 = [
    new ListRow('Option 1', 'Description of option 1', 'opt1'),
    new ListRow('Option 2', 'Description of option 2', 'opt2')
];
$rows2 = [
    new ListRow('Option 3', 'Description of option 3', 'opt3'),
    new ListRow('Option 4', 'Description of option 4', 'opt4')
];

$sections = [
    new ListSection('Section 1', $rows1),
    new ListSection('Section 2', $rows2)
];

Evolution::instance('my-instance')->message->sendList(
    '5511999999999',
    'List Title',
    'Choose an option',
    'View Options',
    'List footer',
    $sections
);

// Send buttons
$buttons = [
    new Button('reply', 'Yes', ['id' => 'btn-yes']),
    new Button('reply', 'No', ['id' => 'btn-no']),
    new Button('url', 'Visit Website', ['url' => 'https://example.com'])
];

Evolution::instance('my-instance')->message->sendButtons(
    '5511999999999',
    'Confirmation',
    'Do you want to proceed with the operation?',
    'Choose an option below',
    $buttons
);

// Send reaction to a message
Evolution::instance('my-instance')->message->sendReaction(
    ['remoteJid' => '5511999999999@s.whatsapp.net', 'id' => 'ABCDEF123456', 'fromMe' => false],
    '👍'
);

// Send status
Evolution::instance('my-instance')->message->sendStatus(
    'text',
    'Hello, this is my status!',
    null,
    '#25D366',
    2,
    true
);
```

### Working with Labels

```php
use Happones\LaravelEvolutionClient\Facades\Evolution;

// List all labels
$labels = Evolution::instance('my-instance')->label->findLabels();

// Add a label to a chat
Evolution::instance('my-instance')->label->addLabel('5511999999999', 'label_id_123');

// Remove a label from a chat
Evolution::instance('my-instance')->label->removeLabel('5511999999999', 'label_id_123');
```

### Working with Calls

```php
use Happones\LaravelEvolutionClient\Facades\Evolution;

// Make a fake call
Evolution::instance('my-instance')->call->fakeCall('5511999999999', false, 45); // Voice call with 45 seconds
Evolution::instance('my-instance')->call->fakeCall('5511999999999', true, 30);  // Video call with 30 seconds
```

### Working with Profile

```php
use Happones\LaravelEvolutionClient\Facades\Evolution;

// Fetch a contact's profile
$profile = Evolution::instance('my-instance')->profile->fetchProfile('5511999999999');

// Fetch business profile
$businessProfile = Evolution::instance('my-instance')->profile->fetchBusinessProfile('5511999999999');

// Update profile name
Evolution::instance('my-instance')->profile->updateProfileName('My Name');

// Update status
Evolution::instance('my-instance')->profile->updateProfileStatus('Available for service');

// Update profile picture
Evolution::instance('my-instance')->profile->updateProfilePicture('data:image/jpeg;base64,/9j/4AAQSkZJRgABAQE...');

// Remove profile picture
Evolution::instance('my-instance')->profile->removeProfilePicture();

// Fetch privacy settings
$privacySettings = Evolution::instance('my-instance')->profile->fetchPrivacySettings();

// Update privacy settings
Evolution::instance('my-instance')->profile->updatePrivacySettings(
    'all',               // readreceipts
    'contacts',          // profile
    'contacts',          // status
    'all',               // online
    'contacts',          // last
    'contacts'           // groupadd
);
```

### Working with WebSocket

```php
use Happones\LaravelEvolutionClient\Facades\Evolution;

// Configure WebSocket
Evolution::instance('my-instance')->websocket->setWebSocket(true, [
    'message',
    'message.ack',
    'status.instance'
]);

// Fetch WebSocket configuration
$webSocketConfig = Evolution::instance('my-instance')->websocket->findWebSocket();

// Create a WebSocket client
$webSocketClient = Evolution::instance('my-instance')->websocket->createClient();

// Register handlers for events
$webSocketClient->on('message', function ($data) {
    // Process received message
    Log::info('New message received', $data);
});

$webSocketClient->on('message.ack', function ($data) {
    // Process read confirmation
    Log::info('Message read', $data);
});

// Connect to WebSocket server
$webSocketClient->connect();

// ... At some later point, disconnect
$webSocketClient->disconnect();
```

Working with Templates

```php
// Create a template
$response = Evolution::instance('my-instance')->template->create(
    'my_template',
    'MARKETING',
    'en_US',
    [
        [
            'type' => 'BODY',
            'text' => 'Hello {{1}}, welcome to our service!',
            'example' => [
                'body_text' => [
                    ['John Doe']
                ]
            ]
        ],
        [
            'type' => 'BUTTONS',
            'buttons' => [
                [
                    'type' => 'QUICK_REPLY',
                    'text' => 'Yes, please'
                ],
                [
                    'type' => 'QUICK_REPLY',
                    'text' => 'No, thanks'
                ]
            ]
        ]
    ]
);

// Find templates
$templates = Evolution::instance('my-instance')->template->find();

// Send a template message
Evolution::instance('my-instance')->message->sendTemplate(
    '5511999999999',
    'my_template',
    'en_US',
    [
        [
            'type' => 'body',
            'parameters' => [
                [
                    'type' => 'text',
                    'text' => 'John Doe'
                ]
            ]
        ]
    ]
);
```

Managing Settings

```php
// Set instance settings
Evolution::instance('my-instance')->settings->set(
    true,  // reject calls
    'I cannot take calls right now', // call message
    false, // don't ignore groups
    true,  // always show online
    false, // don't read messages automatically
    false, // don't sync full history
    false  // don't read status automatically
);

// Get current settings
$settings = Evolution::instance('my-instance')->settings->find();
```

Evolution Bot

```php
// Create evolution bot
Evolution::instance('my-instance')->evolutionBot->create(
    enabled: true,
    apiUrl: 'https://api.mybot.com/webhook',
    apiKey: 'your-secret-api-key-for-the-bot',
    triggerType: 'keyword',
    triggerOperator: 'equals',
    triggerValue: '!menu',
    expire: 300,
    keywordFinish: '!exit',
    delayMessage: 1200,
    unknownMessage: 'Sorry.',
    listeningFromMe: false,
    stopBotFromMe: true,
    keepOpen: false,
    debounceTime: 1000
);

// Delete evolution bot
$settings = Evolution::instance('my-instance')->evolutionBot->destroy($evolutionBotId);
```

Using Proxy
```php
// Set proxy
Evolution::instance('my-instance')->proxy->set(
    true,     // enabled
    '127.0.0.1', // host
    '8080',   // port
    'http',   // protocol
    'username', // optional
    'password'  // optional
);

// Get current proxy settings
$proxy = Evolution::instance('my-instance')->proxy->find();
```

### Handling Webhooks

Evolution API delivers events (incoming messages, connection changes, QR updates, …) to a
URL you configure. This package parses those payloads into a typed `WebhookEvent` and
exposes them for use.

**1. Register the endpoint.** Enable the built-in route in `config/evolution.php`:

```php
'webhook' => [
    'route' => [
        'enabled'    => env('EVOLUTION_WEBHOOK_ROUTE_ENABLED', true),
        'path'       => env('EVOLUTION_WEBHOOK_ROUTE_PATH', 'evolution/webhook'),
        'middleware' => ['api'],
    ],
],
```

Point your instance webhook at `https://your-app.test/evolution/webhook`. (Or build your
own controller and call `WebhookProcessor::process($request)` yourself.)

**Secure the endpoint.** Evolution API does not sign webhooks, so the package checks a
shared secret sent as a custom header. Set `EVOLUTION_WEBHOOK_SECRET` and register the same
value as a header on the instance webhook:

```php
Evolution::instance('sales')->instance->setWebhook(
    url: route('evolution.webhook'),
    events: ['MESSAGES_UPSERT', 'CONNECTION_UPDATE'],
    headers: ['X-Evolution-Secret' => config('evolution.webhook.secret')],
);
```

Requests without the matching header (`EVOLUTION_WEBHOOK_SECRET_HEADER`, default
`X-Evolution-Secret`) get `401`; payloads without an `event` get `422`. When the secret is
empty the header check is skipped, so always set it on public endpoints. The check lives in
the `VerifyEvolutionWebhook` middleware, which you can also attach to your own route.

**2. Listen for events.** Every webhook dispatches `EvolutionWebhookReceived`, plus a
named `evolution.webhook.{event}` event:

```php
use Happones\LaravelEvolutionClient\Events\EvolutionWebhookReceived;
use Happones\LaravelEvolutionClient\Webhook\WebhookEvent;

Event::listen(function (EvolutionWebhookReceived $received) {
    $event = $received->webhook;                 // WebhookEvent

    if ($event->isMessageUpsert()) {
        $from = $event->get('key.remoteJid');
        $text = $event->get('message.conversation');
        // ...
    }
});
```

Make your listener implement `ShouldQueue` to process it on a queue.

**3. (Optional) Queue at the edge.** To return `200` to Evolution instantly and process
off the request lifecycle, enable the queue — a `ProcessEvolutionWebhook` job is dispatched
instead of running inline:

```php
'webhook' => [
    'queue' => ['enabled' => env('EVOLUTION_WEBHOOK_QUEUE_ENABLED', true)],
],
```

You can also register inline callbacks without Laravel events:

```php
app(\Happones\LaravelEvolutionClient\Webhook\WebhookProcessor::class)
    ->on(WebhookEvent::MESSAGES_UPSERT, fn (WebhookEvent $e) => logger($e->toArray()))
    ->onAny(fn (WebhookEvent $e) => logger("event: {$e->name()}"));
```

## Testing

```bash
composer test        # Pest test suite
composer lint        # Laravel Pint (code style, dry-run)
composer analyse     # Larastan level 5 (static analysis)
composer format      # apply Pint fixes
```

## Changelog

Please see the [CHANGELOG](CHANGELOG.md) for more information about what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email happones@gmail.com instead of using the issue tracker.

## Credits

- [Samuel Terra](https://github.com/happones)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see the [License File](LICENSE.md) for more information.
