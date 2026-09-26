<?php
/**
 * Audit result value object.
 *
 * @package WooCompatAuditor
 */

namespace WooCompatAuditor\Audit;

/**
 * Immutable-style value object for one machine-readable audit result.
 */
final class AuditResult {
	const PASS    = 'pass';
	const WARNING = 'warning';
	const FAIL    = 'fail';
	const INFO    = 'info';

	/**
	 * Stable machine-readable result identifier.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * Human-readable result label.
	 *
	 * @var string
	 */
	private $label;

	/**
	 * Normalized result status.
	 *
	 * @var string
	 */
	private $status;

	/**
	 * Human-readable result message.
	 *
	 * @var string
	 */
	private $message;

	/**
	 * Minimal diagnostic context safe for exports.
	 *
	 * @var array<string,mixed>
	 */
	private $context;

	/**
	 * Constructor.
	 *
	 * @param string              $id      Stable result identifier.
	 * @param string              $label   Human-readable label.
	 * @param string              $status  One of the status constants.
	 * @param string              $message Result message.
	 * @param array<string,mixed> $context Optional machine-readable context.
	 */
	public function __construct( $id, $label, $status, $message, array $context = array() ) {
		$this->id      = sanitize_key( $id );
		$this->label   = (string) $label;
		$this->status  = in_array( $status, array( self::PASS, self::WARNING, self::FAIL, self::INFO ), true ) ? $status : self::INFO;
		$this->message = (string) $message;
		$this->context = $context;
	}

	/**
	 * Convert to an export-safe array.
	 *
	 * @return array<string,mixed>
	 */
	public function to_array() {
		return array(
			'id'      => $this->id,
			'label'   => $this->label,
			'status'  => $this->status,
			'message' => $this->message,
			'context' => $this->context,
		);
	}
}
