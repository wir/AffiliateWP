<?php

class AffWP_Referral_CLI extends \WP_CLI\CommandWithDBObject {

	/**
	 * Referral display fields.
	 *
	 * @since 1.9
	 * @access protected
	 * @var array
	 */
	protected $obj_fields = array(
		'ID',
		'affiliate_name',
		'description',
		'status',
		'date'
	);

	/**
	 * Sets up the fetcher for sanity-checking.
	 *
	 * @since 1.9
	 * @access public
	 */
	public function __construct() {
		$this->fetcher = new AffWP_Referral_Fetcher();
	}

	/**
	 * Retrieves a referral object or field(s) by ID.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The referral ID to retrieve.
	 *
	 * [--field=<field>]
	 * : Instead of returning the whole referral object, returns the value of a single field.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific fields. Defaults to all fields.
	 *
	 * [--format=<format>]
	 * : Accepted values: table, json, csv, yaml. Default: table
	 *
	 * ## EXAMPLES
	 *
	 *     # save the referral field value to a file
	 *     wp post get 12 --field=earnings > earnings.txt
	 */
	public function get( $args, $assoc_args ) {
		$referral = $this->fetcher->get_check( $args[0] );

		$fields_array = get_object_vars( $referral );
		unset( $fields_array['filter'] );

		if ( empty( $assoc_args['fields'] ) ) {
			$assoc_args['fields'] = array_keys( $fields_array );
		}

		$formatter = $this->get_formatter( $assoc_args );
		$formatter->display_item( $fields_array );
	}

	/**
	 * Adds a referral.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function create( $args, $assoc_args ) {

	}

	/**
	 * Updates a referral.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function update( $args, $assoc_args ) {

	}

	/**
	 * Deletes a referral.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function delete( $args, $assoc_args ) {

	}

	/**
	 * Displays a list of referrals.
	 *
	 * ## OPTIONS
	 *
	 * [--<field>=<value>]
	 * : One or more args to pass to get_referrals().
	 *
	 * [--field=<field>]
	 * : Prints the value of a single field for each referral.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific referral fields.
	 *
	 * [--format=<format>]
	 * : Accepted values: table, csv, json, count, ids, yaml. Default: table
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields will be displayed by default for each referral:
	 *
	 * * ID (alias for referral_id)
	 * * affiliate_name
	 * * description
	 * * status
	 * * date
	 *
	 * These fields are optionally available:
	 *
	 * * reference
	 * * amount
	 * * currency
	 * * custom
	 * * campaign
	 * * visit_id
	 * * affiliate_id
	 *
	 * ## EXAMPLES
	 *
	 * affwp referral list --field=affiliate_name
	 *
	 * affwp referral list --rate_type=percentage --fields=affiliate_id,rate,earnings
	 *
	 * affwp referral list --field=earnings --format=json
	 *
	 * @subcommand list
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param array $args       Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function list_( $_, $assoc_args ) {
		$formatter = $this->get_formatter( $assoc_args );

		$defaults = array(
			'order'   => 'ASC',
		);

		$args = array_merge( $defaults, $assoc_args );

		if ( 'count' == $formatter->format ) {
			$affiliates = affiliate_wp()->referrals->get_referrals( $args, $count = true );

			WP_CLI::line( sprintf( __( 'Number of referrals: %d', 'affiliate-wp' ), $affiliates ) );
		} else {
			$referrals = affiliate_wp()->referrals->get_referrals( $args );
			$referrals = $this->process_extra_fields( array( 'ID', 'date', 'affiliate_name' ), $referrals );

			$formatter->display_items( $referrals );
		}
	}

	/**
	 * Processes extra fields that can't be derived from a simple db lookup.
	 *
	 * @since 1.9
	 * @access protected
	 *
	 * @param array $fields Array of fields to process for.
	 * @param array $items  Array of items to process `$fields` for.
	 * @return array Processed array of items.
	 */
	protected function process_extra_fields( $fields, $items ) {
		$processed = array();

		foreach ( $items as $item ) {
			// Alias for referral_id.
			if ( in_array( 'ID', $fields ) ) {
				$item->ID = $item->referral_id;
			}

			if ( in_array( 'date', $fields ) ) {
				$item->date = mysql2date( 'M j, Y', $item->date, false );
			}

			if ( in_array( 'affiliate_name', $fields ) ) {
				$item->affiliate_name = affwp_get_affiliate_name( $item->affiliate_id );
			}
			$processed[] = $item;
		}

		return $processed;
	}

}
WP_CLI::add_command( 'affwp referral', 'AffWP_Referral_CLI' );
