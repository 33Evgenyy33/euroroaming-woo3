<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce coupon usage (default column) exportability model
 *
 * @since 2.2.1
 */
class ACA_WC_Export_ShopCoupon_Usage extends ACP_Export_Model {

	public function get_value( $id ) {
		$coupon = new WC_Coupon( $id );

		$usage_count = $coupon->get_usage_count();
		$usage_limit = $coupon->get_usage_limit();

		$limit_string = $usage_limit ? $usage_limit : __( 'Infinity', 'codepress-admin-columns' );

		return sprintf( '%d / %s', $usage_count, $limit_string );
	}

}
