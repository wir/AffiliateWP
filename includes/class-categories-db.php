<?php

class Affiliate_WP_Categories_DB extends Affiliate_WP_DB {

	public function __construct() {
		global $wpdb;

		if( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) {
			// Allows a single visits table for the whole network
			$this->table_name  = 'affiliate_wp_categories';
		} else {
			$this->table_name  = $wpdb->prefix . 'affiliate_wp_categories';
		}
		$this->primary_key = 'category_id';
		$this->version     = '1.0';
	}


	public function get_columns() {
		return array(
			'category_id'  => '%d',
			'object_type'  => '%d',
			'object_id'    => '%d',
			'name'         => '%s',
			'slug'         => '%s'
		);
	}

	public function get_column_defaults() {
		return array(
			'affiliate_id' => 0,
			'referral_id'  => 0,
			'date'         => date( 'Y-m-d H:i:s' ),
			'referrer'     => ! empty( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : ''
		);
	}

	public function create_table() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE {$this->table_name} (
			category_id bigint(20) NOT NULL AUTO_INCREMENT,
			object_id bigint(20) NOT NULL,
			object_type varchar(20) NOT NULL,
			name mediumtext NOT NULL,
			slug mediumtext NOT NULL,
			PRIMARY KEY  (category_id),
			KEY affiliate_id (object_id)
			) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}
}
