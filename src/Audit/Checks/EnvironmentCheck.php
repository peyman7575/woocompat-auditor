<?php
/**
 * WordPress and PHP environment checks.
 *
 * @package WooCompatAuditor
 */

namespace WooCompatAuditor\Audit\Checks;

use WooCompatAuditor\Audit\AuditResult;

final class EnvironmentCheck {
	/**
	 * Run checks.
	 *
	 * @return AuditResult[]
	 */
	public function run() {
		global $wp_version;

		$results     = array();
		$environment = function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'unknown';

		$results[] = new AuditResult(
			'php-version',
			'PHP version',
			version_compare( PHP_VERSION, '7.4', '>=' ) ? AuditResult::PASS : AuditResult::FAIL,
			version_compare( PHP_VERSION, '7.4', '>=' ) ? 'PHP meets the plugin minimum requirement.' : 'PHP 7.4 or newer is required.',
			array( 'version' => PHP_VERSION )
		);

		$results[] = new AuditResult(
			'wordpress-version',
			'WordPress version',
			version_compare( (string) $wp_version, '6.6', '>=' ) ? AuditResult::PASS : AuditResult::WARNING,
			version_compare( (string) $wp_version, '6.6', '>=' ) ? 'WordPress meets the tested baseline.' : 'WordPress is older than the current audit baseline.',
			array( 'version' => (string) $wp_version )
		);

		$debug_enabled = defined( 'WP_DEBUG' ) && WP_DEBUG;
		$debug_status  = ( 'production' === $environment && $debug_enabled ) ? AuditResult::WARNING : AuditResult::PASS;
		$debug_message = $debug_enabled ? 'WP_DEBUG is enabled.' : 'WP_DEBUG is disabled.';

		if ( 'production' !== $environment && $debug_enabled ) {
			$debug_message .= ' This is expected in many non-production environments.';
		}

		$results[] = new AuditResult(
			'wp-debug',
			'WordPress debug mode',
			$debug_status,
			$debug_message,
			array( 'environment' => $environment )
		);

		$display_enabled = defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY;
		$results[]       = new AuditResult(
			'wp-debug-display',
			'Debug display',
			( 'production' === $environment && $display_enabled ) ? AuditResult::WARNING : AuditResult::PASS,
			$display_enabled ? 'WP_DEBUG_DISPLAY is enabled.' : 'WP_DEBUG_DISPLAY is disabled.',
			array( 'environment' => $environment )
		);

		$memory_limit = defined( 'WP_MEMORY_LIMIT' ) ? WP_MEMORY_LIMIT : ini_get( 'memory_limit' );
		$memory_bytes = function_exists( 'wp_convert_hr_to_bytes' ) ? wp_convert_hr_to_bytes( (string) $memory_limit ) : 0;
		$results[]    = new AuditResult(
			'memory-limit',
			'WordPress memory limit',
			( 0 === $memory_bytes || $memory_bytes >= 268435456 ) ? AuditResult::PASS : AuditResult::WARNING,
			( 0 === $memory_bytes || $memory_bytes >= 268435456 ) ? 'Memory limit is suitable for typical WooCommerce workloads.' : 'A memory limit below 256 MB can become restrictive on larger WooCommerce sites.',
			array( 'limit' => (string) $memory_limit )
		);

		$results[] = new AuditResult(
			'https',
			'HTTPS request',
			is_ssl() ? AuditResult::PASS : AuditResult::WARNING,
			is_ssl() ? 'The current admin request is using HTTPS.' : 'The current admin request is not using HTTPS.'
		);

		$results[] = new AuditResult(
			'object-cache',
			'Persistent object cache',
			wp_using_ext_object_cache() ? AuditResult::PASS : AuditResult::INFO,
			wp_using_ext_object_cache() ? 'A persistent object cache is active.' : 'No persistent object cache was detected. This is optional but can help larger stores.'
		);

		return $results;
	}
}
