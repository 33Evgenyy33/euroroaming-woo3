<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce coupon type (default column) exportability model
 *
 * @since 2.2.1
 */
class ACA_WC_Export_ShopCoupon_Type extends ACP_Export_Model {

	public function get_value( $id ) {
		$coupon = new WC_Coupon( $id );

		return wc_get_coupon_type( $coupon->get_discount_type() );
	}

}
