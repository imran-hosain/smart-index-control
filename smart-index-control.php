<?php
/**
 * Plugin Name:       Smart Index Control
 * Plugin URI:        https://imranhosain.com/plugins/smart-index-control
 * Description:       Cleanly control indexing for tag archives, disable XML feeds, and redirect attachment pages to their parent post — without touching your theme.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Imran Hosain
 * Author URI:        https://imranhosain.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       smart-index-control
 */

// Block direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SIC_VERSION', '1.0.0' );
define( 'SIC_PLUGIN_FILE', __FILE__ );
define( 'SIC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SIC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SIC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Include required files.
 */
require_once SIC_PLUGIN_DIR . 'includes/class-sic-settings.php';
require_once SIC_PLUGIN_DIR . 'includes/class-sic-indexing.php';
require_once SIC_PLUGIN_DIR . 'includes/class-sic-feeds.php';
require_once SIC_PLUGIN_DIR . 'includes/class-sic-attachments.php';

/**
 * Bootstrap the plugin once everything is loaded.
 */
function sic_init() {
	new SIC_Settings();
	new SIC_Indexing();
	new SIC_Feeds();
	new SIC_Attachments();
}
add_action( 'plugins_loaded', 'sic_init', 20 );

/**
 * Runs on plugin activation. Sets default option values
 * so the settings page always has something sane to show.
 */
function sic_activate() {
	$defaults = array(
		'noindex_tag_archives'      => 0,
		'noindex_category_archives' => 0,
		'disable_feeds'             => 0,
		'redirect_attachments'      => 0,
	);

	if ( false === get_option( 'sic_settings' ) ) {
		add_option( 'sic_settings', $defaults );
	}
}
register_activation_hook( __FILE__, 'sic_activate' );

/**
 * Runs on plugin deactivation. We intentionally do NOT delete
 * the saved settings here — only on uninstall (see uninstall.php)
 * so a temporary deactivation doesn't wipe a client's config.
 */
function sic_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'sic_deactivate' );
