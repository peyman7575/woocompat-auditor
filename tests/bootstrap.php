<?php
/**
 * PHPUnit bootstrap with the minimum WordPress function surface required by unit tests.
 *
 * @package WooCompatAuditor
 */

if ( ! defined( 'WOOCOMPAT_AUDITOR_VERSION' ) ) {
	define( 'WOOCOMPAT_AUDITOR_VERSION', '0.2.0' );
}

if ( ! defined( 'WOOCOMPAT_AUDITOR_PATH' ) ) {
	define( 'WOOCOMPAT_AUDITOR_PATH', dirname( __DIR__ ) . '/' );
}

if ( ! defined( 'WP_MEMORY_LIMIT' ) ) {
	define( 'WP_MEMORY_LIMIT', '256M' );
}

if ( ! function_exists( 'sanitize_key' ) ) {
	function sanitize_key( $key ) {
		$key = strtolower( (string) $key );
		return preg_replace( '/[^a-z0-9_\\-]/', '', $key );
	}
}

if ( ! function_exists( 'wp_get_environment_type' ) ) {
	function wp_get_environment_type() {
		return 'development';
	}
}

if ( ! function_exists( 'wp_convert_hr_to_bytes' ) ) {
	function wp_convert_hr_to_bytes( $value ) {
		$value = trim( (string) $value );

		if ( '-1' === $value ) {
			return -1;
		}

		$last   = strtolower( substr( $value, -1 ) );
		$number = (int) $value;

		switch ( $last ) {
			case 'g':
				$number *= 1024;
				// Fall through.
			case 'm':
				$number *= 1024;
				// Fall through.
			case 'k':
				$number *= 1024;
		}

		return $number;
	}
}

if ( ! function_exists( 'is_ssl' ) ) {
	function is_ssl() {
		return true;
	}
}

if ( ! function_exists( 'wp_using_ext_object_cache' ) ) {
	function wp_using_ext_object_cache() {
		return false;
	}
}

if ( ! function_exists( 'WC' ) ) {
	function WC() {
		return null;
	}
}

$GLOBALS['wp_version'] = '6.6';

require_once WOOCOMPAT_AUDITOR_PATH . 'src/Autoloader.php';

\WooCompatAuditor\Autoloader::register();
