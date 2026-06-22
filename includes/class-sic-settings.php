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
	 * Registers the settings group, the option itself (with sanitization),
	 * a single section, and each individual checkbox field.
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

		add_settings_section(
			'sic_main_section',
			__( 'Indexing & Crawling', 'smart-index-control' ),
			array( $this, 'render_section_intro' ),
			'smart-index-control'
		);

		$fields = $this->get_field_definitions();

		foreach ( $fields as $key => $field ) {
			add_settings_field(
				$key,
				$field['label'],
				array( $this, 'render_checkbox_field' ),
				'smart-index-control',
				'sic_main_section',
				array(
					'key'         => $key,
					'description' => $field['description'],
				)
			);
		}
	}

	/**
	 * Default values for every setting. Centralized here so the
	 * activation hook (in the main plugin file) and this class
	 * never fall out of sync.
	 *
	 * @return array
	 */
	private function get_defaults() {
		return array(
			'noindex_tag_archives'      => 0,
			'noindex_category_archives' => 0,
			'disable_feeds'             => 0,
			'redirect_attachments'      => 0,
		);
	}

	/**
	 * Field key => label/description map. Keeping this in one place
	 * means adding a new toggle later is a one-line change here,
	 * not a change in three different methods.
	 *
	 * @return array
	 */
	private function get_field_definitions() {
		return array(
			'noindex_tag_archives'      => array(
				'label'       => __( 'Noindex Tag Archives', 'smart-index-control' ),
				'description' => __( 'Add a noindex meta tag to tag archive pages (e.g. /tag/example/). Useful when tag pages create thin or duplicate content.', 'smart-index-control' ),
			),
			'noindex_category_archives' => array(
				'label'       => __( 'Noindex Category Archives', 'smart-index-control' ),
				'description' => __( 'Add a noindex meta tag to category archive pages. Leave unchecked if your categories are an important part of your site structure.', 'smart-index-control' ),
			),
			'disable_feeds'             => array(
				'label'       => __( 'Disable RSS/Atom Feeds', 'smart-index-control' ),
				'description' => __( 'Returns a 410 Gone response for all feed URLs instead of leaving them publicly accessible.', 'smart-index-control' ),
			),
			'redirect_attachments'      => array(
				'label'       => __( 'Redirect Attachment Pages', 'smart-index-control' ),
				'description' => __( '301-redirects standalone attachment pages to their parent post, preventing thin attachment pages from being indexed.', 'smart-index-control' ),
			),
		);
	}

	/**
	 * Intro text shown above the checkbox fields.
	 */
	public function render_section_intro() {
		echo '<p>' . esc_html__( 'Enable only what you need. Nothing here is turned on by default.', 'smart-index-control' ) . '</p>';
	}

	/**
	 * Renders a single checkbox field. Used for every setting via
	 * the $args passed from add_settings_field().
	 *
	 * @param array $args Contains 'key' and 'description'.
	 */
	public function render_checkbox_field( $args ) {
		$key      = $args['key'];
		$settings = get_option( self::OPTION_KEY, $this->get_defaults() );
		$checked  = ! empty( $settings[ $key ] );
		?>
		<label>
			<input
				type="checkbox"
				name="<?php echo esc_attr( self::OPTION_KEY ); ?>[<?php echo esc_attr( $key ); ?>]"
				value="1"
				<?php checked( $checked ); ?>
			/>
			<?php echo esc_html( $args['description'] ); ?>
		</label>
		<?php
	}

	/**
	 * Sanitizes the settings array before it's saved. Every known
	 * checkbox key is cast to 0/1; unknown keys are dropped so the
	 * option can't be polluted by a malformed request.
	 *
	 * @param array $input Raw input from the settings form.
	 * @return array
	 */
	public function sanitize_settings( $input ) {
		$clean    = array();
		$defaults = $this->get_defaults();

		foreach ( $defaults as $key => $default_value ) {
			$clean[ $key ] = ! empty( $input[ $key ] ) ? 1 : 0;
		}

		return $clean;
	}

	/**
	 * Renders the settings page markup.
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Smart Index Control', 'smart-index-control' ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'sic_settings_group' );
				do_settings_sections( 'smart-index-control' );
				submit_button( __( 'Save Settings', 'smart-index-control' ) );
				?>
			</form>
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