<?php
/**
 * Tests for WooCommerce extension feature-compatibility declaration checks.
 *
 * @package WooCompatAuditor
 */

namespace WooCompatAuditor\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WooCompatAuditor\Audit\Checks\PluginFeatureCompatibilityCheck;

final class PluginFeatureCompatibilityCheckTest extends TestCase {
	/**
	 * Explicit incompatibility must surface as a warning.
	 *
	 * @return void
	 */
	public function test_explicit_incompatibility_is_a_warning() {
		$check   = new PluginFeatureCompatibilityCheck(
			$this->snapshot(
				array(
					array( 'plugin' => 'payments/payments.php', 'version' => '2.4.0' ),
				),
				array(
					array( 'plugin' => 'legacy/legacy.php', 'version' => '1.3.0' ),
				),
				array(
					array( 'plugin' => 'shipping/shipping.php', 'version' => '3.1.0' ),
				)
			)
		);
		$results = $check->run();

		$this->assertCount( 1, $results );
		$result = $results[0]->to_array();

		$this->assertSame( 'warning', $result['status'] );
		$this->assertSame( 'legacy/legacy.php', $result['context']['incompatible'][0]['plugin'] );
		$this->assertSame( '1.3.0', $result['context']['incompatible'][0]['version'] );
	}

	/**
	 * A missing declaration is informational, not a compatibility failure.
	 *
	 * @return void
	 */
	public function test_undeclared_extension_is_informational() {
		$check   = new PluginFeatureCompatibilityCheck(
			$this->snapshot(
				array(
					array( 'plugin' => 'payments/payments.php', 'version' => '2.4.0' ),
				),
				array(),
				array(
					array( 'plugin' => 'shipping/shipping.php', 'version' => '3.1.0' ),
				)
			)
		);
		$results = $check->run();
		$result  = $results[0]->to_array();

		$this->assertSame( 'info', $result['status'] );
		$this->assertStringContainsString( 'not treated as known incompatibility', $result['message'] );
	}

	/**
	 * Fully declared compatibility should pass.
	 *
	 * @return void
	 */
	public function test_all_detected_extensions_declaring_compatibility_passes() {
		$check   = new PluginFeatureCompatibilityCheck(
			$this->snapshot(
				array(
					array( 'plugin' => 'payments/payments.php', 'version' => '2.4.0' ),
					array( 'plugin' => 'shipping/shipping.php', 'version' => '3.1.0' ),
				),
				array(),
				array()
			)
		);
		$results = $check->run();

		$this->assertSame( 'pass', $results[0]->to_array()['status'] );
	}

	/**
	 * No third-party extension is a neutral informational result.
	 *
	 * @return void
	 */
	public function test_no_detected_extensions_is_informational() {
		$check   = new PluginFeatureCompatibilityCheck( $this->snapshot( array(), array(), array() ) );
		$results = $check->run();

		$this->assertSame( 'info', $results[0]->to_array()['status'] );
	}

	/**
	 * Build one normalized feature snapshot.
	 *
	 * @param array<int,array<string,string>> $compatible   Compatible extensions.
	 * @param array<int,array<string,string>> $incompatible Incompatible extensions.
	 * @param array<int,array<string,string>> $undeclared   Extensions without declarations.
	 * @return array<string,mixed>
	 */
	private function snapshot( array $compatible, array $incompatible, array $undeclared ) {
		return array(
			'features' => array(
				array(
					'id'           => 'custom_order_tables',
					'label'        => 'High-Performance Order Storage (HPOS)',
					'enabled'      => true,
					'compatible'   => $compatible,
					'incompatible' => $incompatible,
					'undeclared'   => $undeclared,
				),
			),
		);
	}
}
