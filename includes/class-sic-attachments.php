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
	 * attachment has no parent, it falls back to the site homepage
	 * rather than leaving the thin attachment page accessible.
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
			// No parent post — send to home rather than leave a
			// dangling attachment-only page live.
			$redirect_url = home_url( '/' );
		}

		if ( ! $redirect_url ) {
			return;
		}

		wp_safe_redirect( $redirect_url, 301 );
		exit;
	}
}