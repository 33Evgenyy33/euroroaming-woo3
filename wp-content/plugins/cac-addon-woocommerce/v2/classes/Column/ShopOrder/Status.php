<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.1
 */
class ACA_WC_Column_ShopOrder_Status extends AC_Column
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'order_status' );
		$this->set_original( true );
	}

	// Display

	public function get_value( $id ) {
		return null;
	}

	public function get_raw_value( $post_id ) {
		$order = new WC_Order( $post_id );

		return $order->get_status();
	}

	// Pro

	public function sorting() {
		return new ACP_Sorting_Model( $this );
	}

	public function editing() {
		return new ACA_WC_Editing_ShopOrder_Status( $this );
	}

	public function filtering() {
		return new ACA_WC_Filtering_ShopOrder_Status( $this );
	}

	// Common

	public function get_order_status_options() {
		$options = array();

		if ( ac_addon_wc()->is_woocommerce_version_gte( '2.2' ) ) {
			$options = wc_get_order_statuses();
		}
		else {
			$statuses_raw = (array) get_terms( 'shop_order_status', array(
				'hide_empty' => 0,
				'orderby'    => 'id',
			) );
			foreach ( $statuses_raw as $status ) {
				$options[ $status->slug ] = $status->name;
			}
		}

		return $options;
	}

}
