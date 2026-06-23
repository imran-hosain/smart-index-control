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
	 * Outputs a noindex meta tag on tag/category archives if the
	 * corresponding setting is enabled. Runs early on wp_head so
	 * it appears near the top of <head>, before most other plugins'
	 * meta output.
	 */
	public function maybe_output_noindex() {

		// Never touch the admin or anything outside the main query.
		if ( is_admin() || ! is_main_query() ) {
			return;
		}

		$should_noindex = false;

		if ( is_tag() && SIC_Settings::get( 'noindex_tag_archives' ) ) {
			$should_noindex = true;
		}

		if ( is_category() && SIC_Settings::get( 'noindex_category_archives' ) ) {
			$should_noindex = true;
		}

		/**
		 * Allows other code (or a future PRO version) to override
		 * the decision on a per-request basis, e.g. to exempt a
		 * specific tag from being noindexed.
		 *
		 * @param bool $should_noindex Whether to output the noindex tag.
		 */
		$should_noindex = apply_filters( 'sic_should_noindex', $should_noindex );

		if ( $should_noindex ) {
			echo '<meta name="robots" content="noindex, follow" />' . "\n";
		}
	}
}