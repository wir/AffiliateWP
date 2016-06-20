<?php
/**
 * Tests for Affiliate_WP_Creatives_DB class
 *
 * @covers Affiliate_WP_Creatives_DB
 * @group database
 * @group creatives
 */
class Creatives_DB_Tests extends WP_UnitTestCase {

	/**
	 * @covers Affiliate_WP_Creatives_DB::get_creatives()
	 */
	public function test_get_creatives_should_return_array_of_Creative_objects_if_not_count_query() {
		for ( $i = 0; $i <= 4; $i++ ) {
			affiliate_wp()->creatives->add();
		}

		$results = affiliate_wp()->creatives->get_creatives();

		// Check a random creative.
		$this->assertInstanceOf( 'AffWP\Creative', $results[ rand( 0, 3 ) ] );
	}
}
