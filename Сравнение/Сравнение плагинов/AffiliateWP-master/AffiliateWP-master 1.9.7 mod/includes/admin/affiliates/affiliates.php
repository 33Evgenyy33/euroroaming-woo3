<?php
/**
 * Affiiates Admin
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Affiliates
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/affiliates/screen-options.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/affiliates/class-list-table.php';

function affwp_affiliates_admin() {

	$action = null;

	if ( isset( $_GET['action2'] ) && '-1' !== $_GET['action2'] ) {
		$action = $_GET['action2'];
	} elseif ( isset( $_GET['action'] ) && '-1' !== $_GET['action'] ) {
		$action = $_GET['action'];
	}

	if ( 'view_affiliate' === $action ) {

		include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/affiliates/view.php';

	} elseif ( 'add_affiliate' === $action ) {

		include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/affiliates/new.php';

	} elseif ( 'edit_affiliate' === $action ) {

		include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/affiliates/edit.php';

	} elseif ( 'review_affiliate' === $action ) {

		include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/affiliates/review.php';

	} elseif ( 'delete' === $action ) {

		include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/affiliates/delete.php';

	} else {

		$affiliates_table = new AffWP_Affiliates_Table();
		$affiliates_table->prepare_items();
?>
		<div class="wrap">
			<h1>
				<?php _e( 'Affiliates', 'affiliate-wp' ); ?>
				<a href="<?php echo esc_url( add_query_arg( array( 'affwp_notice' => false, 'action' => 'add_affiliate' ) ) ); ?>" class="page-title-action"><?php _e( 'Add New', 'affiliate-wp' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'affiliate-wp-reports', 'tab' => 'affiliates' ) ) ); ?>" class="page-title-action"><?php _ex( 'Reports', 'affiliates', 'affiliate-wp' ); ?></a>
			</h1>
			<?php do_action( 'affwp_affiliates_page_top' ); ?>
			<form id="affwp-affiliates-filter" method="get" action="<?php echo admin_url( 'admin.php?page=affiliate-wp' ); ?>">
				<?php $affiliates_table->search_box( __( 'Search', 'affiliate-wp' ), 'affwp-affiliates' ); ?>

				<input type="hidden" name="page" value="affiliate-wp-affiliates" />

				<?php $affiliates_table->views() ?>
				<?php $affiliates_table->display() ?>
			</form>
			<?php do_action( 'affwp_affiliates_page_bottom' ); ?>
		</div>
<?php

	}

}
