<?php $active_tab = ! empty( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'urls'; ?>

<div id="affwp-affiliate-dashboard">

	<?php if ( 'pending' == affwp_get_affiliate_status( affwp_get_affiliate_id() ) ) : ?>

		<p class="affwp-notice"><?php _e( 'Your affiliate account is pending approval', 'affiliate-wp' ); ?></p>

	<?php elseif ( 'inactive' == affwp_get_affiliate_status( affwp_get_affiliate_id() ) ) : ?>

		<p class="affwp-notice"><?php _e( 'Your affiliate account is not active', 'affiliate-wp' ); ?></p>

	<?php elseif ( 'rejected' == affwp_get_affiliate_status( affwp_get_affiliate_id() ) ) : ?>

		<p class="affwp-notice"><?php _e( 'Your affiliate account request has been rejected', 'affiliate-wp' ); ?></p>

	<?php endif; ?>

	<?php if ( 'active' == affwp_get_affiliate_status( affwp_get_affiliate_id() ) ) : ?>

		<?php do_action( 'affwp_affiliate_dashboard_top', affwp_get_affiliate_id() ); ?>

		<?php if ( ! empty( $_GET['affwp_notice'] ) && 'profile-updated' == $_GET['affwp_notice'] ) : ?>

			<p class="affwp-notice"><?php _e( 'Your affiliate profile has been updated', 'affiliate-wp' ); ?></p>

		<?php endif; ?>

		<?php do_action( 'affwp_affiliate_dashboard_notices', affwp_get_affiliate_id() ); ?>

		<ul id="affwp-affiliate-dashboard-tabs">

			<?php echo affwp_tab( 'urls', __( 'Affiliate URLs', 'affiliate-wp' ) ); ?>

			<?php echo affwp_tab( 'stats', __( 'Statistics', 'affiliate-wp' ) ); ?>

			<?php echo affwp_tab( 'graphs', __( 'Graphs', 'affiliate-wp' ) ); ?>

			<?php echo affwp_tab( 'referrals', __( 'Referrals', 'affiliate-wp' ) ); ?>

			<?php echo affwp_tab( 'visits', __( 'Visits', 'affiliate-wp' ) ); ?>

			<?php echo affwp_tab( 'creatives', __( 'Creatives', 'affiliate-wp' ) ); ?>

			<?php echo affwp_tab( 'settings', __( 'Settings', 'affiliate-wp' ) ); ?>

			<?php do_action( 'affwp_affiliate_dashboard_tabs', affwp_get_affiliate_id(), $active_tab ); ?>
		</ul>

		<?php
			if ( apply_filters( 'affwp_dashboard_tabs', true, $active_tab ) ) {
				affiliate_wp()->templates->get_template_part( 'dashboard-tab', $active_tab );
			}
		?>

		<?php do_action( 'affwp_affiliate_dashboard_bottom', affwp_get_affiliate_id() ); ?>

	<?php endif; ?>


</div>
