<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.0
 */
class ACA_WC_Column_ShopCoupon_Orders extends AC_Column {

	public function __construct() {
		$this->set_type( 'column-wc-coupon_orders' );
		$this->set_label( __( 'Orders', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_value( $id ) {
		$order_ids = ac_addon_wc_helper()->get_order_ids_by_coupon_id( $id );

		if ( ! $order_ids ) {
			return false;
		}

		$values = array();
		foreach ( $order_ids as $id ) {
			$values[] = ac_helper()->html->link( get_edit_post_link( $id ), $id );
		}

		return ac_helper()->html->more( $values, 5 );
	}

}
