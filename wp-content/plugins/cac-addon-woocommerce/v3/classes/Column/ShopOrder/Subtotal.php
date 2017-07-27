<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Column_ShopOrder_Subtotal extends AC_Column {

	public function __construct() {
		$this->set_type( 'column-wc-order-subtotal' );
		$this->set_label( __( 'Subtotal', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	// Display

	public function get_value( $post_id ) {
		$order = new WC_Order( $post_id );

		return $order->get_subtotal_to_display();
	}

}
