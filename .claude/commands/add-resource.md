---
description: Scaffold a new Evolution API Resource, register it everywhere, and add a test.
argument-hint: <ResourceName>
allowed-tools: Read, Grep, Glob, Edit, Write, Bash
---

Scaffold a new API Resource named **$1** for this package. Follow `AGENTS.md` §4 exactly.

1. Create `src/Resources/$1.php` mirroring an existing Resource (constructor
   `(EvolutionService $service, string $instanceName)`, `getInstanceName()` /
   `setInstanceName()`). Add the endpoint methods requested (verify verb/path/body against
   the Evolution API v2 OpenAPI spec).
2. Register it in `src/EvolutionApiClient.php`: add `public $1 \$${1,};` property (use a
   sensible camelCase property name) and construct it in `__construct()` (`instance()`
   returns a new client, so it needs no change).
3. Add the `@method static ... get$1Attribute()` line to `src/Facades/Evolution.php`.
4. Create `tests/Unit/Resources/$1ResourceTest.php` using the mocked-Guzzle pattern.
5. Update `README.md` usage and `CHANGELOG.md` (Unreleased → Added).
6. Run `vendor/bin/pint`, `composer analyse`, `vendor/bin/pest` and make them green.

If no method list was given, ask which endpoints the Resource should cover.
