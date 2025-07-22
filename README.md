# Laravel Evolution Client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/happones/laravel-evolution-client.svg?style=flat-square)](https://packagist.org/packages/happones/laravel-evolution-client)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/happones/laravel-evolution-client/run-tests?label=tests)](https://github.com/happones/laravel-evolution-client/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/happones/laravel-evolution-client/Check%20&%20fix%20styling?label=code%20style)](https://github.com/happones/laravel-evolution-client/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/happones/laravel-evolution-client.svg?style=flat-square)](https://packagist.org/packages/happones/laravel-evolution-client)

A Laravel client for the Evolution API, providing simple integration with WhatsApp for messaging, group management, and more.

## Features

- Complete WhatsApp functionality through Evolution API
- Send and receive messages (text, media, buttons, lists, polls)
- Create and manage groups
- Manage contacts and labels
- Handle webhook events
- Simple and clean Laravel integration

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
```

### Working with Chats

```php
use Happones\LaravelEvolutionClient\Facades\Evolution;

// List all chats
$chats = Evolution::chat->all();

// Find a specific chat
$chat = Evolution::chat->find('5511999999999');

// Get messages from a chat
$messages = Evolution::chat->messages('5511999999999', 20);

// Mark a chat as read
Evolution::chat->markAsRead('5511999999999');
```

### Working with Groups

```php
use Happones\LaravelEvolutionClient\Facades\Evolution;

// List all groups
$groups = Evolution::group->all();

// Create a new group
$newGroup = Evolution::group->create('Group Name', [
    '5511999999999',
    '5511888888888',
]);

// Add participants to a group
Evolution::group->addParticipants($groupId, [
    '5511777777777',
]);

// Promote to admin
Evolution::group->promoteToAdmin($groupId, '5511999999999');
```

### Sending Different Types of Messages

```php
use Happones\LaravelEvolutionClient\Facades\Evolution;
use Happones\LaravelEvolutionClient\Models\Button;
use Happones\LaravelEvolutionClient\Models\ListRow;
use Happones\LaravelEvolutionClient\Models\ListSection;

// Send text
Evolution::message->sendText('5511999999999', 'Hello, how are you?');

// Send text with delay and link preview
Evolution::message->sendText('5511999999999', 'Check out this website: https://example.com', false, 1000, true);

// Send image
Evolution::message->sendImage('5511999999999', 'https://example.com/image.jpg', 'Image caption');

// Send document
Evolution::message->sendDocument('5511999999999', 'https://example.com/document.pdf', 'filename.pdf', 'Check out this document');

// Send location
Evolution::message->sendLocation('5511999999999', -23.5505, -46.6333, 'São Paulo', 'Paulista Avenue, 1000');

// Send contact
Evolution::message->sendContact('5511999999999', 'Contact Name', '5511888888888');

// Send poll
Evolution::message->sendPoll('5511999999999', 'What is your favorite color?', 1, ['Blue', 'Green', 'Red', 'Yellow']);

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

Evolution::message->sendList(
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

Evolution::message->sendButtons(
    '5511999999999',
    'Confirmation',
    'Do you want to proceed with the operation?',
    'Choose an option below',
    $buttons
);

// Send reaction to a message
Evolution::message->sendReaction(
    ['remoteJid' => '5511999999999@c.us', 'id' => 'ABCDEF123456', 'fromMe' => false],
    '👍'
);

// Send status
Evolution::message->sendStatus(
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
$labels = Evolution::label->findLabels();

// Add a label to a chat
Evolution::label->addLabel('5511999999999', 'label_id_123');

// Remove a label from a chat
Evolution::label->removeLabel('5511999999999', 'label_id_123');
```

### Working with Calls

```php
use Happones\LaravelEvolutionClient\Facades\Evolution;

// Make a fake call
Evolution::call->fakeCall('5511999999999', false, 45); // Voice call with 45 seconds
Evolution::call->fakeCall('5511999999999', true, 30);  // Video call with 30 seconds
```

### Working with Profile

```php
use Happones\LaravelEvolutionClient\Facades\Evolution;

// Fetch a contact's profile
$profile = Evolution::profile->fetchProfile('5511999999999');

// Fetch business profile
$businessProfile = Evolution::profile->fetchBusinessProfile('5511999999999');

// Update profile name
Evolution::profile->updateProfileName('My Name');

// Update status
Evolution::profile->updateProfileStatus('Available for service');

// Update profile picture
Evolution::profile->updateProfilePicture('data:image/jpeg;base64,/9j/4AAQSkZJRgABAQE...');

// Remove profile picture
Evolution::profile->removeProfilePicture();

// Fetch privacy settings
$privacySettings = Evolution::profile->fetchPrivacySettings();

// Update privacy settings
Evolution::profile->updatePrivacySettings(
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
Evolution::websocket->setWebSocket(true, [
    'message',
    'message.ack',
    'status.instance'
]);

// Fetch WebSocket configuration
$webSocketConfig = Evolution::websocket->findWebSocket();

// Create a WebSocket client
$webSocketClient = Evolution::websocket->createClient();

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
$response = Evolution::template->create(
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
$templates = Evolution::template->find();

// Send a template message
Evolution::message->sendTemplate(
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
Evolution::settings->set(
    true,  // reject calls
    'I cannot take calls right now', // call message
    false, // don't ignore groups
    true,  // always show online
    false, // don't read messages automatically
    false, // don't sync full history
    false  // don't read status automatically
);

// Get current settings
$settings = Evolution::settings->find();
```

Using Proxy
```php
// Set proxy
Evolution::proxy->set(
    true,     // enabled
    '127.0.0.1', // host
    '8080',   // port
    'http',   // protocol
    'username', // optional
    'password'  // optional
);

// Get current proxy settings
$proxy = Evolution::proxy->find();
```

## Testing
```bash
composer test
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
