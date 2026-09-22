=== WooCompat Auditor ===
Contributors: peyman7575
Tags: woocommerce, compatibility, diagnostics, hpos, developer-tools
Requires at least: 6.6
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Developer-focused, read-only compatibility and production-readiness auditing for WooCommerce sites.

== Description ==

WooCompat Auditor provides a repeatable diagnostic snapshot for WooCommerce developers and maintainers.

Version 0.1.0 checks the WordPress/PHP environment, WooCommerce runtime availability, HPOS status, Action Scheduler, debug configuration, memory limits, HTTPS, persistent object cache usage, and WooCommerce template overrides.

Reports are available in wp-admin, as a local JSON download, and through WP-CLI.

The plugin is read-only and does not automatically change store configuration.

== Installation ==

1. Upload the plugin directory to /wp-content/plugins/.
2. Activate WooCompat Auditor.
3. Open WooCommerce > Compatibility Auditor.

== Changelog ==

= 0.1.0 =
* Initial public release.
* Added read-only environment and WooCommerce checks.
* Added HPOS status detection.
* Added theme-level template override version checks.
* Added JSON export and WP-CLI output.
