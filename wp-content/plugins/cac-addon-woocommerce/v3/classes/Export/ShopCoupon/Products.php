<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce coupon product IDs (default column) exportability model
 *
 * @since 2.2.1
 */
class ACA_WC_Export_ShopCoupon_Products extends ACP_Export_Model {

	public function get_value( $id ) {
		$coupon = new WC_Coupon( $id );
		$product_ids = $coupon->get_product_ids();

		if ( count( $product_ids ) < 1 ) {
			return '';
		}

		return implode( ', ', $product_ids );
	}

}
