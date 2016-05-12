<?php

class AffWP_Creative_Fetcher extends WP_CLI\Fetchers\Base {

	/**
	 * Not found message.
	 *
	 * @since 1.9
	 * @access protected
	 * @var string
	 */
	protected $msg = "Could not find a creative with ID %s.";

	/**
	 * Retrieves a creative by ID.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param int $arg Creative ID.
	 * @return AffWP_Creative|false Creative object, false otherwise.
	 */
	public function get( $arg ) {
		return affiliate_wp()->creatives->get( $arg );
	}
}
