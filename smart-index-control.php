<?php
/**
 * Plugin Name:       Smart Index Control
 * Plugin URI:        https://github.com/imran-hosain/smart-index-control
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

define( 'SMIDX_VERSION', '1.0.0' );
define( 'SMIDX_PLUGIN_FILE', __FILE__ );
define( 'SMIDX_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SMIDX_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SMIDX_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Include required files.
 */
require_once SMIDX_PLUGIN_DIR . 'includes/class-smidx-settings.php';
require_once SMIDX_PLUGIN_DIR . 'includes/class-smidx-indexing.php';
require_once SMIDX_PLUGIN_DIR . 'includes/class-smidx-feeds.php';
require_once SMIDX_PLUGIN_DIR . 'includes/class-smidx-attachments.php';

/**
 * Bootstrap the plugin once everything is loaded.
 */
function smidx_init() {
	new SMIDX_Settings();
	new SMIDX_Indexing();
	new SMIDX_Feeds();
	new SMIDX_Attachments();
}
add_action( 'plugins_loaded', 'smidx_init', 20 );

/**
 * Runs on plugin activation. Sets default option values
 * so the settings page always has something sane to show.
 */
function smidx_activate() {
	$defaults = array(
		'noindex_tag_archives'      => 0,
		'noindex_category_archives' => 0,
		'disable_feeds'             => 0,
		'redirect_attachments'      => 0,
	);

	if ( false === get_option( 'smidx_settings' ) ) {
		add_option( 'smidx_settings', $defaults );
	}
}
register_activation_hook( __FILE__, 'smidx_activate' );

/**
 * Runs on plugin deactivation. We intentionally do NOT delete
 * the saved settings here — only on uninstall (see uninstall.php)
 * so a temporary deactivation doesn't wipe a client's config.
 */
function smidx_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'smidx_deactivate' );
