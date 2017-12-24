<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.3
 */
class ACA_WC_Column_User_CouponsUsed extends AC_Column {

	public function __construct() {
		$this->set_type( 'column-wc-user_coupons_used' );
		$this->set_label( __( 'Coupons Used', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	// Display

	public function get_value( $user_id ) {
		$coupons = array();

		foreach ( ac_addon_wc_helper()->get_orders_by_user( $user_id ) as $order ) {
			foreach ( $order->get_used_coupons() as $coupon ) {
				$coupons[] = ac_helper()->html->link( get_edit_post_link( $order->get_id() ), $coupon, array( 'tooltip' => 'order: #' . $order->get_id() ) );
			}
		}

		return implode( ' | ', $coupons );
	}

	/**
	 * @param int $user_id
	 *
	 * @return int Count
	 */
	public function get_raw_value( $user_id ) {
		$coupons = array();

		foreach ( ac_addon_wc_helper()->get_orders_by_user( $user_id ) as $order ) {
			$coupons = array_merge( $coupons, $order->get_used_coupons() );
		}

		return count( $coupons );
	}

}
