<?php
/**
 * Handles the plugin's admin settings page.
 *
 * @package SmartIndexControl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SIC_Settings {

	const OPTION_KEY = 'sic_settings';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Adds the "Smart Index Control" page under Settings.
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'Smart Index Control', 'smart-index-control' ),
			__( 'Smart Index Control', 'smart-index-control' ),
			'manage_options',
			'smart-index-control',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Registers the settings group and individual fields.
	 * Field UI and sanitization logic will be filled in on Day 2.
	 */
	public function register_settings() {
		register_setting( 'sic_settings_group', self::OPTION_KEY );
	}

	/**
	 * Renders the settings page markup.
	 * Replaced with full UI on Day 2.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Smart Index Control', 'smart-index-control' ); ?></h1>
			<p><?php esc_html_e( 'Settings UI coming soon.', 'smart-index-control' ); ?></p>
		</div>
		<?php
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
}
