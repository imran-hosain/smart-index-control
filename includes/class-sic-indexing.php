<?php
/**
 * Handles noindex rules for tag and category archives.
 *
 * @package SmartIndexControl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SIC_Indexing {

	public function __construct() {
		add_action( 'wp_head', array( $this, 'maybe_output_noindex' ), 1 );
	}

	/**
	 * Outputs a noindex meta tag on tag/category archives if enabled
	 * in settings. Full condition logic added on Day 3.
	 */
	public function maybe_output_noindex() {
		// Logic implemented on Day 3.
	}
}
