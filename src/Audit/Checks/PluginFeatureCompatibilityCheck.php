<?php
/**
 * WooCommerce extension feature-compatibility declaration checks.
 *
 * @package WooCompatAuditor
 */

namespace WooCompatAuditor\Audit\Checks;

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use Automattic\WooCommerce\Utilities\PluginUtil;
use Throwable;
use WooCompatAuditor\Audit\AuditResult;

/**
 * Audits active WooCommerce extensions for explicit feature compatibility declarations.
 */
final class PluginFeatureCompatibilityCheck {
	/**
	 * Optional normalized snapshot used by unit tests.
	 *
	 * @var array<string,mixed>|null
	 */
	private $snapshot;

	/**
	 * Constructor.
	 *
	 * @param array<string,mixed>|null $snapshot Optional normalized feature snapshot.
	 */
	public function __construct( $snapshot = null ) {
		$this->snapshot = is_array( $snapshot ) ? $snapshot : null;
	}

	/**
	 * Run checks.
	 *
	 * @return AuditResult[]
	 */
	public function run() {
		$snapshot = $this->snapshot;

		if ( null === $snapshot ) {
			$snapshot = $this->collect_snapshot();
		}

		if ( isset( $snapshot['unavailable'] ) ) {
			return array(
				new AuditResult(
					'plugin-feature-compatibility',
					'Extension feature compatibility declarations',
					AuditResult::INFO,
					(string) $snapshot['unavailable']
				),
			);
		}

		$features = isset( $snapshot['features'] ) && is_array( $snapshot['features'] )
			? $snapshot['features']
			: array();

		$results = array();

		foreach ( $features as $feature ) {
			if ( ! is_array( $feature ) || empty( $feature['id'] ) || empty( $feature['label'] ) ) {
				continue;
			}

			$compatible   = $this->normalize_plugin_rows( isset( $feature['compatible'] ) ? $feature['compatible'] : array() );
			$incompatible = $this->normalize_plugin_rows( isset( $feature['incompatible'] ) ? $feature['incompatible'] : array() );
			$undeclared   = $this->normalize_plugin_rows( isset( $feature['undeclared'] ) ? $feature['undeclared'] : array() );

			if ( ! empty( $incompatible ) ) {
				$status  = AuditResult::WARNING;
				$message = sprintf(
					'%1$d active extension(s) explicitly declare incompatibility with %2$s.',
					count( $incompatible ),
					$feature['label']
				);
			} elseif ( ! empty( $undeclared ) ) {
				$status  = AuditResult::INFO;
				$message = sprintf(
					'%1$d active WooCommerce-aware extension(s) have no compatibility declaration for %2$s. Missing declarations are not treated as known incompatibility.',
					count( $undeclared ),
					$feature['label']
				);
			} elseif ( ! empty( $compatible ) ) {
				$status  = AuditResult::PASS;
				$message = sprintf(
					'All %1$d detected active extension(s) explicitly declare compatibility with %2$s.',
					count( $compatible ),
					$feature['label']
				);
			} else {
				$status  = AuditResult::INFO;
				$message = sprintf(
					'No third-party active WooCommerce-aware extensions were detected for %s.',
					$feature['label']
				);
			}

			$results[] = new AuditResult(
				'plugin-feature-' . str_replace( '_', '-', $feature['id'] ),
				$feature['label'] . ' extension compatibility',
				$status,
				$message,
				array(
					'feature_id'   => (string) $feature['id'],
					'enabled'      => isset( $feature['enabled'] ) ? (bool) $feature['enabled'] : null,
					'compatible'   => $compatible,
					'incompatible' => $incompatible,
					'undeclared'   => $undeclared,
				)
			);
		}

		return $results;
	}

	/**
	 * Collect a normalized snapshot from WooCommerce public utility APIs.
	 *
	 * @return array<string,mixed>
	 */
	private function collect_snapshot() {
		if (
			! class_exists( FeaturesUtil::class )
			|| ! class_exists( PluginUtil::class )
			|| ! function_exists( 'wc_get_container' )
		) {
			return array(
				'unavailable' => 'WooCommerce feature compatibility APIs are unavailable in this environment.',
			);
		}

		if ( function_exists( 'did_action' ) && 1 > did_action( 'woocommerce_init' ) ) {
			return array(
				'unavailable' => 'WooCommerce feature compatibility declarations are not available before woocommerce_init.',
			);
		}

		try {
			$plugin_util = wc_get_container()->get( PluginUtil::class );
		} catch ( Throwable $throwable ) {
			unset( $throwable );

			return array(
				'unavailable' => 'WooCommerce plugin compatibility services could not be initialized.',
			);
		}

		if ( ! $plugin_util instanceof PluginUtil ) {
			return array(
				'unavailable' => 'WooCommerce plugin compatibility services could not be initialized.',
			);
		}

		$active_plugins = array_values( array_unique( $plugin_util->get_all_active_valid_plugins() ) );
		$aware_plugins  = array_values( array_unique( $plugin_util->get_woocommerce_aware_plugins( true ) ) );
		$plugin_data    = $this->load_plugin_metadata();

		$self_plugin = function_exists( 'plugin_basename' ) && defined( 'WOOCOMPAT_AUDITOR_FILE' )
			? plugin_basename( WOOCOMPAT_AUDITOR_FILE )
			: 'woocompat-auditor/woocompat-auditor.php';

		$excluded = array(
			'woocommerce/woocommerce.php',
			$self_plugin,
		);

		$aware_plugins = array_values( array_diff( array_intersect( $aware_plugins, $active_plugins ), $excluded ) );

		$definitions = array(
			array(
				'id'    => 'custom_order_tables',
				'label' => 'High-Performance Order Storage (HPOS)',
			),
			array(
				'id'    => 'cart_checkout_blocks',
				'label' => 'Cart and Checkout Blocks',
			),
		);

		$features = array();

		foreach ( $definitions as $definition ) {
			try {
				$declarations = FeaturesUtil::get_compatible_plugins_for_feature( $definition['id'] );
				$enabled      = FeaturesUtil::feature_is_enabled( $definition['id'] );
			} catch ( Throwable $throwable ) {
				unset( $throwable );

				continue;
			}

			$declared_compatible = $this->normalize_plugin_ids(
				isset( $declarations['compatible'] ) ? $declarations['compatible'] : array()
			);
			$declared_incompatible = $this->normalize_plugin_ids(
				isset( $declarations['incompatible'] ) ? $declarations['incompatible'] : array()
			);

			$compatible   = array_values( array_diff( array_intersect( $declared_compatible, $active_plugins ), $excluded ) );
			$incompatible = array_values( array_diff( array_intersect( $declared_incompatible, $active_plugins ), $excluded ) );
			$candidates   = array_values( array_unique( array_merge( $aware_plugins, $compatible, $incompatible ) ) );
			$undeclared   = array_values( array_diff( $candidates, $compatible, $incompatible ) );

			sort( $compatible );
			sort( $incompatible );
			sort( $undeclared );

			$features[] = array(
				'id'           => $definition['id'],
				'label'        => $definition['label'],
				'enabled'      => (bool) $enabled,
				'compatible'   => $this->build_plugin_rows( $compatible, $plugin_data ),
				'incompatible' => $this->build_plugin_rows( $incompatible, $plugin_data ),
				'undeclared'   => $this->build_plugin_rows( $undeclared, $plugin_data ),
			);
		}

		if ( empty( $features ) ) {
			return array(
				'unavailable' => 'WooCommerce feature compatibility declarations could not be read for the supported features.',
			);
		}

		return array( 'features' => $features );
	}

	/**
	 * Load WordPress plugin metadata when the plugin API is available.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	private function load_plugin_metadata() {
		if ( ! function_exists( 'get_plugins' ) && defined( 'ABSPATH' ) ) {
			$plugin_api = ABSPATH . 'wp-admin/includes/plugin.php';

			if ( is_readable( $plugin_api ) ) {
				require_once $plugin_api;
			}
		}

		return function_exists( 'get_plugins' ) ? get_plugins() : array();
	}

	/**
	 * Normalize plugin ids returned by WooCommerce.
	 *
	 * @param mixed $plugins Plugin ids.
	 * @return string[]
	 */
	private function normalize_plugin_ids( $plugins ) {
		if ( ! is_array( $plugins ) ) {
			return array();
		}

		$normalized = array();

		foreach ( $plugins as $plugin ) {
			if ( is_string( $plugin ) && '' !== $plugin ) {
				$normalized[] = $plugin;
			}
		}

		return array_values( array_unique( $normalized ) );
	}

	/**
	 * Build export-safe plugin rows.
	 *
	 * @param string[]                          $plugins     Plugin basenames.
	 * @param array<string,array<string,mixed>> $plugin_data WordPress plugin metadata.
	 * @return array<int,array<string,string>>
	 */
	private function build_plugin_rows( array $plugins, array $plugin_data ) {
		$rows = array();

		foreach ( $plugins as $plugin ) {
			$version = isset( $plugin_data[ $plugin ]['Version'] )
				? (string) $plugin_data[ $plugin ]['Version']
				: '';

			$rows[] = array(
				'plugin'  => $plugin,
				'version' => $version,
			);
		}

		return $rows;
	}

	/**
	 * Normalize rows supplied to the result builder.
	 *
	 * @param mixed $rows Plugin rows.
	 * @return array<int,array<string,string>>
	 */
	private function normalize_plugin_rows( $rows ) {
		if ( ! is_array( $rows ) ) {
			return array();
		}

		$normalized = array();

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) || empty( $row['plugin'] ) ) {
				continue;
			}

			$normalized[] = array(
				'plugin'  => (string) $row['plugin'],
				'version' => isset( $row['version'] ) ? (string) $row['version'] : '',
			);
		}

		return $normalized;
	}
}
