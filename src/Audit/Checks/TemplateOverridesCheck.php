<?php
/**
 * WooCommerce template override checks.
 *
 * @package WooCompatAuditor
 */

namespace WooCompatAuditor\Audit\Checks;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use WooCompatAuditor\Audit\AuditResult;

final class TemplateOverridesCheck {
	/**
	 * Run checks.
	 *
	 * @return AuditResult[]
	 */
	public function run() {
		if ( ! function_exists( 'WC' ) || ! WC() ) {
			return array();
		}

		$core_root = trailingslashit( WC()->plugin_path() ) . 'templates/';
		$roots     = array();

		$stylesheet_root = trailingslashit( get_stylesheet_directory() ) . 'woocommerce/';
		$template_root   = trailingslashit( get_template_directory() ) . 'woocommerce/';

		if ( is_dir( $stylesheet_root ) ) {
			$roots[] = $stylesheet_root;
		}

		if ( $template_root !== $stylesheet_root && is_dir( $template_root ) ) {
			$roots[] = $template_root;
		}

		if ( empty( $roots ) ) {
			return array(
				new AuditResult(
					'template-overrides',
					'WooCommerce template overrides',
					AuditResult::PASS,
					'No theme-level WooCommerce template overrides were detected.'
				),
			);
		}

		$outdated = array();
		$unknown  = array();
		$total    = 0;

		foreach ( $roots as $root ) {
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS )
			);

			foreach ( $iterator as $file ) {
				if ( ! $file->isFile() || 'php' !== strtolower( $file->getExtension() ) ) {
					continue;
				}

				++$total;
				$relative  = ltrim( str_replace( '\\', '/', substr( $file->getPathname(), strlen( $root ) ) ), '/' );
				$core_file = $core_root . $relative;

				if ( ! is_readable( $core_file ) ) {
					$unknown[] = $relative;
					continue;
				}

				$override_version = $this->read_version( $file->getPathname() );
				$core_version     = $this->read_version( $core_file );

				if ( ! $override_version || ! $core_version ) {
					$unknown[] = $relative;
					continue;
				}

				if ( version_compare( $override_version, $core_version, '<' ) ) {
					$outdated[] = array(
						'file'     => $relative,
						'override' => $override_version,
						'core'     => $core_version,
					);
				}
			}
		}

		if ( ! empty( $outdated ) ) {
			return array(
				new AuditResult(
					'template-overrides',
					'WooCommerce template overrides',
					AuditResult::WARNING,
					sprintf( '%d outdated override(s) detected across %d override file(s).', count( $outdated ), $total ),
					array(
						'total'    => $total,
						'outdated' => $outdated,
						'unknown'  => array_values( array_unique( $unknown ) ),
					)
				),
			);
		}

		return array(
			new AuditResult(
				'template-overrides',
				'WooCommerce template overrides',
				AuditResult::PASS,
				sprintf( 'No outdated overrides were detected across %d override file(s).', $total ),
				array(
					'total'   => $total,
					'unknown' => array_values( array_unique( $unknown ) ),
				)
			),
		);
	}

	/**
	 * Read the @version header from a WooCommerce template.
	 *
	 * @param string $file File path.
	 * @return string|null
	 */
	private function read_version( $file ) {
		$handle = fopen( $file, 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen

		if ( ! $handle ) {
			return null;
		}

		$contents = fread( $handle, 8192 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fread
		fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose

		if ( preg_match( '/@version\s+([0-9]+(?:\.[0-9]+)+)/i', (string) $contents, $matches ) ) {
			return $matches[1];
		}

		return null;
	}
}
