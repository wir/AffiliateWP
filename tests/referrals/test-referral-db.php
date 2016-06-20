<?php
/**
 * Tests for Affiliate_WP_DB_Affiliates class
 *
 * @covers Affiliate_WP_Referrals_DB
 * @group database
 * @group referrals
 */
class Referrals_DB_Tests extends WP_UnitTestCase {

	/**
	 * @covers Affiliate_WP_Referrals_DB::get_referrals()
	 */
	public function test_get_referrals_should_return_array_of_Referral_objects_if_not_count_query() {
		$affiliate_id = affwp_add_affiliate( array(
			'user_id' => $this->factory()->user->create() 
		) );

		for ( $i = 0; $i <= 4; $i++ ) {
			affwp_add_referral( array(
				'affiliate_id' => $affiliate_id
			) );
		}

		$results = affiliate_wp()->referrals->get_referrals();

		// Check a random referral.
		$this->assertInstanceOf( 'AffWP\Referral', $results[ rand( 0, 3 ) ] );
	}
}
