# WooCompat Auditor

[![CI](https://github.com/peyman7575/woocompat-auditor/actions/workflows/ci.yml/badge.svg)](https://github.com/peyman7575/woocompat-auditor/actions/workflows/ci.yml)
[![License: GPL-2.0-or-later](https://img.shields.io/badge/License-GPL--2.0--or--later-blue.svg)](LICENSE)

**WooCompat Auditor** is a developer-focused, read-only compatibility and production-readiness auditor for WooCommerce sites.

It turns common pre-release and troubleshooting checks into a repeatable report that can be reviewed in wp-admin, exported as JSON, or generated with WP-CLI.

> Status: early development (\`0.1.0\`). The project is usable, while the audit catalog and automated test coverage are intentionally still growing.

## Why this project exists

WooCommerce stores combine WordPress core, WooCommerce, extensions, themes, template overrides, background jobs, order-storage modes, and server configuration. Compatibility issues often surface only after one of those layers changes.

WooCompat Auditor provides a neutral diagnostic snapshot. It does **not** modify site configuration and it does **not** claim that every warning is a defect.

## Current checks

- PHP and WordPress baseline versions
- WooCommerce availability and version
- High-Performance Order Storage (HPOS) status
- Action Scheduler availability
- WooCommerce session initialization context
- Production debug configuration
- WordPress memory limit
- HTTPS on the current request
- Persistent object cache status
- Theme-level WooCommerce template overrides, including outdated \`@version\` headers

## Interfaces

### wp-admin

After activation, open:

**WooCommerce → Compatibility Auditor**

If WooCommerce is unavailable, the screen falls back under **Tools**.

The screen provides a status summary, technical context for each check, and a JSON export.

### WP-CLI

\`\`\`bash
wp woocompat audit
wp woocompat audit --format=json
\`\`\`

## Installation

1. Download or clone the repository.
2. Place it at \`wp-content/plugins/woocompat-auditor\`.
3. Activate **WooCompat Auditor**.
4. Open **WooCommerce → Compatibility Auditor**.

Development clone:

\`\`\`bash
git clone https://github.com/peyman7575/woocompat-auditor.git
\`\`\`

## Privacy and safety

WooCompat Auditor is read-only in \`0.1.x\`.

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

PHP syntax check:

\`\`\`bash
find . -name '*.php' -not -path './vendor/*' -print0 | xargs -0 -n1 php -l
\`\`\`

Install optional development tooling:

\`\`\`bash
composer install
composer lint
\`\`\`

## Roadmap

Near-term work includes:

- broader plugin compatibility declarations and feature detection
- safer, richer template override diagnostics
- automated WordPress/WooCommerce integration tests
- machine-readable check metadata for CI pipelines
- optional redacted support bundles
- extensibility API for third-party checks

See the GitHub issues for active scoped work.

## Contributing

Contributions are welcome. Please read [CONTRIBUTING.md](CONTRIBUTING.md) before opening a pull request.

For security-sensitive reports, follow [SECURITY.md](SECURITY.md) instead of opening a public issue.

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).
