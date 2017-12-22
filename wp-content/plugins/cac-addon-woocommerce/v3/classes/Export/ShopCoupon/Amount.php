<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce coupon amount (default column) exportability model
 *
 * @since 2.2.1
 */
class ACA_WC_Export_ShopCoupon_Amount extends ACP_Export_Model {

	public function get_value( $id ) {
		$coupon = new WC_Coupon( $id );

		return $coupon->get_amount();
	}

}
