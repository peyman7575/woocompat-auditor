# Changelog

All notable changes to WooCompat Auditor will be documented here.

The project follows semantic versioning while the public API is developed.

## [Unreleased]

### Added

- Active WooCommerce extension compatibility declaration auditing for HPOS and Cart/Checkout Blocks.
- PHPUnit unit-test foundation for audit result and audit report behavior.
- CI quality gates across supported PHP versions.
- Installable ZIP build script and tag-driven release workflow.
- Pull request template and repository editor defaults.
- Automated version metadata consistency check.

### Changed

- Expanded Composer metadata and development scripts.
- Documented the development, packaging, and release workflow.

## [0.1.0] - 2026-09-23

### Added

- Initial read-only compatibility audit runner.
- WordPress, PHP, HTTPS, debug, memory, and object-cache checks.
- WooCommerce and Action Scheduler runtime checks.
- HPOS status detection.
- Theme-level WooCommerce template override version scanning.
- wp-admin report UI with status summary.
- Secure JSON report export.
- `wp woocompat audit` WP-CLI command.
- CI syntax checks and open-source contribution/security documentation.
