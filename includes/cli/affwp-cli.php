<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class AffWP_CLI
 */
class AffWP_CLI extends WP_CLI_Command {

	/**
	 * Prints information about AffiliateWP.
	 *
	 * @since 1.9
	 * @access public
	 */
	public function details( $_, $assoc_args ) {
		if ( ! class_exists( 'Affiliate_WP' ) ) {
			WP_CLI::error( 'AffiliateWP is not installed' );
		}

		if ( defined( 'AFFILIATEWP_VERSION' ) ) {
			WP_CLI::line( sprintf( __( 'AffiliateWP version: %s', 'affiliate-wp' ), AFFILIATEWP_VERSION ) );
		}

	}

	/**
	 * Retrieves AffiliateWP stats.
	 *
	 * ## OPTIONS
	 *
	 * [--global]
	 * : Whether to retrieve global stats from a multisite network.
	 *
	 * @since 1.9
	 * @access public
	 */
	public function stats( $args, $assoc_args ) {
		$affiliate_count = $referral_count = $visit_count = 0;

		$global = \WP_CLI\Utils\get_flag_value( $assoc_args, 'global' );

		if ( is_multisite() && $global ) {
			// wp_is_large_network() is handled in wp_get_sites().
			$sites = wp_list_pluck( wp_get_sites( array( 'limit' => 9999 ) ), 'blog_id' );
		} else {
			$sites = array( get_current_blog_id() );
		}

		$count = count( $sites );

		if ( $count > 1 ) {
			WP_CLI::line( sprintf( __( '%d sites found. Retrieving stats ...', 'affiliate-wp' ), $count ) );
		}

		foreach ( $sites as $site_id ) {
			if ( $count > 1 && $global ) {
				switch_to_blog( $site_id );
			}
			$affiliate_count = $affiliate_count + affiliate_wp()->affiliates->count();
			$referral_count  = $referral_count  + affiliate_wp()->referrals->count();
			$visit_count     = $visit_count     + affiliate_wp()->visits->count();

			if ( $count > 1 && $global ) {
				restore_current_blog();
			}
		}

		// Affiliates.
		WP_CLI::line( sprintf( __( 'Total Affiliates: %d', 'affiliate-wp' ), $affiliate_count ) );

		// Referrals.
		WP_CLI::line( sprintf( __( 'Total Referrals: %d', 'affiliate-wp' ), $referral_count ) );

		// Visits.
		WP_CLI::line( sprintf( __( 'Total Visits: %d', 'affiliate-wp' ), $visit_count ) );
	}

}
WP_CLI::add_command( 'affwp', 'AffWP_CLI' );
