<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class AffWP_Affiliate_Fetcher extends WP_CLI\Fetchers\Base {

	/**
	 * Not found message.
	 * 
	 * @since 1.9
	 * @access protected
	 * @var string
	 */
	protected $msg = "Could not find the affiliate with ID %s.";

	/**
	 * Retrieves an affiliate by ID.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param int $arg Affiliate ID.
	 * @return AffWP_Affiliate|false Affiliate object, false otherwise.
	 */
	public function get( $arg ) {
		return affwp_get_affiliate( $arg );
	}
}
