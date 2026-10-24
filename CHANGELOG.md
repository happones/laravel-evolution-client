# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [4.0.1] - 2026-10-24

### Fixed
- **`"error": false` is no longer a failure.** Evolution answers several successes (logout,
  instance delete, restart) with `"error": false`; the client threw on them because it only
  checked that the key existed. Only a truthy `error` or `"status": "error"` fails now.
- **API errors are no longer wrapped as "Unexpected error: …".** An `EvolutionApiException`
  raised while reading the response (an API error, invalid JSON) now reaches the caller with its
  own message and code instead of being re-wrapped by the generic `Exception` handler.

## [4.0.0] - 2026-10-17

### Added
- **Laravel 11 and 12 support** in addition to 13 (`illuminate/* ^11.0|^12.0|^13.0`);
  CI now tests L11 (testbench 9), L12 (testbench 10) and L13 (testbench 11).
  Guzzle 8 is allowed where the framework permits it (`^7.8|^8.0`).
- **Pairing code.** `Instance::connect(?string $number)` forwards `number` to
  `GET /instance/connect/{instance}`; new `Instance::getPairingCode()` and
  `EvolutionApiClient::getPairingCode()` return the `pairingCode` to link a device by phone
  number instead of QR.
- **Webhook verification.** New `VerifyEvolutionWebhook` middleware (always attached to the
  built-in route): when `evolution.webhook.secret` (`EVOLUTION_WEBHOOK_SECRET`) is set,
  requests must carry it in `evolution.webhook.secret_header` (default
  `X-Evolution-Secret`) or get `401`; payloads without an `event` get `422`.
- `Chat::deleteMessageForEveryone()` (`DELETE /chat/deleteMessageForEveryone/{instance}`).
- `EvolutionApiClient::getInstanceName()`.
- `EvolutionService::delete()` accepts an optional JSON body as third argument.

### Changed
- **BREAKING — `EvolutionApiClient::instance()` no longer mutates the client.** It returns a
  new client bound to the given instance. The `evolution` singleton is now stateless, so
  queue workers and Octane can no longer leak one instance's name into another job or
  request. Code that called `Evolution::instance('x');` for its side effect and used the
  facade afterwards must chain from (or keep) the returned client.
- **BREAKING — phone numbers are sent as bare digits.** The `@c.us` suffix (a v1 /
  whatsapp-web.js convention) is no longer appended in `Message`, `Chat` and `Business`;
  Evolution v2 builds the JID itself. Values that already contain `@` are passed through.
- **BREAKING — `OpenAIBot` rewritten on the real v2 routes** (`/openai/*/{instance}`); the
  previous `integrations/openai/*` paths do not exist in Evolution v2. New signatures:
  `create($openaiCredsId, $model, $botType, $enabled, $options)`,
  `setCredentials($name, $apiKey)`, `deleteCredentials($credentialsId)`,
  `changeStatus($remoteJid, $status)`.
- `Message::sendImage()` / `sendDocument()` now use `POST /message/sendMedia/{instance}`
  (the old `/message/chat/send/*` paths do not exist in v2). They gained optional
  `mimetype`/`fileName`/`delay` arguments; the document MIME type is guessed from the
  file extension.
- `Chat::all()`, `find()` and `messages()` now use `findChats` / `findMessages`;
  `messages()` gained a `$page` argument.
- Test suite migrated from `/** @test */` docblocks to `#[Test]` attributes — PHPUnit 12
  (Pest 4) silently skipped ~130 annotated tests.
- Dev dependencies updated (Pest `^3.8|^4.0`, Larastan 3.13, Pint 1.32, Testbench 11.3).

### Removed
- **BREAKING —** `Chat::clearMessages()` and `Chat::delete()`: their `/chat/clear` and
  `/chat/delete` endpoints do not exist in Evolution API v2.

### Upgrading from 3.x
- Replace `Evolution::instance('x'); Evolution::...` with
  `Evolution::instance('x')->...` (or `$client = Evolution::instance('x');`).
- If you stored or compared `...@c.us` JIDs, use `...@s.whatsapp.net` or bare numbers.
- Update `OpenAIBot` calls to the new signatures.
- Set `EVOLUTION_WEBHOOK_SECRET` and send it as a webhook header via
  `->instance->setWebhook(headers: ['X-Evolution-Secret' => ...])`.

## [3.0.2] - 2026-07-08

### Fixed
- `Instance::setWebhook()` now nests the configuration under `webhook` as required by the
  deployed Evolution API v2 server (the flat payload returned `400 Bad Request: instance
  requires property "webhook"`). Verified end-to-end against a live server.
- `Instance::restart()` reverted to `POST` (the deployed server returns `404` for `PUT`,
  despite the OpenAPI spec listing `PUT`).

## [3.0.1] - 2026-07-08

### Fixed
- Move the Laravel Boost skill to the expected
  `resources/boost/skills/laravel-evolution-client/SKILL.md` path so Boost auto-discovers
  and publishes it on install.

## [3.0.0] - 2026-07-08

### Added
- **Inbound webhook handling.** New `WebhookProcessor` manager parses Evolution payloads
  into a typed `WebhookEvent` (normalised event names, dot-access to `data`, `is*()`
  helpers) and dispatches the `EvolutionWebhookReceived` Laravel event plus a named
  `evolution.webhook.{event}` event. Inline `on()` / `onAny()` callbacks are also
  supported. An optional, config-gated route + `WebhookController` is provided, and a
  queued `ProcessEvolutionWebhook` job lets the endpoint return immediately and process
  off the request lifecycle (`config('evolution.webhook.queue.enabled')`).
- `Message::sendMedia()` for image/video/document/audio media messages.
- `Instance::setPresence()`.
- `Chat`: `markChatUnread()`, `updateMessage()`, `fetchProfilePictureUrl()`,
  `findStatusMessage()`, `getBase64FromMediaMessage()`, `updateBlockStatus()`.
- `Group`: `updateGroupPicture()`, `revokeInviteCode()`, `inviteInfo()`, `sendInvite()`,
  `toggleEphemeral()`, `updateSetting()`.
- Developer tooling: `AGENTS.md` + `CLAUDE.md` guides, `.claude/`, `.opencode/` and
  `.antigravity/` agent configurations, and a Laravel Boost skill at
  `resources/boost/skills/laravel-evolution-client/SKILL.md`.

### Changed
- **Tooling:** replaced `friendsofphp/php-cs-fixer` with **Laravel Pint** (`pint.json`) and
  added **Larastan level 5** (`phpstan.neon.dist`). CI workflows updated accordingly
  (`fix-php-code-style-issues.yml`, `phpstan.yml`).
- Aligned several methods with the Evolution API v2 spec (fixes requests that previously
  hit non-existent v1-style paths). Note the following signature changes:
  - `Chat::markAsRead(array $readMessages)` (was a single string).
  - `Chat::archive(string $chat, array $lastMessageKey)` /
    `Chat::unarchive(string $chat, array $lastMessageKey)`.
  - `Instance::createInstance()` now sends the required `integration`
    (default `WHATSAPP-BAILEYS`) plus optional `token`/`number`/`qrcode`.
  - `Instance::setWebhook()` body remapped (`base64` → `webhookBase64`, adds
    `webhookByEvents`).

### Fixed
- `Instance::restart()` now uses `PUT` (was `POST`).
- `Instance::isConnected()`/`getStatus()` use `GET /instance/connectionState/{instance}`
  (the previous `/instance/status` endpoint does not exist).
- `Message::sendLocation()`, `Chat::markAsRead()`, `Chat::archive()`/`unarchive()`,
  `Chat::startTyping()`/`stopTyping()`, and several `Group` methods (`create`, `all`,
  `find`, `updateSubject`, `updateDescription`, `getInviteCode`, `joinWithInviteCode`,
  `leave`, `updateParticipant`) corrected to the proper v2 verbs, paths and request bodies.
- Guzzle error handling no longer assumes `hasResponse()` on the base `GuzzleException`.
- Replaced the deprecated `React\EventLoop\Factory::create()` with `Loop::get()`.

## [2.0.0] - 2026-06-22

### Added
- Added `Business` resource supporting `getCatalog` and `getCollections` endpoints.
- Added `connectionState()`, `getWebhook()`, and `fetchInstances()` methods to `Instance` resource.
- Added `findChats()`, `findMessages()`, `findContacts()`, and `whatsappNumbers()` to `Chat` resource.
- Added `getParticipants()` and a unified `updateParticipant()` method (handling add/remove/promote/demote actions) to `Group` resource.
- Added `edit()` and `delete()` methods to `Template` resource.
- Exposed the missing `getInstanceName()` and `setInstanceName()` helper methods to the `OpenAIBot` resource.
- Fully registered `Business`, `OpenAIBot`, and `EvolutionBot` resources within `EvolutionApiClient` and the `Evolution` Facade.

### Changed
- Updated `setWebhook()` in `Instance` resource to use the new `/webhook/set/{instanceName}` v2 endpoint format.

## [1.12.2] - 2025-08-22

### Fixed
- Fix connect

## [1.12.1] - 2025-07-24

### Fixed
- Fix Get Qr

## [1.12.0] - 2025-07-22

### Added
- Laravel 12 Support

## [1.0.0] - 2025-04-11

### Added
- Initial package version
- Support for instance management
- Sending text messages, images, documents, location, and contacts
- Chat and group management
- Webhooks for events
