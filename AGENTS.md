# AGENTS.md

Guidance for AI coding agents (Claude Code, opencode, Antigravity, Cursor, …) working
on **`happones/laravel-evolution-client`** — a Laravel HTTP client for the
[Evolution API v2](https://doc.evolution-api.com/) (WhatsApp integration).

This file is the single source of truth. Tool-specific files
(`CLAUDE.md`, `.claude/`, `.opencode/`, `.antigravity/`) point back here.

---

## 1. What this package is

A framework package (not an application). It wraps the Evolution API v2 REST +
WebSocket surface behind a fluent, Laravel-idiomatic client. It follows the
conventions popularised by [Spatie's Laravel packages](https://spatie.be/open-source):
a service provider, a publishable config, a facade, PSR-4 `src/`, and a fully
mocked test suite.

- **PHP:** `^8.3` (CI runs 8.3, 8.4, 8.5)
- **Laravel:** `^11.0|^12.0|^13.0` (`illuminate/*`), tested via `orchestra/testbench ^9|^10|^11`
  (CI matrix: L11→testbench 9, L12→10, L13→11; Pest `^3.8|^4.0`)
- **Namespace:** `Happones\LaravelEvolutionClient\` → `src/`
- **Tests namespace:** `Happones\LaravelEvolutionClient\Tests\` → `tests/`

---

## 2. Architecture

```
config/evolution.php          Publishable config (tag: evolution-config)
src/
├── EvolutionServiceProvider.php   Registers the `evolution` singleton + config
├── EvolutionApiClient.php         Facade root: exposes all Resources as public props
├── Facades/Evolution.php          Laravel facade (accessor: `evolution`)
├── Services/
│   ├── EvolutionService.php       Guzzle wrapper: get/post/put/delete + error handling
│   └── WebSocketClient.php        ratchet/pawl WebSocket client
├── Resources/                     One class per API domain (Message, Chat, Group, …)
├── Models/                        Plain DTOs / value objects for request payloads
└── Exceptions/EvolutionApiException.php
```

Data flow:

```
Facade (Evolution::) ──▶ EvolutionApiClient ──▶ Resources\* ──▶ EvolutionService ──▶ Guzzle ──▶ Evolution API
```

Key rules of the design:

- **Resources** hold no HTTP logic. They build endpoint paths + payloads and delegate
  to `EvolutionService::{get,post,put,delete}()`, which always returns `array`.
- Every Resource takes `(EvolutionService $service, string $instanceName)` and exposes
  `getInstanceName()` / `setInstanceName()`.
- `EvolutionApiClient::instance($name)` returns a **new** client (it never mutates `$this`):
  the container singleton is shared across queue jobs and Octane requests, so it must stay
  stateless. When you add a Resource you MUST register it (see §4).
- Phone numbers go to the API as bare digits (`formatPhoneNumber()`); values containing
  `@` are passed through as JIDs. Never append `@c.us` (v1 / whatsapp-web.js convention).
- **Models** are dumb payload builders (DTOs). They validate/shape data and usually
  expose `toArray()`. No HTTP, no framework coupling.
- Errors from the API surface as `EvolutionApiException` — never leak `GuzzleException`.

---

## 3. Conventions (match the existing code exactly)

- Match surrounding style: property/param PHPDoc blocks are present throughout — keep them.
- Endpoint paths are built inline, e.g. `"message/sendText/{$this->instanceName}"`.
- Public methods return `array` for API calls (the decoded JSON body).
- Throw `EvolutionApiException` for API-level failures; `InvalidArgumentException`
  for bad caller input (see `Resources/Message.php` for the pattern).
- Prefer typed arguments with sane defaults over associative option arrays, matching
  the existing `sendText(string $phoneNumber, string $message, bool $isGroup = false, ...)`.
- English only for all code, identifiers, comments and PHPDoc.
- No `declare(strict_types=1)` is used in this codebase — do not add it in unrelated files.

---

## 4. How to add a new endpoint / Resource

**Adding a method to an existing Resource** (most common):

1. Add a typed public method to the relevant `src/Resources/*.php`.
2. Build the path with `{$this->instanceName}` and call the matching
   `$this->service->{verb}()`.
3. Validate caller input up front; throw `InvalidArgumentException` when invalid.
4. Add PHPDoc (`@param`, `@throws EvolutionApiException`, `@return array`).
5. Add a unit test in `tests/Unit/Resources/{Resource}ResourceTest.php`.

**Adding a brand-new Resource** — register it in both places below:

1. Create `src/Resources/Foo.php` following an existing Resource.
2. In `src/EvolutionApiClient.php`: add the `public Foo $foo;` property and construct it in
   `__construct()` (`instance()` builds a new client, so nothing else is needed).
3. In `src/Facades/Evolution.php`: add the `@method` PHPDoc line.
4. Create `tests/Unit/Resources/FooResourceTest.php`.
5. Update `README.md` (Usage) and `CHANGELOG.md` (Unreleased → Added).

Cross-check new endpoints, verbs, paths and required fields against the
[Evolution API v2 OpenAPI spec](https://github.com/evolution-foundation/docs-evolution/blob/main/openapi/openapi-v2.json).

---

## 5. Testing

- Framework: **Pest 3/4** on top of PHPUnit 11/12, with **Orchestra Testbench**. Mark
  PHPUnit-style test methods with `#[Test]` — PHPUnit 12 ignores `/** @test */` docblocks.
- HTTP is never hit for real: tests inject a Guzzle `MockHandler`. See
  `tests/TestCase.php` (`addMockResponse()`, `createMockService()`) and
  `tests/Unit/Resources/MessageResourceTest.php` for the canonical pattern.
- Resource tests build a `Message`/`Foo` resource against a mocked `EvolutionService`
  and assert the returned array + that arguments are shaped correctly.
- Name tests descriptively (`it_can_send_text_message`).
- Every new public method needs at least one happy-path test; add error-path tests
  when the method validates input or can throw.

Run: `composer test` (all), or `vendor/bin/pest --filter=Foo`.

---

## 6. Tooling — code style & static analysis

This package uses the modern Laravel standard toolchain.

| Concern        | Tool                    | Config                | Command             |
|----------------|-------------------------|-----------------------|---------------------|
| Code style     | **Laravel Pint**        | `pint.json`           | `composer format`   |
| Style check    | Laravel Pint (dry-run)  | `pint.json`           | `composer lint`     |
| Static analysis| **Larastan (level 5)**  | `phpstan.neon.dist`   | `composer analyse`  |
| Tests          | Pest / Testbench        | `phpunit.xml.dist`    | `composer test`     |

- Pint uses the `laravel` preset (see `pint.json`). Run `composer format` before committing;
  do **not** hand-format.
- Larastan runs at **level 5**. New code must pass `composer analyse` with no new errors.
  Fix types properly — avoid blanket `ignoreErrors`.
- `friendsofphp/php-cs-fixer` has been fully removed; do not reintroduce it.

---

## 7. Release & versioning workflow

This project follows [Semantic Versioning](https://semver.org) and
[Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

**Before every release / tag, all three gates MUST pass locally and in CI:**

```bash
composer test        # Pest test suite (green)
composer lint        # Pint --test (no style diffs)
composer analyse     # Larastan level 5 (no errors)
```

Release checklist:

1. Ensure the branch is `main`, clean, and the three gates above pass.
2. Move entries from `## [Unreleased]` in `CHANGELOG.md` into a new
   `## [X.Y.Z] - YYYY-MM-DD` section. Keep the `### Added / Changed / Fixed / Removed`
   grouping. Leave a fresh empty `## [Unreleased]` on top.
3. Decide the bump per SemVer:
   - **MAJOR** — a breaking change to the public API (Resource method signatures,
     Facade methods, config keys, removed features).
   - **MINOR** — new endpoints/Resources/methods, backward compatible.
   - **PATCH** — bug fixes, no API change.
4. Commit: `git commit -am "release: vX.Y.Z"`.
5. Tag: `git tag vX.Y.Z && git push origin main --tags`.
   Packagist picks up the tag automatically.

Never tag with failing tests, style diffs, or Larastan errors, and never edit an
already-published tag — cut a new patch instead.

---

## 8. Command quick reference

```bash
composer test         # run the Pest suite
composer test-coverage# run with coverage
composer format       # apply Pint fixes
composer lint         # Pint dry-run (CI-style check)
composer analyse      # Larastan level 5
```

---

## 9. Do / Don't

**Do**
- Keep HTTP logic in `EvolutionService`; keep Resources thin.
- Register new Resources in all three places (§4).
- Add/adjust tests and CHANGELOG with every behavioural change.
- Verify endpoints/paths/fields against the OpenAPI v2 spec.

**Don't**
- Don't call Guzzle directly from a Resource.
- Don't add real network calls in tests — always mock.
- Don't reintroduce php-cs-fixer or hand-format around Pint.
- Don't leak `GuzzleException`; wrap failures in `EvolutionApiException`.
- Don't tag a release with red gates or an unmaintained CHANGELOG.
