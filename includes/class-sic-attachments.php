<?php
/**
 * Handles redirecting attachment pages to their parent post.
 *
 * @package SmartIndexControl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SIC_Attachments {

	public function __construct() {
		add_action( 'template_redirect', array( $this, 'maybe_redirect_attachment' ) );
	}

	/**
	 * Redirects a standalone attachment page to its parent post
	 * (301, permanent) when the setting is enabled. If the
	 * attachment has no parent, it falls back to the custom URL set
	 * on the Advanced tab, or the homepage if none was set.
	 */
	public function maybe_redirect_attachment() {

		if ( ! is_attachment() || ! SIC_Settings::get( 'redirect_attachments' ) ) {
			return;
		}

		$post = get_post();

		if ( ! $post ) {
			return;
		}

		$parent_id = $post->post_parent;

		if ( $parent_id ) {
			$redirect_url = get_permalink( $parent_id );
		} else {
			$custom_fallback = SIC_Settings::get( 'redirect_fallback_url', '' );
			$redirect_url     = $custom_fallback ? $custom_fallback : home_url( '/' );
		}

		if ( ! $redirect_url ) {
			return;
		}

		wp_safe_redirect( $redirect_url, 301 );
		exit;
	}
}
