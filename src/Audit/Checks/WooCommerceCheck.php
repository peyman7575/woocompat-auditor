<?php
/**
 * WooCommerce runtime checks.
 *
 * @package WooCompatAuditor
 */

namespace WooCompatAuditor\Audit\Checks;

use WooCompatAuditor\Audit\AuditResult;

final class WooCommerceCheck {
	/**
	 * Run checks.
	 *
	 * @return AuditResult[]
	 */
	public function run() {
		$results = array();

		if ( ! defined( 'WC_VERSION' ) || ! class_exists( 'WooCommerce' ) ) {
			$results[] = new AuditResult(
				'woocommerce',
				'WooCommerce',
				AuditResult::FAIL,
				'WooCommerce is not active. WooCommerce-specific checks are limited.'
			);

			return $results;
		}

		$results[] = new AuditResult(
			'woocommerce',
			'WooCommerce',
			AuditResult::PASS,
			'WooCommerce is active and available to the auditor.',
			array( 'version' => WC_VERSION )
		);

		$action_scheduler_available = function_exists( 'as_next_scheduled_action' ) || class_exists( 'ActionScheduler' );
		$results[]                  = new AuditResult(
			'action-scheduler',
			'Action Scheduler',
			$action_scheduler_available ? AuditResult::PASS : AuditResult::WARNING,
			$action_scheduler_available ? 'Action Scheduler is available.' : 'Action Scheduler could not be detected. Background WooCommerce jobs may need investigation.'
		);

		$wc_session_ready = function_exists( 'WC' ) && WC() && isset( WC()->session );
		$results[]        = new AuditResult(
			'wc-session',
			'WooCommerce session layer',
			$wc_session_ready ? AuditResult::PASS : AuditResult::INFO,
			$wc_session_ready ? 'WooCommerce session handling is initialized for this request.' : 'WooCommerce session handling is not initialized for this admin request; this can be normal.'
		);

		return $results;
	}
}
