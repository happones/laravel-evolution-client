# CLAUDE.md

Guidance for [Claude Code](https://docs.claude.com/en/docs/claude-code) working on this repo.

> **Read [`AGENTS.md`](AGENTS.md) first — it is the single source of truth** for
> architecture, conventions, testing, tooling and the release workflow. This file only
> adds Claude-Code-specific notes. Do not duplicate content; update `AGENTS.md` instead.

## TL;DR

`happones/laravel-evolution-client` — a Laravel HTTP client for the Evolution API v2
(WhatsApp). Framework package, Spatie-style. PHP `^8.3`, Laravel `^11.0|^12.0|^13.0`.

## The three gates (run before every commit / release)

```bash
composer test        # Pest suite — must be green
composer lint        # Pint --test — no style diffs
composer analyse     # Larastan level 5 — no errors
```

Use `composer format` to auto-fix style. Never hand-format around Pint.

## Subagents (`.claude/agents/`)

- **laravel-package-writer** — implement/modify Resources, Models, Services + tests.
- **laravel-package-reviewer** — review a diff for convention/correctness before commit.
- **test-writer** — add Pest tests using the mocked-Guzzle pattern.
- **docs-writer** — keep README / CHANGELOG / this guide in sync.

## Slash commands (`.claude/commands/`)

- `/add-resource <Name>` — scaffold a new API Resource + registration + test.
- `/test [filter]` — run the Pest suite.
- `/lint` — run Pint + Larastan.
- `/release <patch|minor|major>` — run the gates, update CHANGELOG, tag & push.

## Hard rules specific to this repo

- When adding a Resource, register it in `EvolutionApiClient` (property + constructor)
  **and** the `Evolution` facade PHPDoc. See `AGENTS.md` §4.
- Verify every endpoint (verb, path, body) against the Evolution API v2 OpenAPI spec.
- Never `git push --force` a tag or rewrite a published tag — the package is public on
  Packagist and a duplicate/rewritten tag breaks releases. Cut a new patch instead.
- Keep everything PHP 8.3+ / Laravel 11–13 compatible.
