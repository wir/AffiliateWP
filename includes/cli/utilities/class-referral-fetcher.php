<?php

class AffWP_Referral_Fetcher extends WP_CLI\Fetchers\Base {

	/**
	 * Not found message.
	 *
	 * @since 1.9
	 * @access protected
	 * @var string
	 */
	protected $msg = "Could not find the referral with ID %s.";

	/**
	 * Retrieves a referral by ID.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param int $arg Referral ID.
	 * @return AffWP_Referral|false Referral object, false otherwise.
	 */
	public function get( $arg ) {
		return affiliate_wp()->referrals->get( $arg );
	}
}
