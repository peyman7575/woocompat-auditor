<?php
/**
 * Tests for the audit runner report contract.
 *
 * @package WooCompatAuditor
 */

namespace WooCompatAuditor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WooCompatAuditor\Audit\AuditRunner;

final class AuditRunnerTest extends TestCase {
	public function test_report_has_a_consistent_machine_readable_contract() {
		$runner = new AuditRunner();
		$report = $runner->report();

		$this->assertSame( WOOCOMPAT_AUDITOR_VERSION, $report['plugin_version'] );
		$this->assertSame( 'development', $report['site_environment'] );
		$this->assertArrayHasKey( 'generated_at_utc', $report );
		$this->assertArrayHasKey( 'summary', $report );
		$this->assertArrayHasKey( 'results', $report );
		$this->assertNotEmpty( $report['results'] );
		$this->assertSame( count( $report['results'] ), array_sum( $report['summary'] ) );
	}

	public function test_report_degrades_safely_without_woocommerce_runtime() {
		$runner  = new AuditRunner();
		$report  = $runner->report();
		$results = array();

		foreach ( $report['results'] as $result ) {
			$results[ $result['id'] ] = $result;
		}

		$this->assertArrayHasKey( 'woocommerce', $results );
		$this->assertSame( 'fail', $results['woocommerce']['status'] );
		$this->assertArrayHasKey( 'hpos', $results );
		$this->assertSame( 'info', $results['hpos']['status'] );
	}

	public function test_default_report_does_not_expose_plugin_absolute_path() {
		$runner = new AuditRunner();
		$json   = json_encode( $runner->report() );

		$this->assertIsString( $json );
		$this->assertStringNotContainsString( WOOCOMPAT_AUDITOR_PATH, $json );
	}
}
