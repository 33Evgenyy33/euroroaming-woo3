<?php
/**
 * 'Payouts' Admin
 *
 * @package    AffiliateWP\Admin\Payouts
 * @copyright  Copyright (c) 2014, Pippin Williamson
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.9
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/payouts/screen-options.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/payouts/class-list-table.php';

function affwp_payouts_admin() {

	$action = null;

	if ( isset( $_GET['action2'] ) && '-1' !== $_GET['action2'] ) {
		$action = $_GET['action2'];
	} elseif ( isset( $_GET['action'] ) && '-1' !== $_GET['action'] ) {
		$action = $_GET['action'];
	}

	if ( 'view_payout' === $action ) {
		include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/payouts/view.php';
	} else {

		$payouts_table = new AffWP_Payouts_Table();
		$payouts_table->prepare_items();
?>
		<div class="wrap">
			<h1>
				<?php _e( 'Payouts', 'affiliate-wp' ); ?>

				<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'affiliate-wp-referrals' ) ) ); ?>" class="page-title-action"><?php _e( 'Manage Referrals', 'affiliate-wp' ); ?></a>
			</h1>
			<?php
			/**
			 * Fires at the top of the Payouts page (outside the form element).
			 *
			 * @since 1.9
			 */
			do_action( 'affwp_payouts_page_top' );
			?>
			<form id="affwp-payouts-filter" method="get" action="<?php echo admin_url( 'admin.php?page=affiliate-wp-payouts' ); ?>">
				<?php $payouts_table->search_box( __( 'Search', 'affiliate-wp' ), 'affwp-payouts' ); ?>

				<input type="hidden" name="page" value="affiliate-wp-payouts" />

				<?php $payouts_table->views(); ?>
				<?php $payouts_table->display(); ?>
			</form>
			<?php
			/**
			 * Fires at the bottom of the Payouts page (outside the form element).
			 *
			 * @since 1.9
			 */
			do_action( 'affwp_payouts_page_bottom' );
			?>
		</div>
<?php

	}

}
