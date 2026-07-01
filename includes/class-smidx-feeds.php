<?php
/**
 * Handles disabling RSS/Atom feeds (410 Gone) when enabled.
 *
 * @package SmartIndexControl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SMIDX_Feeds {

	public function __construct() {
		add_action( 'do_feed', array( $this, 'maybe_disable_feed' ), 1 );
		add_action( 'do_feed_rdf', array( $this, 'maybe_disable_feed' ), 1 );
		add_action( 'do_feed_rss', array( $this, 'maybe_disable_feed' ), 1 );
		add_action( 'do_feed_rss2', array( $this, 'maybe_disable_feed' ), 1 );
		add_action( 'do_feed_atom', array( $this, 'maybe_disable_feed' ), 1 );
	}

	/**
	 * Sends a 410 Gone response for feed requests when the
	 * "disable feeds" setting is on, then stops WordPress from
	 * rendering the feed template at all.
	 *
	 * 410 (rather than 404) tells search engines the resource was
	 * intentionally and permanently removed, so it gets dropped
	 * from the index faster than a generic "not found".
	 */
	public function maybe_disable_feed() {

		if ( ! SMIDX_Settings::get( 'disable_feeds' ) ) {
			return;
		}

		nocache_headers();
		status_header( 410 );

		wp_die(
			esc_html__( 'Feeds are disabled on this site.', 'smart-index-control' ),
			esc_html__( 'Feed Disabled', 'smart-index-control' ),
			array( 'response' => 410 )
		);
	}
}