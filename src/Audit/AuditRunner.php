<?php
/**
 * Coordinates all audit checks.
 *
 * @package WooCompatAuditor
 */

namespace WooCompatAuditor\Audit;

use WooCompatAuditor\Audit\Checks\EnvironmentCheck;
use WooCompatAuditor\Audit\Checks\HposCheck;
use WooCompatAuditor\Audit\Checks\TemplateOverridesCheck;
use WooCompatAuditor\Audit\Checks\WooCommerceCheck;

final class AuditRunner {
	/**
	 * Run all checks.
	 *
	 * @return AuditResult[]
	 */
	public function run() {
		$checks = array(
			new EnvironmentCheck(),
			new WooCommerceCheck(),
			new HposCheck(),
			new TemplateOverridesCheck(),
		);

		$results = array();

		foreach ( $checks as $check ) {
			foreach ( $check->run() as $result ) {
				if ( $result instanceof AuditResult ) {
					$results[] = $result;
				}
			}
		}

		return $results;
	}

	/**
	 * Build a machine-readable report without secrets or absolute paths.
	 *
	 * @return array<string,mixed>
	 */
	public function report() {
		$results = array_map(
			static function ( AuditResult $result ) {
				return $result->to_array();
			},
			$this->run()
		);

		$summary = array(
			'pass'    => 0,
			'warning' => 0,
			'fail'    => 0,
			'info'    => 0,
		);

		foreach ( $results as $result ) {
			if ( isset( $summary[ $result['status'] ] ) ) {
				++$summary[ $result['status'] ];
			}
		}

		return array(
			'generated_at_utc' => gmdate( 'c' ),
			'plugin_version'   => WOOCOMPAT_AUDITOR_VERSION,
			'site_environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'unknown',
			'summary'          => $summary,
			'results'          => $results,
		);
	}
}
