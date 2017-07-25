<?php $active_tab = affwp_get_active_affiliate_area_tab(); ?>

<div id="affwp-affiliate-dashboard">

	<?php if ( 'pending' == affwp_get_affiliate_status( affwp_get_affiliate_id() ) ) : ?>

		<p class="affwp-notice"><?php _e( 'Your affiliate account is pending approval', 'affiliate-wp' ); ?></p>

	<?php elseif ( 'inactive' == affwp_get_affiliate_status( affwp_get_affiliate_id() ) ) : ?>

		<p class="affwp-notice"><?php _e( 'Your affiliate account is not active', 'affiliate-wp' ); ?></p>

	<?php elseif ( 'rejected' == affwp_get_affiliate_status( affwp_get_affiliate_id() ) ) : ?>

		<p class="affwp-notice"><?php _e( 'Your affiliate account request has been rejected', 'affiliate-wp' ); ?></p>

	<?php endif; ?>

	<?php if ( 'active' == affwp_get_affiliate_status( affwp_get_affiliate_id() ) ) : ?>

		<?php
		/**
		 * Fires at the top of the affiliate dashboard.
		 *
		 * @since 0.2
		 * @since 1.8.2 Added the `$active_tab` parameter.
		 *
		 * @param int|false $affiliate_id ID for the current affiliate.
		 * @param string    $active_tab   Slug for the currently-active tab.
		 */
		do_action( 'affwp_affiliate_dashboard_top', affwp_get_affiliate_id(), $active_tab );
		?>

		<?php if ( ! empty( $_GET['affwp_notice'] ) && 'profile-updated' == $_GET['affwp_notice'] ) : ?>

			<p class="affwp-notice"><?php _e( 'Your affiliate profile has been updated', 'affiliate-wp' ); ?></p>

		<?php endif; ?>

		<?php
		/**
		 * Fires inside the affiliate dashboard above the tabbed interface.
		 *
		 * @since 0.2
		 * @since 1.8.2 Added the `$active_tab` parameter.
		 *
		 * @param int|false $affiliate_id ID for the current affiliate.
		 * @param string    $active_tab   Slug for the currently-active tab.
		 */
		do_action( 'affwp_affiliate_dashboard_notices', affwp_get_affiliate_id(), $active_tab );
		?>

		<ul id="affwp-affiliate-dashboard-tabs">
			<?php if ( affwp_affiliate_area_show_tab( 'urls' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'urls' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'urls' ) ); ?>"><?php _e( 'Affiliate URLs', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'stats' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'stats' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'stats' ) ); ?>"><?php _e( 'Statistics', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'graphs' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'graphs' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'graphs' ) ); ?>"><?php _e( 'Graphs', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'referrals' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'referrals' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'referrals' ) ); ?>"><?php _e( 'Referrals', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'payouts' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'payouts' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'payouts' ) ); ?>"><?php _e( 'Payouts', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'visits' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'visits' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'visits' ) ); ?>"><?php _e( 'Visits', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'creatives' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'creatives' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'creatives' ) ); ?>"><?php _e( 'Creatives', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php if ( affwp_affiliate_area_show_tab( 'settings' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == 'settings' ? ' active' : ''; ?>">
				<a href="<?php echo esc_url( affwp_get_affiliate_area_page_url( 'settings' ) ); ?>"><?php _e( 'Settings', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

			<?php
			/**
			 * Fires immediately after core Affiliate Area tabs are output,
			 * but before the 'Log Out' tab is output (if enabled).
			 *
			 * @since 0.2
			 *
			 * @param int    $affiliate_id ID of the current affiliate.
			 * @param string $active_tab   Slug of the active tab.
			 */
			do_action( 'affwp_affiliate_dashboard_tabs', affwp_get_affiliate_id(), $active_tab );
			?>

			<?php if ( affiliate_wp()->settings->get( 'logout_link' ) ) : ?>
			<li class="affwp-affiliate-dashboard-tab">
				<a href="<?php echo esc_url( affwp_get_logout_url() ); ?>"><?php _e( 'Log out', 'affiliate-wp' ); ?></a>
			</li>
			<?php endif; ?>

		</ul>

		<?php
		if ( ! empty( $active_tab ) && affwp_affiliate_area_show_tab( $active_tab ) ) :
			affiliate_wp()->templates->get_template_part( 'dashboard-tab', $active_tab );
		endif;
		?>

		<?php
		/**
		 * Fires at the bottom of the affiliate dashboard.
		 *
		 * @since 0.2
		 * @since 1.8.2 Added the `$active_tab` parameter.
		 *
		 * @param int|false $affiliate_id ID for the current affiliate.
		 * @param string    $active_tab   Slug for the currently-active tab.
		 */
		do_action( 'affwp_affiliate_dashboard_bottom', affwp_get_affiliate_id(), $active_tab );
		?>

	<?php endif; ?>

</div>
