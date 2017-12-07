<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce order title (default column) exportability model
 *
 * @since NEWVERSION
 */
class ACA_WC_Export_ShopOrder_Order extends ACP_Export_Model {

	public function get_value( $id ) {
		$order = wc_get_order( $id );

		$first_name = $order->get_billing_first_name();
		$last_name = $order->get_billing_last_name();
		$company = $order->get_billing_company();

		if ( $order->get_customer_id() ) {
			$user = get_user_by( 'id', $order->get_customer_id() );
			$name = $user->display_name;
		} elseif ( $first_name || $last_name ) {
			$name = $first_name . ' ' . $last_name;
		} elseif ( $company ) {
			$name = $company;
		} else {
			$name = __( 'guest', 'woocommerce' );
		}

		return sprintf( '%d (%s)', $order->get_order_number(), $name );
	}

}
