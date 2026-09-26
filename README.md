# WooCompat Auditor

[![CI](https://github.com/peyman7575/woocompat-auditor/actions/workflows/ci.yml/badge.svg)](https://github.com/peyman7575/woocompat-auditor/actions/workflows/ci.yml)
[![License: GPL-2.0-or-later](https://img.shields.io/badge/License-GPL--2.0--or--later-blue.svg)](LICENSE)

**WooCompat Auditor** is a developer-focused, read-only compatibility and production-readiness auditor for WooCommerce sites.

It turns common pre-release and troubleshooting checks into a repeatable report that can be reviewed in wp-admin, exported as JSON, or generated with WP-CLI.

> Status: early development (`0.2.x`). The project is usable today, while the audit catalog and integration coverage are intentionally still growing.

## Why this project exists

WooCommerce stores combine WordPress core, WooCommerce, extensions, themes, template overrides, background jobs, order-storage modes, and server configuration. Compatibility problems often surface only after one of those layers changes.

WooCompat Auditor provides a neutral diagnostic snapshot. It does **not** modify site configuration and it does **not** claim that every warning is a defect.

## Current checks

- PHP and WordPress baseline versions
- WooCommerce availability and version
- High-Performance Order Storage (HPOS) status
- Active WooCommerce extension declarations for HPOS and Cart/Checkout Blocks, distinguishing explicit incompatibility from missing declarations
- Action Scheduler availability
- WooCommerce session initialization context
- Production debug configuration
- WordPress memory limit
- HTTPS on the current request
- Persistent object cache status
- Theme-level WooCommerce template overrides, including outdated `@version` headers

## Interfaces

### wp-admin

After activation, open:

**WooCommerce → Compatibility Auditor**

If WooCommerce is unavailable, the screen falls back under **Tools**.

The screen provides a status summary, technical context for each check, and a JSON export.

### WP-CLI

```bash
wp woocompat audit
wp woocompat audit --format=json
```

## Installation

1. Download a release ZIP or clone the repository.
2. Place the plugin at `wp-content/plugins/woocompat-auditor`.
3. Activate **WooCompat Auditor**.
4. Open **WooCommerce → Compatibility Auditor**.

Development clone:

```bash
git clone https://github.com/peyman7575/woocompat-auditor.git
cd woocompat-auditor
composer install
```

## Privacy and safety

WooCompat Auditor is read-only in the `0.2.x` release line.

- It does not change WooCommerce settings.
- It does not submit reports to an external service.
- JSON exports are generated locally for an authorized WooCommerce manager.
- Reports avoid absolute filesystem paths and are designed not to include credentials or secrets.

Before publishing an exported report, review it like any other diagnostic artifact.

## Requirements

- WordPress 6.6+
- PHP 7.4+
- WooCommerce for the full audit catalog

## Development

Run the complete local quality gate:

```bash
composer install
composer check
```

Or run checks separately:

```bash
composer lint
composer test
composer verify-version
```

The repository CI validates PHP syntax across PHP 7.4–8.4, runs PHPUnit across representative supported PHP versions, enforces WordPress Coding Standards, validates package metadata, and smoke-tests an installable plugin ZIP.

## Build an installable ZIP

```bash
bash scripts/build-zip.sh
```

The package is written to `dist/` with a single `woocompat-auditor/` root directory and excludes development-only files.

## Release process

The recommended release flow is PR-driven:

1. Create a branch named `release/X.Y.Z`.
2. Update the plugin header version, `WOOCOMPAT_AUDITOR_VERSION`, `readme.txt` stable tag, changelog, and optional `docs/releases/X.Y.Z.md` release notes.
3. Run `composer check` and open a pull request.
4. Merge the release PR only after CI is green.
5. The Release workflow re-runs the quality gate, builds the installable ZIP, creates `vX.Y.Z` when needed, and publishes the GitHub Release.

A manually pushed `vX.Y.Z` tag is also supported. The workflow verifies that the tag matches the plugin version before publishing.

Version metadata is checked automatically to prevent mismatched release packages.

## Roadmap

Near-term work includes:

- broader WooCommerce feature coverage beyond HPOS and Cart/Checkout Blocks
- safer, richer template override diagnostics
- WordPress/WooCommerce integration tests
- machine-readable check metadata for CI pipelines
- optional redacted support bundles
- extensibility API for third-party checks

See the GitHub issues for active scoped work.

## Contributing

Contributions are welcome. Please read [CONTRIBUTING.md](CONTRIBUTING.md) before opening a pull request.

For security-sensitive reports, follow [SECURITY.md](SECURITY.md) instead of opening a public issue.

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).
