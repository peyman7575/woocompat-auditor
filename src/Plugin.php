<?php
/**
 * Plugin bootstrap.
 *
 * @package WooCompatAuditor
 */

namespace WooCompatAuditor;

use WooCompatAuditor\Admin\AdminPage;
use WooCompatAuditor\Admin\ExportController;
use WooCompatAuditor\Audit\AuditRunner;
use WooCompatAuditor\Cli\AuditCommand;

final class Plugin {
	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static $instance;

	/**
	 * Get singleton instance.
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Boot the plugin.
	 *
	 * @return void
	 */
	public function boot() {
		$runner = new AuditRunner();

		if ( is_admin() ) {
			( new AdminPage( $runner ) )->register();
			( new ExportController( $runner ) )->register();
		}

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			AuditCommand::register( $runner );
		}
	}
}
