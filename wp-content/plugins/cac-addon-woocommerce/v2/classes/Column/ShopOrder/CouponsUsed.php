<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.3
 */
class ACA_WC_Column_ShopOrder_CouponsUsed extends AC_Column_Meta
	implements ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'column-wc-order_coupons_used' );
		$this->set_label( __( 'Coupons Used', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	// Meta

	public function get_meta_key() {
		return '_recorded_coupon_usage_counts';
	}

	// Display

	public function get_value( $post_id ) {
		$order = new WC_Order( $post_id );

		if ( ! $order ) {
			return false;
		}

		$used_coupons = $order->get_used_coupons();

		if ( ! $used_coupons ) {
			return false;
		}

		$coupons = array();
		foreach ( $used_coupons as $code ) {
			$coupons[] = ac_helper()->html->link( get_edit_post_link( ac_addon_wc_helper()->get_coupon_id_from_code( $code ) ), $code );
		}

		return implode( ' | ', $coupons );
	}

	// Pro

	public function filtering() {
		return new ACA_WC_Filtering_ShopOrder_CouponUsed( $this );
	}

}
