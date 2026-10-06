# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project adheres to
[Semantic Versioning](https://semver.org/spec/v2.0.0.html). Until 1.0.0, minor versions may contain breaking
changes.

## [Unreleased]

## [0.2.1] - 2026-10-06

### Fixed

- A theme that renders `form.spam_protection` itself before calling the form template event no longer fails
  with "Field "spam_protection" has already been rendered": the plugin skips a field that is already rendered.

## [0.2.0] - 2026-10-06

### Removed

- Support for Sylius 1.12 and 1.13. They are no longer maintained, and their dependencies have security
  advisories that will never be fixed. Version 0.1 of the plugin remains available for them.

## [0.1.0] - 2026-10-06

### Added

- Initial release: protects the Sylius shop contact, registration and password reset request forms, rendered
  through the shop template events. Supports Sylius 1.12 to 1.14 and requires
  florentingarnier/spam-protection-bundle 0.2.

[Unreleased]: https://github.com/FlorentinGarnier/sylius-spam-protection-plugin/compare/v0.2.1...HEAD
[0.2.1]: https://github.com/FlorentinGarnier/sylius-spam-protection-plugin/compare/v0.2.0...v0.2.1
[0.2.0]: https://github.com/FlorentinGarnier/sylius-spam-protection-plugin/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/FlorentinGarnier/sylius-spam-protection-plugin/releases/tag/v0.1.0
