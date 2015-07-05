<?php

class Affiliate_WP_Referrals_Graph extends Affiliate_WP_Graph {

	public $totals;

	/**
	 * Get things started
	 *
	 * @since 1.0
	 */
	public function __construct( $_data = array() ) {

		$this->totals = array(
			'pending' => 0,
			'paid' => 0,
			'unpaid' => 0,
			'rejected' => 0,
			'earnings' => '0.00',
			'unpaid_earnings' => '0.00'
		);

		if( empty( $_data ) ) {

			$this->data = $this->get_data();

		}

		// Generate unique ID
		$this->id   = md5( rand() );

		// Setup default options;
		$this->options = array(
			'y_mode'          => null,
			'y_decimals'      => 0,
			'x_decimals'      => 0,
			'y_position'      => 'right',
			'time_format'     => '%d/%b',
			'ticksize_unit'   => 'day',
			'ticksize_num'    => 1,
			'multiple_y_axes' => false,
			'bgcolor'         => '#f9f9f9',
			'bordercolor'     => '#ccc',
			'color'           => '#bbb',
			'borderwidth'     => 2,
			'bars'            => false,
			'lines'           => true,
			'points'          => true,
			'affiliate_id'    => false,
		);

	}

	/**
	 * Retrieve referral data
	 *
	 * @since 1.0
	 */
	public function get_data() {

		$paid         = array();
		$unpaid       = array();
		$rejected     = array();
		$pending      = array();

		$dates = affwp_get_report_dates();

		$start = $dates['year'] . '-' . $dates['m_start'] . '-' . $dates['day'] . ' 00:00:00';
		$end   = $dates['year_end'] . '-' . $dates['m_end'] . '-' . $dates['day_end'] . ' 23:59:59';
		$date  = array(
			'start' => $start,
			'end'   => $end
		);

		//echo '<pre>'; print_r( $date ); echo '</pre>'; exit;

		$referrals = affiliate_wp()->referrals->get_referrals( array(
			'orderby'      => 'date',
			'order'        => 'ASC',
			'date'         => $date,
			'number'       => -1,
			'affiliate_id' => $this->get( 'affiliate_id' )
		) );

		$pending[] = array( strtotime( $start ) * 1000 );
		$pending[] = array( strtotime( $end ) * 1000 );

		if( $referrals ) {
			foreach( $referrals as $referral ) {

				switch( $referral->status ) {

					case 'paid' :

						$paid[] = array( strtotime( $referral->date ) * 1000, $referral->amount );
						$this->totals['earnings'] += $referral->amount;
						$this->totals['paid'] += 1;

						break;

					case 'unpaid' :

						$unpaid[] = array( strtotime( $referral->date ) * 1000, $referral->amount );
						$this->totals['unpaid_earnings'] += $referral->amount;
						$this->totals['unpaid'] += 1;

						break;

					case 'rejected' :

						$rejected[] = array( strtotime( $referral->date ) * 1000, $referral->amount );
						$this->totals['rejected'] += 1;

						break;

					case 'pending' :

						$pending[] = array( strtotime( $referral->date ) * 1000, $referral->amount );
						$this->totals['pending'] += 1;

						break;

					default :

						break;

				}

			}
		}

		$data = array(
			__( 'Unpaid Referral Earnings', 'affiliate-wp' )   => $unpaid,
			__( 'Pending Referral Earnings', 'affiliate-wp' )  => $pending,
			__( 'Rejected Referral Earnings', 'affiliate-wp' ) => $rejected,
			__( 'Paid Referral Earnings', 'affiliate-wp' )     => $paid,
		);

		return $data;

	}

}