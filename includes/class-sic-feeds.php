<?php
/**
 * Handles disabling RSS/Atom feeds (410 Gone) when enabled.
 *
 * @package SmartIndexControl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SIC_Feeds {

	public function __construct() {
		add_action( 'do_feed', array( $this, 'maybe_disable_feed' ), 1 );
		add_action( 'do_feed_rdf', array( $this, 'maybe_disable_feed' ), 1 );
		add_action( 'do_feed_rss', array( $this, 'maybe_disable_feed' ), 1 );
		add_action( 'do_feed_rss2', array( $this, 'maybe_disable_feed' ), 1 );
		add_action( 'do_feed_atom', array( $this, 'maybe_disable_feed' ), 1 );
	}

	/**
	 * Sends a 410 Gone response for feed requests when the
	 * "disable feeds" setting is on. Full logic added on Day 4.
	 */
	public function maybe_disable_feed() {
		// Logic implemented on Day 4.
	}
}
