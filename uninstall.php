<?php
/**
 * Fires only when the plugin is deleted via the Plugins screen
 * (not on simple deactivation). Cleans up stored options so we
 * don't leave orphaned data behind on a client's site.
 *
 * @package SmartIndexControl
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'sic_settings' );
