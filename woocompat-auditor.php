<?php
/**
 * Plugin Name:       WooCompat Auditor
 * Plugin URI:        https://github.com/peyman7575/woocompat-auditor
 * Description:       Developer-focused compatibility and production-readiness auditing for WooCommerce sites.
 * Version:           0.1.0
 * Requires at least: 6.6
 * Requires PHP:      7.4
 * Author:            Peyman
 * Author URI:        https://github.com/peyman7575
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       woocompat-auditor
 * Domain Path:       /languages
 * Requires Plugins:  woocommerce
 *
 * @package WooCompatAuditor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WOOCOMPAT_AUDITOR_VERSION', '0.1.0' );
define( 'WOOCOMPAT_AUDITOR_FILE', __FILE__ );
define( 'WOOCOMPAT_AUDITOR_PATH', plugin_dir_path( __FILE__ ) );
define( 'WOOCOMPAT_AUDITOR_URL', plugin_dir_url( __FILE__ ) );

require_once WOOCOMPAT_AUDITOR_PATH . 'src/Autoloader.php';

\WooCompatAuditor\Autoloader::register();

add_action(
	'before_woocommerce_init',
	static function () {
		if ( class_exists( '\\Automattic\\WooCommerce\\Utilities\\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
		}
	}
);

add_action(
	'plugins_loaded',
	static function () {
		\WooCompatAuditor\Plugin::instance()->boot();
	}
);
