<?php
/**
 * Handles the plugin's admin page: top-level menu, tab navigation,
 * settings registration, export/import, and rendering of the
 * dashboard UI.
 *
 * @package SmartIndexControl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SIC_Settings {

	const OPTION_KEY = 'sic_settings';
	const PAGE_SLUG  = 'smart-index-control';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_post_sic_export_settings', array( $this, 'handle_export' ) );
		add_action( 'admin_post_sic_import_settings', array( $this, 'handle_import' ) );
	}

	/**
	 * Adds a top-level menu item, positioned just below WooCommerce.
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'Smart Index Control', 'smart-index-control' ),
			__( 'Smart Index Control', 'smart-index-control' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_page' ),
			'dashicons-shield',
			56
		);
	}

	/**
	 * Loads the admin CSS, but only on this plugin's own page.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_assets( $hook ) {
		if ( 'toplevel_page_' . self::PAGE_SLUG !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'sic-admin',
			SIC_PLUGIN_URL . 'assets/admin.css',
			array(),
			SIC_VERSION
		);

		wp_enqueue_script( 'jquery' );
		wp_add_inline_script( 'jquery', $this->get_tab_script() );
	}

	/**
	 * Returns the inline JS that powers instant client-side tab
	 * switching, so clicking a tab doesn't trigger a full page reload.
	 *
	 * @return string
	 */
	private function get_tab_script() {
	return "
		document.addEventListener('DOMContentLoaded', function () {
			var tabs = document.querySelectorAll('.sic-tabs a');
			var panels = document.querySelectorAll('.sic-tab-panel');

			tabs.forEach(function (tab) {
				tab.addEventListener('click', function (e) {
					e.preventDefault();
					var target = tab.getAttribute('data-tab');

					tabs.forEach(function (t) { t.classList.remove('is-active'); });
					tab.classList.add('is-active');

					panels.forEach(function (p) {
						p.classList.toggle('is-active', p.getAttribute('data-tab') === target);
					});

					if (history.replaceState) {
						var url = new URL(window.location.href);
						url.searchParams.set('tab', target);
						history.replaceState(null, '', url);
					}
				});
			});
		});
	  ";
	}

	/**
	 * Registers the option.
	 */
	public function register_settings() {
		register_setting(
			'sic_settings_group',
			self::OPTION_KEY,
			array(
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
				'default'           => $this->get_defaults(),
			)
		);
	}

	/**
	 * Default values for every setting, including the Advanced tab's
	 * custom redirect fallback URL (empty string = use home_url()).
	 *
	 * @return array
	 */
	private function get_defaults() {
		return array(
			'noindex_tag_archives'      => 0,
			'noindex_category_archives' => 0,
			'disable_feeds'             => 0,
			'redirect_attachments'      => 0,
			'redirect_fallback_url'     => '',
		);
	}

	/**
	 * Tab definitions, each with a dashicon for the tab nav.
	 *
	 * @return array
	 */
	private function get_tabs() {
		return array(
			'archives'    => array(
				'label' => __( 'Archives', 'smart-index-control' ),
				'icon'  => 'dashicons-category',
			),
			'feeds'       => array(
				'label' => __( 'Feeds', 'smart-index-control' ),
				'icon'  => 'dashicons-rss',
			),
			'attachments' => array(
				'label' => __( 'Attachments', 'smart-index-control' ),
				'icon'  => 'dashicons-admin-media',
			),
			'advanced'    => array(
				'label' => __( 'Advanced', 'smart-index-control' ),
				'icon'  => 'dashicons-admin-tools',
			),
			'about'       => array(
				'label' => __( 'About', 'smart-index-control' ),
				'icon'  => 'dashicons-info-outline',
			),
		);
	}

	/**
	 * Toggle-type field definitions (rendered as switch cards).
	 *
	 * @return array
	 */
	private function get_field_definitions() {
		return array(
			'noindex_tag_archives'      => array(
				'tab'         => 'archives',
				'icon'        => 'dashicons-tag',
				'label'       => __( 'Noindex tag archives', 'smart-index-control' ),
				'description' => __( 'Add a noindex meta tag to tag archive pages (e.g. /tag/example/). Useful when tag pages create thin or duplicate content.', 'smart-index-control' ),
			),
			'noindex_category_archives' => array(
				'tab'         => 'archives',
				'icon'        => 'dashicons-category',
				'label'       => __( 'Noindex category archives', 'smart-index-control' ),
				'description' => __( 'Add a noindex meta tag to category archive pages. Leave off if categories are an important part of your site structure.', 'smart-index-control' ),
			),
			'disable_feeds'             => array(
				'tab'         => 'feeds',
				'icon'        => 'dashicons-rss',
				'label'       => __( 'Disable RSS/Atom feeds', 'smart-index-control' ),
				'description' => __( 'Returns a 410 Gone response for all feed URLs instead of leaving them publicly accessible.', 'smart-index-control' ),
			),
			'redirect_attachments'      => array(
				'tab'         => 'attachments',
				'icon'        => 'dashicons-admin-media',
				'label'       => __( 'Redirect attachment pages', 'smart-index-control' ),
				'description' => __( '301-redirects standalone attachment pages to their parent post, preventing thin attachment pages from being indexed.', 'smart-index-control' ),
			),
		);
	}

	/**
	 * Sanitizes the settings array before it's saved. Toggle keys
	 * are cast to 0/1; the redirect fallback URL is sanitized as a
	 * URL (or cleared if invalid/empty).
	 *
	 * @param array $input Raw input from the settings form.
	 * @return array
	 */
	public function sanitize_settings( $input ) {
		$clean    = array();
		$defaults = $this->get_defaults();

		foreach ( $defaults as $key => $default_value ) {
			if ( 'redirect_fallback_url' === $key ) {
				$clean[ $key ] = isset( $input[ $key ] ) ? sanitize_url( wp_unslash( $input[ $key ] ) ) : '';
				continue;
			}
			$clean[ $key ] = ! empty( $input[ $key ] ) ? 1 : 0;
		}

		return $clean;
	}

	/**
	 * Helper for other classes to read a single setting value.
	 *
	 * @param string $key     Setting key.
	 * @param mixed  $default Fallback value if not set.
	 * @return mixed
	 */
	public static function get( $key, $default = 0 ) {
		$settings = get_option( self::OPTION_KEY, array() );
		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}

	/**
	 * Streams the current settings as a downloadable JSON file.
	 * Hooked to admin-post.php so it runs before any page output,
	 * letting us safely send file-download headers.
	 */
	public function handle_export() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to do this.', 'smart-index-control' ) );
		}

		check_admin_referer( 'sic_export_settings' );

		$settings = get_option( self::OPTION_KEY, $this->get_defaults() );

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="smart-index-control-settings.json"' );

		echo wp_json_encode( $settings, JSON_PRETTY_PRINT );
		exit;
	}

	/**
	 * Handles an uploaded settings JSON file: validates it, keeps
	 * only known keys, sanitizes each, and saves. Redirects back to
	 * the Advanced tab with a success/error flag either way.
	 */
	public function handle_import() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to do this.', 'smart-index-control' ) );
		}

		check_admin_referer( 'sic_import_settings' );

		$redirect_url = add_query_arg(
			array( 'page' => self::PAGE_SLUG, 'tab' => 'advanced' ),
			admin_url( 'admin.php' )
		);

		if ( empty( $_FILES['sic_import_file']['tmp_name'] ) ) {
			wp_safe_redirect( add_query_arg( 'sic_import', 'error', $redirect_url ) );
			exit;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents
		$contents = file_get_contents( $_FILES['sic_import_file']['tmp_name'] );
		$decoded  = json_decode( (string) $contents, true );

		if ( ! is_array( $decoded ) ) {
			wp_safe_redirect( add_query_arg( 'sic_import', 'error', $redirect_url ) );
			exit;
		}

		// Re-use the same sanitizer as normal saves so an imported
		// file can never write anything our own form couldn't.
		$clean = $this->sanitize_settings( $decoded );
		update_option( self::OPTION_KEY, $clean );

		wp_safe_redirect( add_query_arg( 'sic_import', 'success', $redirect_url ) );
		exit;
	}

	/**
	 * Renders one toggle card.
	 *
	 * @param string $key      Setting key.
	 * @param array  $field    Field definition.
	 * @param array  $settings Current saved settings.
	 */
	private function render_card( $key, $field, $settings ) {
		$checked    = ! empty( $settings[ $key ] );
		$card_class = $checked ? 'sic-card is-on' : 'sic-card';
		?>
		<div class="<?php echo esc_attr( $card_class ); ?>">
			<div class="sic-card-body">
				<div class="sic-card-icon">
					<span class="dashicons <?php echo esc_attr( $field['icon'] ); ?>"></span>
				</div>
				<div class="sic-card-text">
					<strong><?php echo esc_html( $field['label'] ); ?></strong>
					<span><?php echo esc_html( $field['description'] ); ?></span>
				</div>
			</div>
			<label class="sic-switch">
				<input
					type="checkbox"
					name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $key ); ?>]"
					value="1"
					<?php checked( $checked ); ?>
				/>
				<span class="sic-switch-slider"></span>
			</label>
		</div>
		<?php
	}

	/**
	 * Renders the Advanced tab's settings field (the part that
	 * belongs inside the options.php form): the custom redirect
	 * fallback URL.
	 *
	 * @param array $settings Current saved settings.
	 */
	private function render_advanced_settings_field( $settings ) {
		?>
		<div class="sic-card sic-card-text-input">
			<div class="sic-card-body">
				<div class="sic-card-icon">
					<span class="dashicons dashicons-admin-links"></span>
				</div>
				<div class="sic-card-text">
					<strong><?php esc_html_e( 'Attachment redirect fallback URL', 'smart-index-control' ); ?></strong>
					<span><?php esc_html_e( 'Where to send visitors when an attachment has no parent post. Leave blank to use your homepage.', 'smart-index-control' ); ?></span>
					<input
						type="url"
						class="sic-text-input"
						name="<?php echo esc_attr( self::OPTION_KEY ); ?>[redirect_fallback_url]"
						value="<?php echo esc_attr( isset( $settings['redirect_fallback_url'] ) ? $settings['redirect_fallback_url'] : '' ); ?>"
						placeholder="<?php echo esc_attr( home_url( '/' ) ); ?>"
					/>
				</div>
			</div>
		</div>

		<div class="sic-footer-actions">
			<?php submit_button( __( 'Save settings', 'smart-index-control' ) ); ?>
		</div>
		<?php
	}

	/**
	 * Renders the export/import section. Deliberately rendered
	 * OUTSIDE the main options.php form — each has its own <form>
	 * pointing at admin-post.php, and forms cannot be nested in
	 * valid HTML.
	 */
	private function render_advanced_io_section() {
		?>
		<div class="sic-advanced-section">
			<h3><?php esc_html_e( 'Export & import settings', 'smart-index-control' ); ?></h3>
			<p class="sic-panel-intro">
				<?php esc_html_e( 'Useful when setting up the same configuration on multiple sites.', 'smart-index-control' ); ?>
			</p>

			<div class="sic-io-row">
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="sic_export_settings" />
					<?php wp_nonce_field( 'sic_export_settings' ); ?>
					<button type="submit" class="button">
						<span class="dashicons dashicons-download"></span>
						<?php esc_html_e( 'Export settings (.json)', 'smart-index-control' ); ?>
					</button>
				</form>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
					<input type="hidden" name="action" value="sic_import_settings" />
					<?php wp_nonce_field( 'sic_import_settings' ); ?>
					<input type="file" name="sic_import_file" accept="application/json" required />
					<button type="submit" class="button">
						<span class="dashicons dashicons-upload"></span>
						<?php esc_html_e( 'Import settings', 'smart-index-control' ); ?>
					</button>
				</form>
			</div>

			<?php if ( isset( $_GET['sic_import'] ) ) : ?>
				<?php if ( 'success' === $_GET['sic_import'] ) : ?>
					<p class="sic-io-notice is-success"><?php esc_html_e( 'Settings imported successfully.', 'smart-index-control' ); ?></p>
				<?php else : ?>
					<p class="sic-io-notice is-error"><?php esc_html_e( 'Import failed. Please upload a valid settings JSON file exported from this plugin.', 'smart-index-control' ); ?></p>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Renders the full admin page.
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$tabs       = $this->get_tabs();
		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'archives';

		if ( ! isset( $tabs[ $active_tab ] ) ) {
			$active_tab = 'archives';
		}

		$settings     = get_option( self::OPTION_KEY, $this->get_defaults() );
		$fields       = $this->get_field_definitions();
		$toggle_count = count( $this->get_defaults() ) - 1; // exclude the URL field from the count.
		$active_count = 0;

		foreach ( $fields as $key => $field ) {
			if ( ! empty( $settings[ $key ] ) ) {
				$active_count++;
			}
		}
		?>
		<div class="wrap sic-wrap">

			<div class="sic-header">
				<div class="sic-header-left">
					<div class="sic-logo">
						<span class="dashicons dashicons-shield"></span>
					</div>
					<div>
						<h1><?php esc_html_e( 'Smart Index Control', 'smart-index-control' ); ?></h1>
						<p class="sic-header-sub"><?php esc_html_e( 'Indexing, feeds, and attachment cleanup for SEO', 'smart-index-control' ); ?></p>
					</div>
				</div>
				<span class="sic-status-pill">
					<span class="dashicons dashicons-yes-alt"></span>
					<?php
					printf(
						/* translators: 1: active count, 2: total count */
						esc_html__( '%1$d of %2$d features active', 'smart-index-control' ),
						(int) $active_count,
						(int) $toggle_count
					);
					?>
				</span>
			</div>

			<nav class="sic-tabs">
				<?php foreach ( $tabs as $tab_key => $tab_data ) : ?>
					<a
						href="<?php echo esc_url( add_query_arg( array( 'page' => self::PAGE_SLUG, 'tab' => $tab_key ), admin_url( 'admin.php' ) ) ); ?>"
						data-tab="<?php echo esc_attr( $tab_key ); ?>"
						class="<?php echo $tab_key === $active_tab ? 'is-active' : ''; ?>"
					>
						<span class="dashicons <?php echo esc_attr( $tab_data['icon'] ); ?>"></span>
						<?php echo esc_html( $tab_data['label'] ); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<div class="sic-panel">
				<div class="sic-tab-panel<?php echo 'archives' === $active_tab ? ' is-active' : ''; ?>" data-tab="archives">
					<form action="options.php" method="post">
						<?php settings_fields( 'sic_settings_group' ); ?>
						<p class="sic-panel-intro">
							<?php esc_html_e( 'Enable only what you need. Nothing here is turned on by default.', 'smart-index-control' ); ?>
						</p>
						<?php foreach ( $fields as $key => $field ) : if ( 'archives' === $field['tab'] ) : $this->render_card( $key, $field, $settings ); endif; endforeach; ?>
						<div class="sic-footer-actions"><?php submit_button( __( 'Save settings', 'smart-index-control' ) ); ?></div>
					</form>
				</div>

				<div class="sic-tab-panel<?php echo 'feeds' === $active_tab ? ' is-active' : ''; ?>" data-tab="feeds">
					<form action="options.php" method="post">
						<?php settings_fields( 'sic_settings_group' ); ?>
						<p class="sic-panel-intro">
							<?php esc_html_e( 'Enable only what you need. Nothing here is turned on by default.', 'smart-index-control' ); ?>
						</p>
						<?php foreach ( $fields as $key => $field ) : if ( 'feeds' === $field['tab'] ) : $this->render_card( $key, $field, $settings ); endif; endforeach; ?>
						<div class="sic-footer-actions"><?php submit_button( __( 'Save settings', 'smart-index-control' ) ); ?></div>
					</form>
				</div>

				<div class="sic-tab-panel<?php echo 'attachments' === $active_tab ? ' is-active' : ''; ?>" data-tab="attachments">
					<form action="options.php" method="post">
						<?php settings_fields( 'sic_settings_group' ); ?>
						<p class="sic-panel-intro">
							<?php esc_html_e( 'Enable only what you need. Nothing here is turned on by default.', 'smart-index-control' ); ?>
						</p>
						<?php foreach ( $fields as $key => $field ) : if ( 'attachments' === $field['tab'] ) : $this->render_card( $key, $field, $settings ); endif; endforeach; ?>
						<div class="sic-footer-actions"><?php submit_button( __( 'Save settings', 'smart-index-control' ) ); ?></div>
					</form>
				</div>

				<div class="sic-tab-panel<?php echo 'advanced' === $active_tab ? ' is-active' : ''; ?>" data-tab="advanced">
					<form action="options.php" method="post">
						<?php settings_fields( 'sic_settings_group' ); ?>
						<?php $this->render_advanced_settings_field( $settings ); ?>
					</form>
					<?php $this->render_advanced_io_section(); ?>
				</div>

				<div class="sic-tab-panel<?php echo 'about' === $active_tab ? ' is-active' : ''; ?>" data-tab="about">
					<div class="sic-about-card">
						<p><?php esc_html_e( 'Smart Index Control is a focused SEO cleanup plugin: it controls indexing for tag/category archives, disables XML feeds, and redirects attachment pages — without touching your theme or .htaccess.', 'smart-index-control' ); ?></p>
						<p>
							<?php
							printf(
								/* translators: %s: plugin author link */
								esc_html__( 'Built and maintained by %s.', 'smart-index-control' ),
								'<a href="https://imranhosain.com" target="_blank" rel="noopener noreferrer">Imran Hosain</a>'
							);
							?>
						</p>
						<div class="sic-about-meta">
							<div><span><?php esc_html_e( 'Version', 'smart-index-control' ); ?></span><strong><?php echo esc_html( SIC_VERSION ); ?></strong></div>
							<div><span><?php esc_html_e( 'Requires', 'smart-index-control' ); ?></span><strong>WordPress 6.0+, PHP 7.4+</strong></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
