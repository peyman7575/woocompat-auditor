<?php
/**
 * Secure JSON report export.
 *
 * @package WooCompatAuditor
 */

namespace WooCompatAuditor\Admin;

use WooCompatAuditor\Audit\AuditRunner;

final class ExportController {
	/** @var AuditRunner */
	private $runner;

	/**
	 * Constructor.
	 *
	 * @param AuditRunner $runner Audit runner.
	 */
	public function __construct( AuditRunner $runner ) {
		$this->runner = $runner;
	}

	/**
	 * Register export action.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_post_woocompat_auditor_export', array( $this, 'export' ) );
	}

	/**
	 * Export a JSON report.
	 *
	 * @return void
	 */
	public function export() {
		if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to export this report.', 'woocompat-auditor' ) );
		}

		check_admin_referer( 'woocompat_auditor_export' );

		nocache_headers();
		header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset', 'UTF-8' ) );
		header( 'Content-Disposition: attachment; filename="woocompat-audit-' . gmdate( 'Ymd-His' ) . '.json"' );
		header( 'X-Content-Type-Options: nosniff' );

		echo wp_json_encode( $this->runner->report(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit;
	}
}
