<?php
/**
 * WP-CLI command.
 *
 * @package WooCompatAuditor
 */

namespace WooCompatAuditor\Cli;

use WooCompatAuditor\Audit\AuditRunner;

final class AuditCommand {
	/**
	 * Register the command.
	 *
	 * @param AuditRunner $runner Audit runner.
	 * @return void
	 */
	public static function register( AuditRunner $runner ) {
		\WP_CLI::add_command(
			'woocompat audit',
			static function ( $args, $assoc_args ) use ( $runner ) {
				unset( $args );
				$format = isset( $assoc_args['format'] ) ? sanitize_key( $assoc_args['format'] ) : 'table';
				$report = $runner->report();

				if ( 'json' === $format ) {
					\WP_CLI::line( (string) wp_json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
					return;
				}

				$rows = array_map(
					static function ( $result ) {
						return array(
							'check'   => $result['label'],
							'status'  => strtoupper( $result['status'] ),
							'message' => $result['message'],
						);
					},
					$report['results']
				);

				\WP_CLI\Utils\format_items( 'table', $rows, array( 'check', 'status', 'message' ) );
			},
			array(
				'shortdesc' => 'Run the WooCompat compatibility audit.',
				'synopsis'  => array(
					array(
						'type'        => 'assoc',
						'name'        => 'format',
						'optional'    => true,
						'default'     => 'table',
						'options'     => array( 'table', 'json' ),
						'description' => 'Output format.',
					),
				),
			)
		);
	}
}
