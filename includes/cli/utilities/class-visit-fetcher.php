<?php

class AffWP_Visit_Fetcher extends WP_CLI\Fetchers\Base {

	/**
	 * Not found message.
	 *
	 * @since 1.9
	 * @access protected
	 * @var string
	 */
	protected $msg = "Could not find the visit with ID %s.";

	/**
	 * Retrieves a visit by ID.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param int $arg Visit ID.
	 * @return AffWP_Visit|false Visit object, false otherwise.
	 */
	public function get( $arg ) {
		return affiliate_wp()->visits->get( $arg );
	}
}
