<?php

/**
 * Determines if the specified user ID is an affiliate.
 *
 * If no user ID is given, it will check the currently logged in user
 *
 * @since 1.0
 * @return bool
 */
function affwp_is_affiliate( $user_id = 0 ) {
	return (bool) affwp_get_affiliate_id( $user_id );
}

/**
 * Retrieves the affiliate ID of the specified user
 *
 * If no user ID is given, it will check the currently logged in user
 *
 * @since 1.0
 * @return int
 */
function affwp_get_affiliate_id( $user_id = 0 ) {

	if ( ! is_user_logged_in() && empty( $user_id ) ) {
		return false;
	}

	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	$cache_key    = md5( 'affwp_get_affiliate_id' . $user_id );
	$affiliate_id = wp_cache_get( $cache_key, 'affiliates' );

	if( false === $affiliate_id ) {

		$affiliate_id = affiliate_wp()->affiliates->get_column_by( 'affiliate_id', 'user_id', $user_id );

	}

	return $affiliate_id;
}


/**
 * Retrieves the username of the specified affiliate
 *
 * If no affiliate ID is given, it will check the currently logged in affiliate
 *
 * @since 1.6
 * @return string username if affiliate exists, boolean false otherwise
 */
function affwp_get_affiliate_username( $affiliate_id = 0 ) {

	if ( ! is_user_logged_in() && empty( $affiliate_id ) ) {
		return false;
	}

	if ( empty( $affiliate_id ) ) {
		$affiliate_id = affwp_get_affiliate_id();
	}

	$affiliate = affwp_get_affiliate( $affiliate_id );

	if ( $affiliate ) {
		$user_info = get_userdata( $affiliate->user_id );

		if ( $user_info ) {
			$username  = esc_html( $user_info->user_login );
			return esc_html( $username );
		}

	}

	return false;

}

/**
 * Retrieves the affiliate first name and/or last name, if set.
 *
 * If only one name (first_name or last_name) is provided, this function will return
 * only that name.
 *
 * @since  1.8
 *
 * @uses affwp_get_affiliate_id
 * @uses affwp_get_affiliate
 *
 * @param  $affiliate_id int Optional. Affiliate ID. Default is the ID of the current affiliate.
 * @return string The affiliate user's first and/or last name  if set. An empty string if the affiliate ID
 *                is invalid or neither first nor last name are set.
 */
function affwp_get_affiliate_name( $affiliate_id = 0 ) {

	if ( empty( $affiliate_id ) ) {
		$affiliate_id = affwp_get_affiliate_id();
	}

	$affiliate  = affwp_get_affiliate( $affiliate_id );

	// Return empty if no affiliate.
	if ( empty( $affiliate ) ) {
		return '';
	}

	$user_info    = get_userdata( $affiliate->user_id );
	$first_name   = esc_html( $user_info->first_name );
	$last_name    = esc_html( $user_info->last_name );

	// Check if both names are set first.
	if ( ! empty( $first_name ) && ! empty( $last_name ) ) {
		return $first_name . ' ' . $last_name;
	}

	// If neither are set, return an empty string.
	if ( empty( $first_name ) && empty( $last_name ) ) {
		return '';
	}

	// First name only
	if ( ! empty( $first_name ) && empty( $last_name ) ) {
		return $first_name;
	}

	// Last name only
	if ( empty( $first_name ) && ! empty( $last_name ) ) {
		return $last_name;
	}
}

/**
 * Determines whether or not the affiliate is active
 *
 * If no affiliate ID is given, it will check the currently logged in affiliate
 *
 * @since 1.6
 * @return int
 */
function affwp_is_active_affiliate( $affiliate_id = 0 ) {

	if ( empty( $affiliate_id ) ) {
		$affiliate_id = affwp_get_affiliate_id();
	}

	if ( 'active' == affwp_get_affiliate_status( $affiliate_id ) ) {
		return true;
	}

	return false;
}

/**
 * Retrieves an affiliate's user ID
 *
 * @since 1.0
 *
 * @param int|stdClass $affiliate Affiliate ID or object.
 * @return int|false Affiliate user ID, otherwise false.
 */
function affwp_get_affiliate_user_id( $affiliate ) {

	$affiliate = affwp_get_affiliate( $affiliate );

	return is_object( $affiliate ) ? $affiliate->user_id : false;

}

/**
 * Retrieves the affiliate object
 *
 * @since 1.0
 *
 * @param int|stdClass $affiliate Affiliate ID or object.
 * @return stdClass|false Affiliate object if found, otherwise false.
 */
function affwp_get_affiliate( $affiliate ) {

	if ( is_object( $affiliate ) && isset( $affiliate->affiliate_id ) ) {
		$affiliate_id = $affiliate->affiliate_id;
	} elseif ( is_numeric( $affiliate ) ) {
		$affiliate_id = absint( $affiliate );
	} else {
		return false;
	}

	$cache_key = md5( 'affwp_get_affiliate' . $affiliate_id );
	$affiliate = wp_cache_get( $cache_key, 'affiliates' );

	if( false === $affiliate ) {

		$affiliate = affiliate_wp()->affiliates->get( $affiliate_id );

	}

	return $affiliate;
}

/**
 * Retrieves the affiliate's status
 *
 * @since 1.0
 * @return string
 */
function affwp_get_affiliate_status( $affiliate ) {

	$affiliate = affwp_get_affiliate( $affiliate );

	return is_object( $affiliate ) ? $affiliate->status : false;
}

/**
 * Sets the status for an affiliate
 *
 * @since 1.0
 * @return bool
 */
function affwp_set_affiliate_status( $affiliate, $status = '' ) {

	if ( is_object( $affiliate ) && isset( $affiliate->affiliate_id ) ) {
		$affiliate_id = $affiliate->affiliate_id;
	} elseif ( is_numeric( $affiliate ) ) {
		$affiliate_id = absint( $affiliate );
	} else {
		return false;
	}

	$old_status = affiliate_wp()->affiliates->get_column( 'status', $affiliate_id );

	do_action( 'affwp_set_affiliate_status', $affiliate_id, $status, $old_status );

	if ( affiliate_wp()->affiliates->update( $affiliate_id, array( 'status' => $status ), '', 'affiliate' ) ) {

		return true;
	}

}

/**
 * Retrieves the affiliate's status and returns a translatable string
 *
 * @since  1.8
 * @param  $affiliate int|object Affiliate ID or object
 * @return string $status_label A translatable, filterable label indicating affiliate status
 */
function affwp_get_affiliate_status_label( $affiliate ) {

	$affiliate    = affwp_get_affiliate( $affiliate );

	if ( ! $affiliate ) {
		return;
	}

	$status       = '';
	$status_label = '';

	// Get current affiliate status
	$status       = $affiliate->status;

	// Return translatable string
	switch( $status ) {

		case 'active':
			$status_label = __( 'Active','affiliate-wp' );
			break;
		case 'inactive':
			$status_label = __( 'Inactive','affiliate-wp' );
			break;
		case 'pending':
			$status_label = __( 'Pending','affiliate-wp' );
			break;
		case 'rejected':
			$status_label = __( 'Rejected','affiliate-wp' );
			break;
	}

	return apply_filters( 'affwp_get_affiliate_status_label', $status_label );
}

/**
 * Retrieves the referral rate for an affiliate
 *
 * @since  1.0
 * @param  int     $affiliate_id  The ID of the affiliate we are getting a rate for
 * @param  bool    $formatted     Whether to return a formatted rate with %/currency
 * @param  string  $product_rate  A custom product rate that overrides site/affiliate settings
 * @return string
 */
function affwp_get_affiliate_rate( $affiliate_id = 0, $formatted = false, $product_rate = '', $reference = '' ) {

	// Global referral rate setting, fallback to 20
	$default_rate = affiliate_wp()->settings->get( 'referral_rate', 20 );
	$default_rate = affwp_abs_number_round( $default_rate );

	// Get product-specific referral rate, fallback to global rate
	$product_rate = affwp_abs_number_round( $product_rate );
	$product_rate = ( null !== $product_rate ) ? $product_rate : $default_rate;

	// Get affiliate-specific referral rate
	$affiliate_rate = affiliate_wp()->affiliates->get_column( 'rate', $affiliate_id );

	// Get rate in order of priority: Affiliate -> Product -> Global
	$rate = affwp_abs_number_round( $affiliate_rate );
	$rate = ( null !== $rate ) ? $rate : $product_rate;

	// Get the referral rate type
	$type = affwp_get_affiliate_rate_type( $affiliate_id );

	// Format percentage rates
	$rate = ( 'percentage' === $type ) ? $rate / 100 : $rate;

	/**
	 * Filter the affiliate rate
	 *
	 * @param  string  $rate
	 * @param  int     $affiliate_id
	 * @param  string  $type
	 */
	$rate = (string) apply_filters( 'affwp_get_affiliate_rate', $rate, $affiliate_id, $type, $reference );

	// Return rate now if formatting is not required
	if ( ! $formatted ) {
		return $rate;
	}

	// Format the rate based on the type
	switch ( $type ) {

		case 'percentage' :

			$rate = affwp_abs_number_round( $rate * 100 ) . '%';

			break;

		case 'flat' :

			$rate = affwp_currency_filter( $rate );

			break;

	}

	return $rate;

}

/**
 * Determine if an affiliate has a custom rate
 *
 * @since 1.5
 * @return bool
 */
function affwp_affiliate_has_custom_rate( $affiliate_id = 0 ) {

	$ret = (bool) affiliate_wp()->affiliates->get_column( 'rate', $affiliate_id );

	return apply_filters( 'affwp_affiliate_has_custom_rate', $ret, $affiliate_id );
}

/**
 * Retrieves the referral rate type for an affiliate
 *
 * Either "flat" or "percentage"
 *
 * @since 1.1
 * @return string
 */
function affwp_get_affiliate_rate_type( $affiliate_id = 0 ) {

	// Allowed types
	$types = affwp_get_affiliate_rate_types();

	// default rate
	$type = affiliate_wp()->settings->get( 'referral_rate_type', 'percentage' );

	$affiliate_rate_type = affiliate_wp()->affiliates->get_column( 'rate_type', $affiliate_id );

	if ( ! empty( $affiliate_rate_type ) ) {

		$type = $affiliate_rate_type;

	}

	if ( ! array_key_exists( $type, $types ) ) {
		$type = 'percentage';
	}

	return apply_filters( 'affwp_get_affiliate_rate_type', $type, $affiliate_id );

}

/**
 * Retrieves an array of allowed affiliate rate types
 *
 * @since 1.1
 * @return array
 */
function affwp_get_affiliate_rate_types() {

	// Allowed types
	$types = array(
		'percentage' => __( 'Percentage (%)', 'affiliate-wp' ),
		'flat'       => sprintf( __( 'Flat %s', 'affiliate-wp' ), affwp_get_currency() )
	);

	return apply_filters( 'affwp_get_affiliate_rate_types', $types );

}

/**
 * Retrieves the affiliate's email address
 *
 * @since  1.0
 * @param  object|int $affiliate
 * @param  mixed      $default (optional)
 * @return mixed
 */
function affwp_get_affiliate_email( $affiliate, $default = false ) {

	$affiliate = is_numeric( $affiliate ) ? affwp_get_affiliate( $affiliate ) : $affiliate;

	if ( empty( $affiliate->affiliate_id ) ) {
		return $default;
	}

	$user_id = affwp_get_affiliate_user_id( $affiliate );
	$user    = get_userdata( $user_id );

	if ( empty( $user->user_email ) || ! is_email( $user->user_email ) ) {
		return $default;
	}

	return $user->user_email;

}

/**
 * Retrieves the affiliate's payment email address
 *
 * @since  1.7
 * @param  object|int $affiliate
 * @return mixed
 */
function affwp_get_affiliate_payment_email( $affiliate ) {

	$affiliate = is_numeric( $affiliate ) ? affwp_get_affiliate( $affiliate ) : $affiliate;

	if ( empty( $affiliate->payment_email ) || ! is_email( $affiliate->payment_email ) ) {
		return affwp_get_affiliate_email( $affiliate );
	}

	return $affiliate->payment_email;

}

/**
 * Retrieves the affiliate's user login (username)
 *
 * @since  1.6
 * @param  object|int $affiliate
 * @param  mixed      $default (optional)
 * @return mixed
 */
function affwp_get_affiliate_login( $affiliate, $default = false ) {

	$affiliate = is_numeric( $affiliate ) ? affwp_get_affiliate( $affiliate ) : $affiliate;

	if ( empty( $affiliate->affiliate_id ) ) {
		return $default;
	}

	$user_id = affwp_get_affiliate_user_id( $affiliate );
	$user    = get_userdata( $user_id );

	if ( empty( $user->user_login ) ) {
		return $default;
	}

	return $user->user_login;

}

/**
 * Deletes an affiliate
 *
 * @since 1.0
 * @param $delete_data bool
 * @return bool
 */
function affwp_delete_affiliate( $affiliate, $delete_data = false ) {

	if ( is_object( $affiliate ) && isset( $affiliate->affiliate_id ) ) {
		$affiliate_id = $affiliate->affiliate_id;
	} elseif ( is_numeric( $affiliate ) ) {
		$affiliate_id = absint( $affiliate );
	} else {
		return false;
	}

	if( $delete_data ) {

		$user_id   = affwp_get_affiliate_user_id( $affiliate_id );
		$referrals = affiliate_wp()->referrals->get_referrals( array( 'affiliate_id' => $affiliate_id, 'number' => -1 ) );
		$visits    = affiliate_wp()->visits->get_visits( array( 'affiliate_id' => $affiliate_id, 'number' => -1 ) );

		foreach( $referrals as $referral ) {
			affiliate_wp()->referrals->delete( $referral->referral_id );
		}

		foreach( $visits as $visit ) {
			affiliate_wp()->visits->delete( $visit->visit_id );
		}

		delete_user_meta( $user_id, 'affwp_referral_notifications' );
		delete_user_meta( $user_id, 'affwp_promotion_method' );

	}

	$deleted = affiliate_wp()->affiliates->delete( $affiliate_id );

	if( $deleted ) {

		do_action( 'affwp_affiliate_deleted', $affiliate_id, $delete_data );

	}

	return $deleted;

}

/**
 * Retrieves the total paid earnings for an affiliate
 *
 * @since 1.0
 * @return float
 */
function affwp_get_affiliate_earnings( $affiliate, $formatted = false ) {

	if ( is_object( $affiliate ) && isset( $affiliate->affiliate_id ) ) {
		$affiliate_id = $affiliate->affiliate_id;
	} elseif ( is_numeric( $affiliate ) ) {
		$affiliate_id = absint( $affiliate );
	} else {
		return false;
	}

	$earnings = affiliate_wp()->affiliates->get_column( 'earnings', $affiliate_id );

	if ( empty( $earnings ) ) {

		$earnings = 0;

	}

	if ( $formatted ) {

		$earnings = affwp_currency_filter( $earnings );

	}

	return $earnings;
}

/**
 * Retrieves the total unpaid earnings for an affiliate
 *
 * @since 1.0
 * @return float
 */
function affwp_get_affiliate_unpaid_earnings( $affiliate, $formatted = false ) {

	if ( is_object( $affiliate ) && isset( $affiliate->affiliate_id ) ) {
		$affiliate_id = $affiliate->affiliate_id;
	} elseif ( is_numeric( $affiliate ) ) {
		$affiliate_id = absint( $affiliate );
	} else {
		return false;
	}

	$referrals = affiliate_wp()->referrals->get_referrals( array( 'affiliate_id' => $affiliate_id, 'status' => 'unpaid', 'number' => -1 ) );
	$earnings = 0;

	if ( ! empty( $referrals ) ) {


		foreach( $referrals as $referral ) {

			$earnings += $referral->amount;

		}
	}

	if ( $formatted ) {

		$earnings = affwp_currency_filter( $earnings );

	}

	return $earnings;
}

/**
 * Increases an affiliate's total paid earnings by the specified amount
 *
 * @since 1.0
 * @return float|bool
 */
function affwp_increase_affiliate_earnings( $affiliate_id = 0, $amount = '' ) {

	if ( empty( $affiliate_id ) ) {
		return false;
	}

	if ( empty( $amount ) || floatval( $amount ) <= 0 ) {
		return false;
	}

	$earnings = affwp_get_affiliate_earnings( $affiliate_id );
	$earnings += $amount;
	$earnings = round( $earnings, affwp_get_decimal_count() );
	if ( affiliate_wp()->affiliates->update( $affiliate_id, array( 'earnings' => $earnings ), '', 'affiliate' ) ) {
		$alltime = get_option( 'affwp_alltime_earnings' );
		$alltime += $amount;
		update_option( 'affwp_alltime_earnings', $alltime );

		return $earnings;

	} else {

		return false;

	}

}

/**
 * Decreases an affiliate's total paid earnings by the specified amount
 *
 * @since 1.0
 * @return float|bool
 */
function affwp_decrease_affiliate_earnings( $affiliate_id = 0, $amount = '' ) {

	if ( empty( $affiliate_id ) ) {
		return false;
	}

	if ( empty( $amount ) || floatval( $amount ) <= 0 ) {
		return false;
	}

	$earnings = affwp_get_affiliate_earnings( $affiliate_id );
	$earnings -= $amount;
	$earnings = round( $earnings, affwp_get_decimal_count() );
	if ( $earnings < 0 ) {
		$earnings = 0;
	}
	if ( affiliate_wp()->affiliates->update( $affiliate_id, array( 'earnings' => $earnings ), '', 'affiliate' ) ) {

		$alltime = get_option( 'affwp_alltime_earnings' );
		$alltime -= $amount;
		if ( $alltime < 0 ) {
			$alltime = 0;
		}
		update_option( 'affwp_alltime_earnings', $alltime );

		return $earnings;

	} else {

		return false;

	}

}

/**
 * Retrieves the number of paid referrals for an affiliate
 *
 * @since 1.0
 * @return int
 */
function affwp_get_affiliate_referral_count( $affiliate ) {

	if ( is_object( $affiliate ) && isset( $affiliate->affiliate_id ) ) {
		$affiliate_id = $affiliate->affiliate_id;
	} elseif ( is_numeric( $affiliate ) ) {
		$affiliate_id = absint( $affiliate );
	} else {
		return false;
	}

	return affiliate_wp()->affiliates->get_column( 'referrals', $affiliate_id );
}

/**
 * Increases an affiliate's total paid referrals by 1
 *
 * @since 1.0
 * @return float|bool
 */
function affwp_increase_affiliate_referral_count( $affiliate_id = 0 ) {

	if ( empty( $affiliate_id ) ) {
		return false;
	}

	$referrals = affwp_get_affiliate_referral_count( $affiliate_id );
	$referrals += 1;

	if ( affiliate_wp()->affiliates->update( $affiliate_id, array( 'referrals' => $referrals ), '', 'affiliate' ) ) {

		return $referrals;

	} else {

		return false;

	}

}

/**
 * Decreases an affiliate's total paid referrals by 1
 *
 * @since 1.0
 * @return float|bool
 */
function affwp_decrease_affiliate_referral_count( $affiliate_id = 0 ) {

	if ( empty( $affiliate_id ) ) {
		return false;
	}

	$referrals = affwp_get_affiliate_referral_count( $affiliate_id );
	$referrals -= 1;
	if ( $referrals < 0 ) {
		$referrals = 0;
	}
	if ( affiliate_wp()->affiliates->update( $affiliate_id, array( 'referrals' => $referrals ), '', 'affiliate' ) ) {

		return $referrals;

	} else {

		return false;

	}

}

/**
 * Retrieves an affiliate's total visit count
 *
 * @since 1.0
 * @return int
 */
function affwp_get_affiliate_visit_count( $affiliate ) {

	if ( is_object( $affiliate ) && isset( $affiliate->affiliate_id ) ) {
		$affiliate_id = $affiliate->affiliate_id;
	} elseif ( is_numeric( $affiliate ) ) {
		$affiliate_id = absint( $affiliate );
	} else {
		return false;
	}

	$visits = affiliate_wp()->affiliates->get_column( 'visits', $affiliate_id );

	if ( $visits < 0 ) {
		$visits = 0;
	}

	return absint( $visits );
}

/**
 * Increases an affiliate's total visit count by 1
 *
 * @since 1.0
 * @return int
 */
function affwp_increase_affiliate_visit_count( $affiliate_id = 0 ) {

	if ( empty( $affiliate_id ) ) {
		return false;
	}

	$visits = affwp_get_affiliate_visit_count( $affiliate_id );
	$visits += 1;

	if ( affiliate_wp()->affiliates->update( $affiliate_id, array( 'visits' => $visits ), '', 'affiliate' ) ) {

		return $visits;

	} else {

		return false;

	}

}

/**
 * Decreases an affiliate's total visit count by 1
 *
 * @since 1.0
 * @return float|bool
 */
function affwp_decrease_affiliate_visit_count( $affiliate_id = 0 ) {

	if ( empty( $affiliate_id ) ) {
		return false;
	}

	$visits = affwp_get_affiliate_visit_count( $affiliate_id );
	$visits -= 1;

	if ( $visits < 0 ) {
		$visits = 0;
	}

	if ( affiliate_wp()->affiliates->update( $affiliate_id, array( 'visits' => $visits ), '', 'affiliate' ) ) {

		return $visits;

	} else {

		return false;

	}

}

/**
 * Retrieves the affiliate's conversion rate
 *
 * @since 1.0
 * @return float
 */
function affwp_get_affiliate_conversion_rate( $affiliate ) {

	if ( is_object( $affiliate ) && isset( $affiliate->affiliate_id ) ) {
		$affiliate_id = $affiliate->affiliate_id;
	} elseif ( is_numeric( $affiliate ) ) {
		$affiliate_id = absint( $affiliate );
	} else {
		return false;
	}

	$rate = 0;

	$referrals = affiliate_wp()->referrals->count( array( 'affiliate_id' => $affiliate_id, 'status' => array( 'paid', 'unpaid' ) ) );
	$visits    = affwp_get_affiliate_visit_count( $affiliate_id );
	if ( $visits > 0 ) {
		$rate = round( ( $referrals / $visits ) * 100, 2 );
	}

	return apply_filters( 'affwp_get_affiliate_conversion_rate', $rate . '%', $affiliate_id );

}

/**
 * Retrieves the affiliate's tracked campaigns
 *
 * @since 1.7
 * @return array
 */
function affwp_get_affiliate_campaigns( $affiliate ) {

	if ( is_object( $affiliate ) && isset( $affiliate->affiliate_id ) ) {
		$affiliate_id = $affiliate->affiliate_id;
	} elseif ( is_numeric( $affiliate ) ) {
		$affiliate_id = absint( $affiliate );
	} else {
		return false;
	}

	$campaigns = affiliate_wp()->campaigns->get_campaigns( $affiliate_id );

	return apply_filters( 'affwp_get_affiliate_campaigns', $campaigns, $affiliate_id );

}

/**
 * Adds a new affiliate to the database
 *
 * @since 1.0
 *
 * @see Affiliate_WP_DB_Affiliates::add()
 *
 * @param array $data {
 *     Optional. Array of arguments for adding a new affiliate. Default empty array.
 *
 *     @type string $status          Affiliate status. Default 'active'.
 *     @type string $date_registered Date the affiliate was registered. Default is the current time.
 *     @type string $rate            Affiliate-specific referral rate.
 *     @type string $rate_type       Rate type. Accepts 'percentage' or 'flat'.
 *     @type string $payment_email   Affiliate payment email.
 *     @type int    $earnings        Affiliate earnings. Default 0.
 *     @type int    $referrals       Number of affiliate referrals.
 *     @type int    $visits          Number of visits.
 *     @type int    $user_id         User ID used to correspond to the affiliate.
 * }
 * @return bool
 */
function affwp_add_affiliate( $data = array() ) {

	if ( ! empty( $data['status'] ) ) {
		$status = $data['status'];
	} elseif ( affiliate_wp()->settings->get( 'require_approval' ) ) {
		$status = 'pending';
	} else {
		$status = 'active';
	}

	if ( empty( $data['user_id'] ) ) {
		return false;
	}

	$user_id = absint( $data['user_id'] );

	if ( ! affiliate_wp()->affiliates->get_by( 'user_id', $user_id ) ) {

		$args = array(
			'user_id'       => $user_id,
			'status'        => $status,
			'rate'          => ! empty( $data['rate'] ) ? sanitize_text_field( $data['rate'] ) : '',
			'rate_type'     => ! empty( $data['rate_type' ] ) ? sanitize_text_field( $data['rate_type'] ) : '',
			'payment_email' => ! empty( $data['payment_email'] ) ? sanitize_text_field( $data['payment_email'] ) : ''
		);

		$affiliate_id = affiliate_wp()->affiliates->add( $args );

		if ( $affiliate_id ) {

			affwp_set_affiliate_status( $affiliate_id, $status );

			return $affiliate_id;
		}

	}

	return false;

}

/**
 * Updates an affiliate
 *
 * @since 1.0
 * @return bool
 */
function affwp_update_affiliate( $data = array() ) {

	if ( empty( $data['affiliate_id'] ) ) {
		return false;
	}

	$args         = array();
	$affiliate_id = absint( $data['affiliate_id'] );
	$affiliate    = affwp_get_affiliate( $affiliate_id );
	$user_id      = empty( $affiliate->user_id ) ? absint( $data['user_id'] ) : $affiliate->user_id;

	$args['account_email'] = ! empty( $data['account_email' ] ) && is_email( $data['account_email' ] ) ? sanitize_text_field( $data['account_email'] ) : '';
	$args['payment_email'] = ! empty( $data['payment_email' ] ) && is_email( $data['payment_email' ] ) ? sanitize_text_field( $data['payment_email'] ) : '';
	$args['rate']          = ( isset( $data['rate' ] ) && '' !== $data['rate' ] )                      ? sanitize_text_field( $data['rate'] )          : '';
	$args['rate_type']     = ! empty( $data['rate_type' ] ) ? sanitize_text_field( $data['rate_type'] ) : '';
	$args['user_id']       = $user_id;

	/**
	 * Fires immediately before data for the current affiliate is updated.
	 *
	 * @since 1.8
	 *
	 * @param stdClass $affiliate Affiliate object.
	 * @param array    $args      Prepared affiliate data.
	 * @param array    $data      Raw affiliate data.
	 */
	do_action( 'affwp_pre_update_affiliate', $affiliate, $args, $data );

	$updated = affiliate_wp()->affiliates->update( $affiliate_id, $args, '', 'affiliate' );

	/**
	 * Fires immediately after an affiliate has been updated.
	 *
	 * @since 1.8
	 *
	 * @param stdClass $affiliate Updated affiliate object.
	 * @param bool     $updated   Whether the update was successful.
	 */
	do_action( 'affwp_updated_affiliate', affwp_get_affiliate( $affiliate ), $updated );

	if ( $updated ) {

		// update affiliate's account email
		if( wp_update_user( array( 'ID' => $user_id, 'user_email' => $args['account_email'] ) ) ) {

			return true;

		}

	}

	return false;

}

/**
 * Updates an affiliate's profile settings
 *
 * @since 1.0
 * @return bool
 */
function affwp_update_profile_settings( $data = array() ) {

	if ( ! is_user_logged_in() ) {
		return false;
	}

	if ( empty( $data['affiliate_id'] ) ) {
		return false;
	}

	if ( affwp_get_affiliate_id() != $data['affiliate_id'] && ! current_user_can( 'manage_affiliates' ) ) {

		return false;
	}

	$affiliate_id = absint( $data['affiliate_id'] );
	$user_id      = affwp_get_affiliate_user_id( $affiliate_id );

	if ( ! empty( $data['referral_notifications'] ) ) {

		update_user_meta( $user_id, 'affwp_referral_notifications', '1' );

	} else {

		delete_user_meta( $user_id, 'affwp_referral_notifications' );

	}

	if ( ! empty( $data['payment_email'] ) && is_email( $data['payment_email'] ) ) {
		affiliate_wp()->affiliates->update( $affiliate_id, array( 'payment_email' => $data['payment_email'] ), '', 'affiliate' );
	}

	do_action( 'affwp_update_affiliate_profile_settings', $data );

	if ( ! empty( $_POST['affwp_action'] ) ) {
		wp_redirect( add_query_arg( 'affwp_notice', 'profile-updated' ) ); exit;
	}
}

/**
 * Builds an affiliate's referral URL.
 *
 * Used by creatives, referral URL generator and [affiliate_referral_url] shortcode
 *
 * @since 1.6
 *
 * @param array $args {
 *     Optional. Array of arguments for building an affiliate referral URL. Default empty array.
 *
 *     @type int          $affiliate_id Affiliate ID. Default is the current user's affiliate ID.
 *     @type string|false $pretty       Whether to build a pretty referral URL. Accepts 'yes' or 'no'. False
 *                                      disables pretty URLs. Default empty, see affwp_is_pretty_referral_urls().
 *     @type string       $format       Referral format. Accepts 'id' or 'username'. Default empty,
 *                                      see affwp_get_referral_format().
 *     @type string       $base_url     Base URL to use for building a referral URL. If specified, should contain
 *                                      'query' and 'fragment' query vars. 'scheme', 'host', and 'path' query vars
 *                                      can also be passed as part of the base URL. Default empty.
 * }
 * @return string Trailing-slashed value of home_url() when `$args` is empty, built referral URL otherwise.
 */
function affwp_get_affiliate_referral_url( $args = array() ) {

	$defaults = array(
		'pretty' => '',
		'format' => '',
	);

	$args = wp_parse_args( $args, $defaults );

	// get affiliate ID if passed in
	$affiliate_id = isset( $args['affiliate_id'] ) ? $args['affiliate_id'] : affwp_get_affiliate_id();

	// get format, username or id
	$format = isset( $args['format'] ) ? $args['format'] : affwp_get_referral_format();

	// pretty URLs
	if ( ! empty( $args['pretty'] ) && 'yes' == $args['pretty'] ) {
		// pretty URLS explicitly turned on
		$pretty = true;
	} elseif ( ( ! empty( $args['pretty'] ) && 'no' == $args['pretty'] ) || false === $args['pretty'] ) {
		// pretty URLS explicitly turned off
		$pretty = false;
	} else {
		// pretty URLs set from admin
		$pretty = affwp_is_pretty_referral_urls();
	}

	// get base URL
	if ( isset( $args['base_url'] ) ) {
		$base_url = $args['base_url'];
	} else {
		$base_url = affwp_get_affiliate_base_url();
	}

	// add trailing slash only if no query string exists and there's no fragment identifier
	if ( isset( $args['base_url'] ) && ! array_key_exists( 'query', parse_url( $base_url ) ) && ! array_key_exists( 'fragment', parse_url( $base_url ) ) ) {
		$base_url = trailingslashit( $args['base_url'] );
	}

	// the format value, either affiliate's ID or username
	$format_value = affwp_get_referral_format_value( $format, $affiliate_id );

	$url_parts = parse_url( $base_url );

	// if fragment identifier exists in base URL, strip it and store in variable so we can append it later
	$fragment        = array_key_exists( 'fragment', $url_parts ) ? '#' . $url_parts['fragment'] : '';

	// if query exists in base URL, strip it and store in variable so we can append to the end of the URL
	$query_string    = array_key_exists( 'query', $url_parts ) ? '?' . $url_parts['query'] : '';

	$url_scheme      = isset( $url_parts['scheme'] ) ? $url_parts['scheme'] : 'http';
	$url_host        = isset( $url_parts['host'] ) ? $url_parts['host'] : '';
	$constructed_url = $url_scheme . '://' . $url_host . $url_parts['path'];
	$base_url        = $constructed_url;

	// set up URLs
	$pretty_urls     = trailingslashit( $base_url ) . trailingslashit( affiliate_wp()->tracking->get_referral_var() ) . trailingslashit( $format_value ) . $query_string . $fragment;
	$non_pretty_urls = esc_url( add_query_arg( affiliate_wp()->tracking->get_referral_var(), $format_value, $base_url . $query_string . $fragment ) );

	if ( $pretty ) {
		$referral_url = $pretty_urls;
	} else {
		$referral_url = $non_pretty_urls;
	}

	return $referral_url;

}

/**
 * Gets the base URL that is then displayed in the Page URL input field of the affiliate area
 *
 * @since 1.6
 * @return string
 */
function affwp_get_affiliate_base_url() {

	if ( isset( $_GET['url'] ) && ! empty( $_GET['url'] ) ) {
		$base_url = urldecode( $_GET['url'] );
	} else {
		$base_url = home_url( '/' );
	}

	$default_referral_url = affiliate_wp()->settings->get( 'default_referral_url' );

	if ( $default_referral_url ) {
		$base_url = $default_referral_url;
	}

	return apply_filters( 'affwp_affiliate_referral_url_base', $base_url );

}

/**
 * Retrieves the page ID for the Affiliate Area page.
 *
 * @since 1.8
 *
 * @return int Affiliate Area page ID.
 */
function affwp_get_affiliate_area_page_id() {
	$affiliate_page_id = affiliate_wp()->settings->get( 'affiliates_page' );

	/**
	 * Filter the Affiliate Area page ID.
	 *
	 * @since 1.8
	 *
	 * @param int $affiliate_page_id Affiliate Area page ID.
	 */
	return apply_filters( 'affwp_affiliate_area_page_id', $affiliate_page_id );
}

/**
 * Retrieves the Affiliates Area page URL.
 *
 * @since 1.8
 *
 * @param string $tab Optional. Tab ID. Default empty.
 * @return string If $tab is specified and valid, the URL for the given tab within the Affiliate Area page.
 *                Otherwise the Affiliate Area page URL.
 */
function affwp_get_affiliate_area_page_url( $tab = '' ) {
	$affiliate_area_page_id = affwp_get_affiliate_area_page_id();

	$affiliate_area_page_url = get_permalink( $affiliate_area_page_id );

	if ( ! empty( $tab )
		&& in_array( $tab, array( 'urls', 'stats', 'graphs', 'referrals', 'visits', 'creatives', 'settings' ) )
	) {
		$affiliate_area_page_url = add_query_arg( array( 'tab' => $tab ), $affiliate_area_page_url );
	}

	/**
	 * Filter the Affilate Area page URL.
	 *
	 * @since 1.8
	 *
	 * @param string $affiliate_area_page_url Page URL.
	 * @param int    $affiliate_area_page_id  Page ID.
	 * @param string $tab                     Page tab (if specified).
	 */
	return apply_filters( 'affwp_affiliate_area_page_url', $affiliate_area_page_url, $affiliate_area_page_id, $tab );
}

/**
 * Retrieves the active Affiliate Area tab slug.
 *
 * @since 1.8.1
 *
 * @return string Active tab if valid, empty string otherwise.
 */
function affwp_get_active_affiliate_area_tab() {
	$active_tab = ! empty( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : '';

	/**
	 * Filters the Affiliate Area tabs list.
	 *
	 * @since 1.8.1
	 *
	 * @param array $tabs Array of tabs.
	 */
	$tabs = apply_filters( 'affwp_affiliate_area_tabs', array(
		'urls', 'stats', 'graphs', 'referrals',
		'visits', 'creatives', 'settings'
	) );

	// If the tab can't be shown, remove it from play.
	foreach ( $tabs as $index => $tab ) {
		if ( false === affwp_affiliate_area_show_tab( $tab ) ) {
			unset( $tabs[ $index ] );
		}
	}

	if ( in_array( $active_tab, $tabs ) ) {
		$active_tab = $active_tab;
	} elseif ( ! empty( $tabs ) ) {
		$active_tab = reset( $tabs );
	} else {
		$active_tab = '';
	}

	return $active_tab;
}
