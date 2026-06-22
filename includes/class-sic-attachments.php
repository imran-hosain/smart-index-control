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
	 * Redirects attachment page views to the parent post when
	 * the setting is enabled. Full logic added on Day 5.
	 */
	public function maybe_redirect_attachment() {
		// Logic implemented on Day 5.
	}
}
