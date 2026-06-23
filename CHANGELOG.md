# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
