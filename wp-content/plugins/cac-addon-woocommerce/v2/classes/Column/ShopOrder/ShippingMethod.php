<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.4
 */
class ACA_WC_Column_ShopOrder_ShippingMethod extends AC_Column
	implements ACP_Column_SortingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_group( 'woocommerce' );
		$this->set_type( 'column-wc-order_shipping_method' );
		$this->set_label( __( 'Shipping Method', 'woocommerce' ) );
	}

	// Display

	public function get_value( $order_id ) {
		$order = new WC_Order( $order_id );

		return $order->get_shipping_method();
	}

	// Pro

	public function filtering() {
		return new ACA_WC_Filtering_ShopOrder_ShippingMethod( $this );
	}

	public function sorting() {
		return new ACP_Sorting_Model( $this );
	}

}
