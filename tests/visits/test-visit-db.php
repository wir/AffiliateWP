<?php
/**
 * Tests for Affiliate_WP_Visits_DB class
 *
 * @covers Affiliate_WP_Visits_DB
 * @group database
 * @group visits
 */
class Visits_DB_Tests extends WP_UnitTestCase {

	/**
	 * @covers Affiliate_WP_Visits_DB::get_visits()
	 */
	public function test_get_visits_should_return_array_of_Visit_objects_if_not_count_query() {
		$affiliate_id = affwp_add_affiliate( array(
			'user_id' => $this->factory()->user->create()
		) );

		for ( $i = 0; $i <= 4; $i++ ) {
			affiliate_wp()->visits->add( array(
				'affiliate_id' => $affiliate_id
			) );
		}

		$results = affiliate_wp()->visits->get_visits();

		// Check a random visit.
		$this->assertInstanceOf( 'AffWP\Visit', $results[ rand( 0, 3 ) ] );
	}
}
