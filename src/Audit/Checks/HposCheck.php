<?php
/**
 * HPOS checks.
 *
 * @package WooCompatAuditor
 */

namespace WooCompatAuditor\Audit\Checks;

use WooCompatAuditor\Audit\AuditResult;

final class HposCheck {
	/**
	 * Run checks.
	 *
	 * @return AuditResult[]
	 */
	public function run() {
		$order_util = '\\Automattic\\WooCommerce\\Utilities\\OrderUtil';

		if ( ! class_exists( $order_util ) || ! is_callable( array( $order_util, 'custom_orders_table_usage_is_enabled' ) ) ) {
			return array(
				new AuditResult(
					'hpos',
					'High-Performance Order Storage',
					AuditResult::INFO,
					'HPOS status is unavailable in this WooCommerce version.'
				),
			);
		}

		$enabled = (bool) call_user_func( array( $order_util, 'custom_orders_table_usage_is_enabled' ) );

		return array(
			new AuditResult(
				'hpos',
				'High-Performance Order Storage',
				AuditResult::INFO,
				$enabled ? 'HPOS is enabled for this store.' : 'HPOS is currently not enabled for this store.',
				array( 'enabled' => $enabled )
			),
		);
	}
}
