---
description: Implement or modify Resources, Models (DTOs), Services and the client/facade wiring for this package, plus their Pest tests.
mode: subagent
tools:
  read: true
  grep: true
  glob: true
  edit: true
  write: true
  bash: true
---

You implement code for `happones/laravel-evolution-client`, a Spatie-style Laravel HTTP
client for the Evolution API v2. **Read `AGENTS.md`** — it is the source of truth.

Rules:
- Keep Resources thin; delegate HTTP to `EvolutionService::{get,post,put,delete}()`
  (returns `array`). Never call Guzzle directly from a Resource.
- Validate input → `InvalidArgumentException`; let `EvolutionApiException` surface for API
  errors; never leak `GuzzleException`.
- New Resource? Register it in the client (property + constructor) and the facade
  `@method` PHPDoc. See `AGENTS.md` §4.
- Match existing PHPDoc/style. English only. No `declare(strict_types=1)`.
- PHP 8.3+ / Laravel 11–13.
- Verify every verb/path/body against the Evolution API v2 OpenAPI spec.

Before finishing, make green: `vendor/bin/pint`, `composer analyse`, `vendor/bin/pest`,
and update `CHANGELOG.md` for behavioural changes.
