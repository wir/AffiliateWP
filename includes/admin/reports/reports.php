<?php
/**
 * Reports Admin
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Reports
 * @copyright   Copyright (c) 2016, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

include      AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/screen-options.php';
include      AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/reports-functions.php';
include      AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/class-reports-metabox.php';

function affwp_reports_admin() {

	$active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET['tab'], affwp_get_reports_tabs() ) ? $_GET[ 'tab' ] : 'overview';

?>
	<div class="wrap">

		<?php do_action( 'affwp_reports_page_top' ); ?>

		<h2 class="nav-tab-wrapper">
			<?php
			foreach( affwp_get_reports_tabs() as $tab_id => $tab_name ) {

				$tab_url = add_query_arg( array(
					'settings-updated' => false,
					'tab'              => $tab_id,
					'affwp_notice'     => false
				) );

				$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

				echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
					echo esc_html( $tab_name );
				echo '</a>';
			}
			?>
		</h2>

		<?php do_action( 'affwp_reports_page_middle' ); ?>

		<div id="tab_container">
			<?php do_action( 'affwp_reports_tab_' . $active_tab ); ?>
		</div><!-- #tab_container-->

		<?php do_action( 'affwp_reports_page_bottom' ); ?>

	</div>
<?php
}

/**
 * Retrieve reports tabs
 *
 * @since 1.1
 * @return array $tabs
 */
function affwp_get_reports_tabs() {

	$tabs                  = array();
	$tabs['overview']      = __( 'Overview', 'affiliate-wp' );
	$tabs['affiliates']    = __( 'Affiliates', 'affiliate-wp' );
	$tabs['referrals']     = __( 'Referrals', 'affiliate-wp' );
	$tabs['visits']        = __( 'Visits', 'affiliate-wp' );
	$tabs['exporter']      = __( 'Exporter', 'affiliate-wp' );

	return apply_filters( 'affwp_reports_tabs', $tabs );
}

/**
 * Display the overview reports tab
 *
 * @since 1.8
 * @return void
 */
function affwp_reports_tab_overview() {

	$affwp_reports_earnings_today = affiliate_wp()->referrals->paid_earnings( 'today' );

?>
	<div id="dashboard-widgets" class="metabox-holder reports-metabox-holder">
		<div id="postbox-container-1" class="postbox-container">
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				<div class="postbox">
					<button type="button" class="handlediv button-link" aria-expanded="true">
						<span class="screen-reader-text">
							Toggle panel: Referrals and Earnings
						</span>
						<span class="toggle-indicator" aria-hidden="false"></span>
					</button>
					<h2 class="hndle ui-sortable-handle">
						<span>
							Referrals and Earnings
						</span>
					</h2>
					<div class="inside">
						<div class="main">
							<h2>
								<?php echo $affwp_reports_earnings_today . __(' earned today.', 'affiliate-wp' ); ?>
							</h2>
							<table class="affwp_table">

								<thead>

									<tr>

										<th><?php _e( 'Paid Earnings', 'affiliate-wp' ); ?></th>
										<th><?php _e( 'Paid Earnings This Month', 'affiliate-wp' ); ?></th>
										<th><?php _e( 'Paid Earnings Today', 'affiliate-wp' ); ?></th>

									</tr>

								</thead>

								<tbody>

									<tr>
										<td><?php echo affiliate_wp()->referrals->paid_earnings(); ?></td>
										<td><?php echo affiliate_wp()->referrals->paid_earnings( 'month' ); ?></td>
										<td><?php echo affiliate_wp()->referrals->paid_earnings( 'today' ); ?></td>
									</tr>

								</tbody>

							</table>

							<table class="affwp_table">

								<thead>

									<tr>

										<th><?php _e( 'Unpaid Referrals', 'affiliate-wp' ); ?></th>
										<th><?php _e( 'Unpaid Referrals This Month', 'affiliate-wp' ); ?></th>
										<th><?php _e( 'Unpaid Referrals Today', 'affiliate-wp' ); ?></th>

									</tr>

								</thead>

								<tbody>

									<tr>
										<td><?php echo affiliate_wp()->referrals->unpaid_count(); ?></td>
										<td><?php echo affiliate_wp()->referrals->unpaid_count( 'month' ); ?></td>
										<td><?php echo affiliate_wp()->referrals->unpaid_count( 'today' ); ?></td>
									</tr>

								</tbody>

							</table>
							<table class="affwp_table">

								<thead>

									<tr>

										<th><?php _e( 'Unpaid Earnings', 'affiliate-wp' ); ?></th>
										<th><?php _e( 'Unpaid Earnings This Month', 'affiliate-wp' ); ?></th>
										<th><?php _e( 'Unpaid Earnings Today', 'affiliate-wp' ); ?></th>

									</tr>

								</thead>

								<tbody>

									<tr>
										<td><?php echo affiliate_wp()->referrals->unpaid_earnings(); ?></td>
										<td><?php echo affiliate_wp()->referrals->unpaid_earnings( 'month' ); ?></td>
										<td><?php echo affiliate_wp()->referrals->unpaid_earnings( 'today' ); ?></td>
									</tr>

								</tbody>

							</table>

						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="postbox-container-2" class="postbox-container">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<div class="postbox">
					<button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Top Affiliates</span><span class="toggle-indicator" aria-hidden="false"></span></button>
					<h2 class="hndle ui-sortable-handle"><span>Top Affiliates</span></h2>
					<div class="inside">
						<div class="main">
							<?php $highest_earner = 'Joey Joe Joe'; ?>
							<h2>
								<?php echo  $highest_earner . __( ' is your highest-earning affiliate.', 'affiliate-wp' ); ?>
							</h2>
							<hr />
							<?php echo __( 'Top five affiliates:', 'affiliate-wp' ); ?>
							<?php $affiliates = affiliate_wp()->affiliates->get_affiliates( apply_filters( 'affwp_overview_most_valuable_affiliates', array( 'number' => 5, 'orderby' => 'earnings', 'order' => 'DESC' ) ) ); ?>
							<table class="affwp_table">

								<thead>

									<tr>
										<th><?php _e( 'Affiliate', 'affiliate-wp' ); ?></th>
										<th><?php _e( 'Earnings', 'affiliate-wp' ); ?></th>
										<th><?php _e( 'Referrals', 'affiliate-wp' ); ?></th>
										<th><?php _e( 'Visits', 'affiliate-wp' ); ?></th>
									</tr>

								</thead>

								<tbody>
								<?php if( $affiliates ) : ?>
									<?php foreach( $affiliates as $affiliate  ) : ?>
										<tr>
											<td><?php echo affiliate_wp()->affiliates->get_affiliate_name( $affiliate->affiliate_id ); ?></td>
											<td><?php echo affwp_currency_filter( $affiliate->earnings ); ?></td>
											<td><?php echo absint( $affiliate->referrals ); ?></td>
											<td><?php echo absint( $affiliate->visits ); ?></td>
										</tr>
									<?php endforeach; ?>
								<?php else : ?>
									<tr>
										<td colspan="4"><?php _e( 'No registered affiliates', 'affiliate-wp' ); ?></td>
									</tr>
								<?php endif; ?>
								</tbody>

							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="postbox-container-2" class="postbox-container">
			<div id="side-sortables" class="meta-box-sortables ui-sortable">
				<div class="postbox">
					<button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Top Performers</span><span class="toggle-indicator" aria-hidden="false"></span></button>
					<h2 class="hndle ui-sortable-handle"><span>Top Performers</span></h2>
					<div class="inside">
						<div class="main">
							<h3>
								<?php echo __( 'References', 'affiliate-wp' ); ?>
							</h3>

							<?php echo affwp_get_referrals_by_reference(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	$graph = new Affiliate_WP_Reports_Overview_Graph;
	$graph->set( 'x_mode', 'time' );
	$graph->display();

}

add_action( 'affwp_reports_tab_overview', 'affwp_reports_tab_overview' );

/**
 * Display the reports tab
 * Contains WP_List_Table view of general affiliate data
 *
 * @since 1.8
 * @return void
 */
function affwp_reports_tab_affiliates() {

	$reports_table = new AffWP_Reports_Table();
	$reports_table->prepare_items();
	$reports_table->views();
	$reports_table->display();
}
add_action('affwp_reports_tab_affiliates','affwp_reports_tab_affiliates' );

/**
 * Display the referrals reports tab
 *
 * @since 1.1
 * @return void
 */
function affwp_reports_tab_referrals() {
?>
	<table id="affwp_total_earnings" class="affwp_table">

		<thead>

			<tr>

				<th><?php _e( 'Paid Earnings', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Paid Earnings This Month', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Paid Earnings Today', 'affiliate-wp' ); ?></th>

			</tr>

		</thead>

		<tbody>

			<tr>
				<td><?php echo affiliate_wp()->referrals->paid_earnings(); ?></td>
				<td><?php echo affiliate_wp()->referrals->paid_earnings( 'month' ); ?></td>
				<td><?php echo affiliate_wp()->referrals->paid_earnings( 'today' ); ?></td>
			</tr>

		</tbody>

	</table>

	<table id="affwp_unpaid_earnings" class="affwp_table">

		<thead>

			<tr>

				<th><?php _e( 'Unpaid Earnings', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Unpaid Earnings This Month', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Unpaid Earnings Today', 'affiliate-wp' ); ?></th>

			</tr>

		</thead>

		<tbody>

			<tr>
				<td><?php echo affiliate_wp()->referrals->unpaid_earnings(); ?></td>
				<td><?php echo affiliate_wp()->referrals->unpaid_earnings( 'month' ); ?></td>
				<td><?php echo affiliate_wp()->referrals->unpaid_earnings( 'today' ); ?></td>
			</tr>

		</tbody>

	</table>

	<table id="affwp_unpaid_counts" class="affwp_table">

		<thead>

			<tr>

				<th><?php _e( 'Unpaid Referrals', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Unpaid Referrals This Month', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Unpaid Referrals Today', 'affiliate-wp' ); ?></th>

			</tr>

		</thead>

		<tbody>

			<tr>
				<td><?php echo affiliate_wp()->referrals->unpaid_count(); ?></td>
				<td><?php echo affiliate_wp()->referrals->unpaid_count( 'month' ); ?></td>
				<td><?php echo affiliate_wp()->referrals->unpaid_count( 'today' ); ?></td>
			</tr>

		</tbody>

	</table>

	<?php
	$graph = new Affiliate_WP_Referrals_Graph;
	$graph->set( 'x_mode', 'time' );
	$graph->display();

}
add_action( 'affwp_reports_tab_referrals', 'affwp_reports_tab_referrals' );

/**
 * Display the visits reports tab
 *
 * @since 1.1
 * @return void
 */
function affwp_reports_tab_visits() {

	$graph = new Affiliate_WP_Visits_Graph;
	$graph->set( 'x_mode',   'time' );
	$graph->set( 'currency', false  );

?>
	<table id="affwp_total_earnings" class="affwp_table">

		<thead>

			<tr>

				<th><?php _e( 'Visits', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Successful Conversions', 'affiliate-wp' ); ?></th>
				<th><?php _e( 'Conversion Rate', 'affiliate-wp' ); ?></th>

			</tr>

		</thead>

		<tbody>

			<tr>
				<td><?php echo absint( $graph->total ); ?></td>
				<td><?php echo absint( $graph->converted ); ?></td>
				<td><?php echo $graph->get_conversion_rate(); ?>%</td>
			</tr>

		</tbody>

	</table>
<?php
	$graph->display();

}
add_action( 'affwp_reports_tab_visits', 'affwp_reports_tab_visits' );

/**
 * Display the affiliate registration reports tab
 *
 * @since 1.1
 * @return void
 */
function affwp_reports_tab_registrations() {

	require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/class-registrations-graph.php';

	$graph = new Affiliate_WP_Registrations_Graph;
	$graph->set( 'x_mode',   'time' );
	$graph->set( 'currency', false  );
	$graph->display();

}
add_action( 'affwp_reports_tab_registrations', 'affwp_reports_tab_registrations' );

/**
 * Display the affiliate reports exporter
 *
 * @since 1.8
 * @return void
 */
function affwp_reports_tab_exporter() {

	require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/reports/class-reports-exporter.php';

	$exporter = new Affiliate_WP_Reports_Exporter;
	$exporter->display();

}
add_action( 'affwp_reports_tab_exporter', 'affwp_reports_tab_exporter' );

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * AffWP_Reports_Table Class
 *
 * Renders the Affiliates table on the Affiliates page
 *
 * @since 1.8
 */
class AffWP_Reports_Table extends WP_List_Table {

	/**
	 * Default number of items to show per page
	 *
	 * @var string
	 * @since 1.8
	 */
	public $per_page = 30;

	/**
	 * Total number of affiliates found
	 *
	 * @var int
	 * @since 1.8
	 */
	public $total_count;

	/**
	 * Number of active affiliates found
	 *
	 * @var string
	 * @since 1.8
	 */
	public $active_count;

	/**
	 *  Number of inactive affiliates found
	 *
	 * @var string
	 * @since 1.8
	 */
	public $inactive_count;

	/**
	 * Number of pending affiliates found
	 *
	 * @var string
	 * @since 1.8
	 */
	public $pending_count;

	/**
	 * Number of rejected affiliates found
	 *
	 * @var string
	 * @since 1.8
	 */
	public $rejected_count;

	/**
	 * Get things started
	 *
	 * @since 1.8
	 * @uses AffWP_Reports_Table::get_affiliate_counts()
	 * @see WP_List_Table::__construct()
	 */
	public function __construct() {
		global $status, $page;

		parent::__construct( array(
			'singular'  => 'affiliate',
			'plural'    => 'affiliates',
			'ajax'      => false
		) );

		$this->get_affiliate_counts();
	}

	/**
	 * Show the search field
	 *
	 * @access public
	 * @since 1.8
	 *
	 * @param string $text Label for the search box
	 * @param string $input_id ID of the search box
	 *
	 * @return void
	 */
	public function search_box( $text, $input_id ) {
		if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
			return;

		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) )
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		if ( ! empty( $_REQUEST['order'] ) )
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		?>
		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
			<input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
			<?php submit_button( $text, 'button', false, false, array( 'ID' => 'search-submit' ) ); ?>
		</p>
	<?php
	}

	/**
	 * Retrieve the view types
	 *
	 * @access public
	 * @since 1.8
	 * @return array $views All the views available
	 */
	public function get_views() {
		$base           = admin_url( 'admin.php?page=affiliate-wp-affiliates' );

		$current        = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$total_count    = '&nbsp;<span class="count">(' . $this->total_count    . ')</span>';
		$active_count   = '&nbsp;<span class="count">(' . $this->active_count . ')</span>';
		$inactive_count = '&nbsp;<span class="count">(' . $this->inactive_count  . ')</span>';
		$pending_count  = '&nbsp;<span class="count">(' . $this->pending_count  . ')</span>';
		$rejected_count = '&nbsp;<span class="count">(' . $this->rejected_count  . ')</span>';

		$views = array(
			'all'		=> sprintf( '<a href="%s"%s>%s</a>', esc_url( remove_query_arg( 'status', $base ) ), $current === 'all' || $current == '' ? ' class="current"' : '', __('All', 'affiliate-wp') . $total_count ),
			'active'	=> sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'active', $base ) ), $current === 'active' ? ' class="current"' : '', __('Active', 'affiliate-wp') . $active_count ),
			'inactive'	=> sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'inactive', $base ) ), $current === 'inactive' ? ' class="current"' : '', __('Inactive', 'affiliate-wp') . $inactive_count ),
			'pending'	=> sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'pending', $base ) ), $current === 'pending' ? ' class="current"' : '', __('Pending', 'affiliate-wp') . $pending_count ),
			'rejected'	=> sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'rejected', $base ) ), $current === 'rejected' ? ' class="current"' : '', __('Rejected', 'affiliate-wp') . $rejected_count ),
		);

		return $views;
	}

	/**
	 * Retrieve the table columns
	 *
	 * @access public
	 * @since 1.8
	 * @return array $columns Array of all the list table columns
	 */
	public function get_columns() {
		$columns = array(
			'cb'           => '<input type="checkbox" />',
			'name'         => __( 'Name', 'affiliate-wp' ),
			'username'     => __( 'Username', 'affiliate-wp' ),
			'affiliate_id' => __( 'Affiliate ID', 'affiliate-wp' ),
			'earnings'     => __( 'Earnings', 'affiliate-wp' ),
			'rate'     	   => __( 'Rate', 'affiliate-wp' ),
			'unpaid'       => __( 'Unpaid Referrals', 'affiliate-wp' ),
			'referrals'    => __( 'Paid Referrals', 'affiliate-wp' ),
			'visits'       => __( 'Visits', 'affiliate-wp' ),
			'status'       => __( 'Status', 'affiliate-wp' ),
			'actions'      => __( 'Actions', 'affiliate-wp' ),
		);

		return apply_filters( 'affwp_reports_table_columns', $columns );
	}

	/**
	 * Retrieve the table's sortable columns
	 *
	 * @access public
	 * @since 1.8
	 * @return array Array of all the sortable columns
	 */
	public function get_sortable_columns() {
		return array(
			'name'         => array( 'name', false ),
			'username'     => array( 'username', false ),
			'affiliate_id' => array( 'affiliate_id', false ),
			'earnings'     => array( 'earnings', false ),
			'rate'         => array( 'rate', false ),
			'unpaid'       => array( 'unpaid', false ),
			'referrals'    => array( 'referrals', false ),
			'visits'       => array( 'visits', false ),
			'status'       => array( 'status', false ),
		);
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @access public
	 * @since 1.8
	 *
	 * @param array $affiliate Contains all the data of the affiliate
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 */
	function column_default( $affiliate, $column_name ) {
		switch( $column_name ){

			default:
				$value = isset( $affiliate->$column_name ) ? $affiliate->$column_name : '';
				break;
		}

		return apply_filters( 'affwp_reports_table_' . $column_name, $value );
	}

	/**
	 * Render the Name Column
	 *
	 * @access public
	 * @since 1.8
	 * @param array $affiliate Contains all the data of the affiliate
	 * @return string Data shown in the Name column
	 */
	function column_name( $affiliate ) {
		$base         = admin_url( 'admin.php?page=affiliate-wp&affiliate_id=' . $affiliate->affiliate_id );
		$row_actions  = array();
		$name         = affiliate_wp()->affiliates->get_affiliate_name( $affiliate->affiliate_id );

		if( $name ) {
			$value = sprintf( '<a href="%s">%s</a>', get_edit_user_link( $affiliate->user_id ), $name );
		} else {
			$value = __( '(user deleted)', 'affiliate-wp' );
		}

		return apply_filters( 'affwp_reports_table_name', $value, $affiliate );
	}

	/**
	 * Render the Username Column
	 *
	 * @access public
	 * @since 1.8
	 * @param array $affiliate Contains all the data of the affiliate
	 * @return string Data shown in the Username column
	 */
	function column_username( $affiliate ) {

		$row_actions = array();
		$user_info = get_userdata( $affiliate->user_id );
		$username  = $user_info->user_login;

		if ( $username ) {
			$value = $username;
		} else {
			$value = __( '(user deleted)', 'affiliate-wp' );
		}

		return apply_filters( 'affwp_reports_table_username', $value, $affiliate );

	}

	/**
	 * Render the checkbox column
	 *
	 * @access public
	 * @since 1.8
	 * @param array $affiliate Contains all the data for the checkbox column
	 * @return string Displays a checkbox
	 */
	function column_cb( $affiliate ) {
		return '<input type="checkbox" name="affiliate_id[]" value="' . absint( $affiliate->affiliate_id ) . '" />';
	}

	/**
	 * Render the earnings column
	 *
	 * @access public
	 * @since 1.8
	 * @param array $affiliate Contains all the data for the earnings column
	 * @return string earnings link
	 */
	function column_earnings( $affiliate ) {
		$value = affwp_currency_filter( affwp_format_amount( affwp_get_affiliate_earnings( $affiliate->affiliate_id ) ) );
		return apply_filters( 'affwp_reports_table_earnings', $value, $affiliate );
	}

	/**
	 * Render the earnings column
	 *
	 * @access public
	 * @since 1.8
	 * @param array $affiliate Contains all the data for the earnings column
	 * @return string earnings link
	 */
	function column_rate( $affiliate ) {
		$value = affwp_get_affiliate_rate( $affiliate->affiliate_id, true );
		return apply_filters( 'affwp_reports_table_rate', $value, $affiliate );
	}


	/**
	 * Render the unpaid referrals column
	 *
	 * @access public
	 * @since 1.7.5
	 * @param array $affiliate Contains all the data for the unpaid referrals column
	 * @return string unpaid referrals link
	 */
	function column_unpaid( $affiliate ) {
		$unpaid_count = affiliate_wp()->referrals->unpaid_count( '', $affiliate->affiliate_id );

		$value = '<a href="' . admin_url( 'admin.php?page=affiliate-wp-referrals&affiliate_id=' . $affiliate->affiliate_id . '&status=unpaid' ) . '">' . $unpaid_count . '</a>';
		return apply_filters( 'affwp_reports_table_unpaid', $value, $affiliate );
	}


	/**
	 * Render the referrals column
	 *
	 * @access public
	 * @since 1.8
	 * @param array $affiliate Contains all the data for the referrals column
	 * @return string referrals link
	 */
	function column_referrals( $affiliate ) {
		$value = '<a href="' . admin_url( 'admin.php?page=affiliate-wp-referrals&affiliate_id=' . $affiliate->affiliate_id . '&status=paid' ) . '">' . $affiliate->referrals . '</a>';
		return apply_filters( 'affwp_reports_table_referrals', $value, $affiliate );
	}

	/**
	 * Render the visits column
	 *
	 * @access public
	 * @since 1.8
	 * @param array $affiliate Contains all the data for the visits column
	 * @return string visits link
	 */
	function column_visits( $affiliate ) {
		$value = '<a href="' . admin_url( 'admin.php?page=affiliate-wp-visits&affiliate=' . $affiliate->affiliate_id ) . '">' . affwp_get_affiliate_visit_count( $affiliate->affiliate_id ) . '</a>';
		return apply_filters( 'affwp_reports_table_visits', $value, $affiliate );
	}

	/**
	 * Render the actions column
	 *
	 * @access public
	 * @since 1.8
	 * @param array $affiliate Contains all the data for the visits column
	 * @return string action links
	 */
	function column_actions( $affiliate ) {

		$row_actions['reports'] = '<a href="' . esc_url( add_query_arg( array( 'affwp_notice' => false, 'affiliate_id' => $affiliate->affiliate_id, 'action' => 'view_affiliate' ) ) ) . '">' . __( 'Reports', 'affiliate-wp' ) . '</a>';
		$row_actions['edit'] = '<a href="' . esc_url( add_query_arg( array( 'affwp_notice' => false, 'action' => 'edit_affiliate', 'affiliate_id' => $affiliate->affiliate_id ) ) ) . '">' . __( 'Edit', 'affiliate-wp' ) . '</a>';

		if ( strtolower( $affiliate->status ) == 'active' ) {
			$row_actions['deactivate'] = '<a href="' . wp_nonce_url( add_query_arg( array( 'affwp_notice' => 'affiliate_deactivated', 'action' => 'deactivate', 'affiliate_id' => $affiliate->affiliate_id ) ), 'affiliate-nonce' ) . '">' . __( 'Deactivate', 'affiliate-wp' ) . '</a>';
		} elseif( strtolower( $affiliate->status ) == 'pending' ) {
			$row_actions['review'] = '<a href="' . wp_nonce_url( add_query_arg( array( 'affwp_notice' => false, 'action' => 'review_affiliate', 'affiliate_id' => $affiliate->affiliate_id ) ), 'affiliate-nonce' ) . '">' . __( 'Review', 'affiliate-wp' ) . '</a>';
			$row_actions['accept'] = '<a href="' . wp_nonce_url( add_query_arg( array( 'affwp_notice' => 'affiliate_accepted', 'action' => 'accept', 'affiliate_id' => $affiliate->affiliate_id ) ), 'affiliate-nonce' ) . '">' . __( 'Accept', 'affiliate-wp' ) . '</a>';
			$row_actions['reject'] = '<a href="' . wp_nonce_url( add_query_arg( array( 'affwp_notice' => 'affiliate_rejected', 'action' => 'reject', 'affiliate_id' => $affiliate->affiliate_id ) ), 'affiliate-nonce' ) . '">' . __( 'Reject', 'affiliate-wp' ) . '</a>';
		} else {
			$row_actions['activate'] = '<a href="' . wp_nonce_url( add_query_arg( array( 'affwp_notice' => 'affiliate_activated', 'action' => 'activate', 'affiliate_id' => $affiliate->affiliate_id ) ), 'affiliate-nonce' ) . '">' . __( 'Activate', 'affiliate-wp' ) . '</a>';
		}

		$row_actions['delete'] = '<a href="' . wp_nonce_url( add_query_arg( array( 'action' => 'delete', 'affiliate_id' => $affiliate->affiliate_id, 'affwp_notice' => false ) ), 'affiliate-nonce' ) . '">' . __( 'Delete', 'affiliate-wp' ) . '</a>';

		$row_actions = apply_filters( 'affwp_affiliate_row_actions', $row_actions, $affiliate );

		return $this->row_actions( $row_actions, true );

	}


	/**
	 * Message to be displayed when there are no items
	 *
	 * @since 1.7.2
	 * @access public
	 */
	function no_items() {
		_e( 'No affiliates found.', 'affiliate-wp' );
	}

	/**
	 * Retrieve the bulk actions
	 *
	 * @access public
	 * @since 1.8
	 * @return array $actions Array of the bulk actions
	 */
	public function get_bulk_actions() {
		$actions = array(
			'accept'     => __( 'Accept', 'affiliate-wp' ),
			'reject'     => __( 'Reject', 'affiliate-wp' ),
			'activate'   => __( 'Activate', 'affiliate-wp' ),
			'deactivate' => __( 'Deactivate', 'affiliate-wp' ),
			'delete'     => __( 'Delete', 'affiliate-wp' )
		);

		return apply_filters( 'affwp_affilates_bulk_actions', $actions );
	}

	/**
	 * Process the bulk actions
	 *
	 * @access public
	 * @since 1.8
	 * @return void
	 */
	public function process_bulk_action() {

		if( empty( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		if( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-affiliates' ) && ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'affiliate-nonce' ) ) {
			return;
		}

		$ids = isset( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : false;

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		$ids = array_map( 'absint', $ids );

		if ( empty( $ids ) ) {
			return;
		}

		foreach ( $ids as $id ) {

			if ( 'accept' === $this->current_action() ) {
				affwp_set_affiliate_status( $id, 'active' );
			}

			if ( 'reject' === $this->current_action() ) {
				affwp_set_affiliate_status( $id, 'rejected' );
			}

			if ( 'activate' === $this->current_action() ) {
				affwp_set_affiliate_status( $id, 'active' );
			}

			if ( 'deactivate' === $this->current_action() ) {
				affwp_set_affiliate_status( $id, 'inactive' );
			}

		}

	}

	/**
	 * Retrieve the discount code counts
	 *
	 * @access public
	 * @since 1.8
	 * @return void
	 */
	public function get_affiliate_counts() {

		$search = isset( $_GET['s'] ) ? $_GET['s'] : '';

		$this->active_count   = affiliate_wp()->affiliates->count( array( 'status' => 'active', 'search' => $search ) );
		$this->inactive_count = affiliate_wp()->affiliates->count( array( 'status' => 'inactive', 'search' => $search ) );
		$this->pending_count  = affiliate_wp()->affiliates->count( array( 'status' => 'pending', 'search' => $search ) );
		$this->rejected_count = affiliate_wp()->affiliates->count( array( 'status' => 'rejected', 'search' => $search ) );
		$this->total_count    = $this->active_count + $this->inactive_count + $this->pending_count + $this->rejected_count;
	}

	/**
	 * Retrieve all the data for all the Affiliates
	 *
	 * @access public
	 * @since 1.8
	 * @return array $affiliate_data Array of all the data for the Affiliates
	 */
	public function affiliate_data() {

		$page    = isset( $_GET['paged'] )    ? absint( $_GET['paged'] ) : 1;
		$status  = isset( $_GET['status'] )   ? $_GET['status']          : '';
		$search  = isset( $_GET['s'] )        ? $_GET['s']               : '';
		$order   = isset( $_GET['order'] )    ? $_GET['order']           : 'DESC';
		$orderby = isset( $_GET['orderby'] )  ? $_GET['orderby']         : 'affiliate_id';

		$per_page = $this->get_items_per_page( 'affwp_edit_affiliates_per_page', $this->per_page );

		$affiliates   = affiliate_wp()->affiliates->get_affiliates( array(
			'number'  => $per_page,
			'offset'  => $per_page * ( $page - 1 ),
			'status'  => $status,
			'search'  => $search,
			'orderby' => sanitize_text_field( $orderby ),
			'order'   => sanitize_text_field( $order )
		) );
		return $affiliates;
	}

	/**
	 * Setup the final data for the table
	 *
	 * @access public
	 * @since 1.8
	 * @uses AffWP_Reports_Table::get_columns()
	 * @uses AffWP_Reports_Table::get_sortable_columns()
	 * @uses AffWP_Reports_Table::process_bulk_action()
	 * @uses AffWP_Reports_Table::affiliate_data()
	 * @uses WP_List_Table::get_pagenum()
	 * @uses WP_List_Table::set_pagination_args()
	 * @return void
	 */
	public function prepare_items() {
		$per_page = $this->get_items_per_page( 'affwp_edit_affiliates_per_page', $this->per_page );

		$columns = $this->get_columns();

		$hidden = array();

		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();

		$data = $this->affiliate_data();

		$current_page = $this->get_pagenum();

		$status = isset( $_GET['status'] ) ? $_GET['status'] : 'any';

		switch( $status ) {
			case 'active':
				$total_items = $this->active_count;
				break;
			case 'inactive':
				$total_items = $this->inactive_count;
				break;
			case 'pending':
				$total_items = $this->pending_count;
				break;
			case 'rejected':
				$total_items = $this->rejected_count;
				break;
			case 'any':
				$total_items = $this->total_count;
				break;
		}

		$this->items = $data;

		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page )
			)
		);
	}
}

