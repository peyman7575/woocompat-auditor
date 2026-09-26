<?php
/**
 * Tests for the audit result value object.
 *
 * @package WooCompatAuditor
 */

namespace WooCompatAuditor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WooCompatAuditor\Audit\AuditResult;

final class AuditResultTest extends TestCase {
	public function test_exports_a_stable_machine_readable_shape() {
		$result = new AuditResult(
			'My Check!',
			'Example check',
			AuditResult::PASS,
			'Everything is fine.',
			array( 'version' => '1.2.3' )
		);

		$this->assertSame(
			array(
				'id'      => 'mycheck',
				'label'   => 'Example check',
				'status'  => 'pass',
				'message' => 'Everything is fine.',
				'context' => array( 'version' => '1.2.3' ),
			),
			$result->to_array()
		);
	}

	public function test_invalid_status_falls_back_to_info() {
		$result = new AuditResult( 'example', 'Example', 'unexpected', 'Message' );

		$this->assertSame( AuditResult::INFO, $result->to_array()['status'] );
	}
}
