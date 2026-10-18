# Workspace rules — laravel-evolution-client

Rules for the Antigravity agent on this repo. The full guide lives in
[`AGENTS.md`](../../AGENTS.md) at the repo root (Antigravity reads it as project context);
this file is the short, always-on rule set.

## Project

Spatie-style Laravel HTTP client for the **Evolution API v2** (WhatsApp).
Namespace `Happones\LaravelEvolutionClient\` → `src/`. **PHP 8.3+ / Laravel 11–13.**

## Always

- Keep Resources thin: build the path with `{$this->instanceName}` and delegate to
  `EvolutionService::{get,post,put,delete}()` (returns `array`).
- Validate input → throw `InvalidArgumentException`; let `EvolutionApiException` surface
  for API failures.
- New Resource → register it in the client (property + constructor) and the `Evolution`
  facade `@method` PHPDoc. See `AGENTS.md` §4.
- Add a Pest test (mocked Guzzle, no network) for every new/changed public method.
- Verify every verb/path/body against the Evolution API v2 OpenAPI spec.
- Match existing PHPDoc/formatting; English only; no `declare(strict_types=1)`.

## Never

- Never call Guzzle directly from a Resource, or leak `GuzzleException`.
- Never hand-format around Pint, or reintroduce php-cs-fixer.
- Never tag a release with red gates, or force-push / rewrite a published tag
  (public on Packagist — duplicate tags error out).

## Gates (run before commit/release)

```bash
composer test      # Pest
composer lint      # Pint --test
composer analyse   # Larastan level 5
```
