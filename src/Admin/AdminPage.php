<?php
/**
 * Admin audit screen.
 *
 * @package WooCompatAuditor
 */

namespace WooCompatAuditor\Admin;

use WooCompatAuditor\Audit\AuditRunner;

final class AdminPage {
	/** @var AuditRunner */
	private $runner;

	/** @var string */
	private $hook_suffix = '';

	/**
	 * Constructor.
	 *
	 * @param AuditRunner $runner Audit runner.
	 */
	public function __construct( AuditRunner $runner ) {
		$this->runner = $runner;
	}

	/**
	 * Register admin hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'add_page' ), 99 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Add the menu page.
	 *
	 * @return void
	 */
	public function add_page() {
		$woocommerce_active = class_exists( 'WooCommerce' );
		$parent             = $woocommerce_active ? 'woocommerce' : 'tools.php';
		$capability         = $woocommerce_active ? 'manage_woocommerce' : 'manage_options';

		$this->hook_suffix = add_submenu_page(
			$parent,
			__( 'WooCompat Auditor', 'woocompat-auditor' ),
			__( 'Compatibility Auditor', 'woocompat-auditor' ),
			$capability,
			'woocompat-auditor',
			array( $this, 'render' )
		);
	}

	/**
	 * Enqueue page styles only on the auditor screen.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue_assets( $hook ) {
		if ( $hook !== $this->hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'woocompat-auditor-admin',
			WOOCOMPAT_AUDITOR_URL . 'assets/admin.css',
			array(),
			WOOCOMPAT_AUDITOR_VERSION
		);
	}

	/**
	 * Render the audit screen.
	 *
	 * @return void
	 */
	public function render() {
		if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to view this report.', 'woocompat-auditor' ) );
		}

		$report     = $this->runner->report();
		$export_url = wp_nonce_url(
			admin_url( 'admin-post.php?action=woocompat_auditor_export' ),
			'woocompat_auditor_export'
		);
		?>
		<div class="wrap woocompat-auditor">
			<div class="woocompat-auditor__header">
				<div>
					<h1><?php echo esc_html__( 'WooCompat Auditor', 'woocompat-auditor' ); ?></h1>
					<p><?php echo esc_html__( 'A read-only compatibility and production-readiness snapshot for WooCommerce developers.', 'woocompat-auditor' ); ?></p>
				</div>
				<div class="woocompat-auditor__actions">
					<a class="button button-secondary" href="<?php echo esc_url( $export_url ); ?>"><?php echo esc_html__( 'Export JSON', 'woocompat-auditor' ); ?></a>
					<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=woocompat-auditor' ) ); ?>"><?php echo esc_html__( 'Run audit again', 'woocompat-auditor' ); ?></a>
				</div>
			</div>

			<div class="woocompat-auditor__summary" aria-label="<?php echo esc_attr__( 'Audit summary', 'woocompat-auditor' ); ?>">
				<?php foreach ( $report['summary'] as $status => $count ) : ?>
					<div class="woocompat-auditor__summary-card is-<?php echo esc_attr( $status ); ?>">
						<strong><?php echo esc_html( (string) $count ); ?></strong>
						<span><?php echo esc_html( ucfirst( $status ) ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="woocompat-auditor__grid">
				<?php foreach ( $report['results'] as $result ) : ?>
					<article class="woocompat-auditor__result is-<?php echo esc_attr( $result['status'] ); ?>">
						<div class="woocompat-auditor__result-topline">
							<h2><?php echo esc_html( $result['label'] ); ?></h2>
							<span class="woocompat-auditor__badge"><?php echo esc_html( strtoupper( $result['status'] ) ); ?></span>
						</div>
						<p><?php echo esc_html( $result['message'] ); ?></p>
						<?php if ( ! empty( $result['context'] ) ) : ?>
							<details>
								<summary><?php echo esc_html__( 'Technical context', 'woocompat-auditor' ); ?></summary>
								<pre><?php echo esc_html( wp_json_encode( $result['context'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) ); ?></pre>
							</details>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>

			<p class="woocompat-auditor__footer-note">
				<?php echo esc_html__( 'WooCompat Auditor is read-only. Warnings are investigation prompts, not automatic proof of a defect.', 'woocompat-auditor' ); ?>
			</p>
		</div>
		<?php
	}
}
