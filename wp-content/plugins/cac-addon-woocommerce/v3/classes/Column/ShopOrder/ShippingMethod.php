<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.4
 */
class ACA_WC_Column_ShopOrder_ShippingMethod extends AC_Column
	implements ACP_Column_SortingInterface, ACP_Column_FilteringInterface, ACP_Export_Column {

	public function __construct() {
		$this->set_group( 'woocommerce' );
		$this->set_type( 'column-wc-order_shipping_method' );
		$this->set_label( __( 'Shipping Method', 'woocommerce' ) );
	}

	// Display

	public function get_value( $order_id ) {
		$order = new WC_Order( $order_id );

		$value = $order->get_shipping_method();

		if ( ! $value ) {
			return $this->get_empty_char();
		}

		return $value;
	}

	// Pro

	public function filtering() {
		return new ACA_WC_Filtering_ShopOrder_ShippingMethod( $this );
	}

	public function sorting() {
		return new ACP_Sorting_Model( $this );
	}

	public function export() {
		return new ACP_Export_Model_StrippedValue( $this );
	}

}
