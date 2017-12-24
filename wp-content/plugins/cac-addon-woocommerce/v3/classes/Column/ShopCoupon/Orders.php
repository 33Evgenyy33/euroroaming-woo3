<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.0
 */
class ACA_WC_Column_ShopCoupon_Orders extends AC_Column
	implements AC_Column_AjaxValue, ACP_Column_SortingInterface, ACP_Export_Column {

	public function __construct() {
		$this->set_type( 'column-wc-coupon_orders' );
		$this->set_label( __( 'Orders', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_value( $id ) {
		$order_ids = $this->get_raw_value( $id );

		if ( ! $order_ids ) {
			return $this->get_empty_char();
		}

		$count = sprintf( _n( '%s item', '%s items', count( $order_ids ) ), count( $order_ids ) );

		return ac_helper()->html->get_ajax_toggle_box_link( $id, $count, $this->get_name() );
	}

	public function get_raw_value( $id ) {
		return ac_addon_wc_helper()->get_order_ids_by_coupon_id( $id );
	}

	public function get_ajax_value( $id ) {
		$values = array();
		foreach ( ac_addon_wc_helper()->get_order_ids_by_coupon_id( $id ) as $id ) {
			$values[] = ac_helper()->html->link( get_edit_post_link( $id ), $id );
		}

		return implode( ', ', $values );
	}

	public function sorting() {
		return new ACP_Sorting_Model( $this );
	}

	public function export() {
		return new ACA_WC_Export_ShopCoupon_Orders( $this );
	}

}
