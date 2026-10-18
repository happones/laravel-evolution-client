---
name: laravel-evolution-client
description: How to use the happones/laravel-evolution-client package to talk to the Evolution API v2 (WhatsApp) from Laravel — send messages, manage instances, chats, groups, labels, profiles and webhooks via the Evolution facade. Use whenever the app integrates WhatsApp through Evolution API.
---

# Laravel Evolution Client

A Laravel HTTP client for the **Evolution API v2** (WhatsApp). Exposes a fluent
`Evolution` facade backed by per-domain Resources. All calls return `array` (the decoded
API response) and throw `Happones\LaravelEvolutionClient\Exceptions\EvolutionApiException`
on API failures.

## Setup

Config lives in `config/evolution.php` (publish with
`php artisan vendor:publish --tag="evolution-config"`). Set these env vars:

```dotenv
EVOLUTION_API_URL=http://localhost:8080
EVOLUTION_API_KEY=your-api-key
EVOLUTION_DEFAULT_INSTANCE=default
```

## Core concept: instances

An *instance* is a WhatsApp connection. The default instance comes from config; switch at
runtime with `Evolution::instance('name')`, which returns a **new** client bound to that
instance (the shared singleton is never mutated, so it is safe in queues and Octane). Always
chain from it or keep the returned client — never call it just for its side effect.

```php
use Happones\LaravelEvolutionClient\Facades\Evolution;

// Uses the default instance
Evolution::sendText('5511999999999', 'Hello from Laravel');

// Target a specific instance, then use a resource
Evolution::instance('sales')->message->sendText('5511999999999', 'Hi');
```

## Resources

Access each domain as a property on the client (or via the facade's convenience methods):
`message`, `chat`, `group`, `instance`, `call`, `label`, `profile`, `websocket`,
`template`, `proxy`, `settings`, `openAIBot`, `evolutionBot`, `business`.

### Instance lifecycle

```php
Evolution::createInstance('sales');          // provision a new instance
$qr = Evolution::instance('sales')->getQrCode();   // QR to link WhatsApp
$code = Evolution::instance('sales')->getPairingCode('5215512345678'); // or link by phone number
$connected = Evolution::instance('sales')->isConnected();
Evolution::instance('sales')->disconnect();
```

### Messaging

```php
Evolution::instance('sales')->message->sendText('5511999999999', 'Hello');
Evolution::instance('sales')->message->sendReaction('5511999999999', $messageKey, '👍');
Evolution::instance('sales')->message->sendPoll('5511999999999', 'Lunch?', ['Yes', 'No']);
```

Verify the exact method signature in `src/Resources/Message.php` before use — media,
buttons, lists, location, contacts and status messages each have dedicated methods.

### Chats, groups, profiles

```php
Evolution::instance('sales')->chat->findChats([]);
Evolution::instance('sales')->group->create('Team', ['5511...', '5511...']);
Evolution::instance('sales')->profile->fetchProfile('5511999999999');
```

## Error handling

```php
use Happones\LaravelEvolutionClient\Exceptions\EvolutionApiException;

try {
    Evolution::sendText('5511999999999', 'Hi');
} catch (EvolutionApiException $e) {
    report($e);           // $e->getMessage() / $e->getCode() carry the API error
}
```

## Rules for agents

- Prefer the `Evolution` facade; resolve `'evolution'` from the container only when you
  need a fresh, non-facade instance.
- Never assume a method exists — check `src/Resources/*.php` (or the facade `@method`
  PHPDoc in `src/Facades/Evolution.php`) for the real signature.
- Phone numbers are passed as digits with country code (e.g. `5511999999999`), no `+` and
  no `@c.us` suffix. Group/JID values containing `@` are passed through unchanged.
- All resource methods can throw `EvolutionApiException`; wrap network-facing calls.
- To handle inbound events, enable `evolution.webhook.route`, set `EVOLUTION_WEBHOOK_SECRET`
  and send the same value as the `X-Evolution-Secret` header via
  `->instance->setWebhook(url: ..., headers: [...])`; then listen for
  `EvolutionWebhookReceived` (see the package README).
