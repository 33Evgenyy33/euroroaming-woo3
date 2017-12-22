<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ACA_WC_Column_ShopOrder_Discount extends ACP_Column_Meta
	implements ACP_Column_SortingInterface {

	public function __construct() {
		$this->set_type( 'column-wc-order_discount' );
		$this->set_label( __( 'Order Discount', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	// Meta

	public function get_meta_key() {
		return '_cart_discount';
	}

	// Display

	public function get_value( $id ) {
		$order = wc_get_order( $id );

		if ( ! $order->get_total_discount() ) {
			return $this->get_empty_char();
		}

		return $order->get_discount_to_display();
	}

	public function get_raw_value( $id ) {
		$order = wc_get_order( $id );

		return $order->get_total_discount();
	}

	// Pro

	public function filtering() {
		return new ACA_WC_Filtering_Numeric( $this );
	}

	public function sorting() {
		return new ACP_Sorting_Model( $this );
	}

	public function editing() {
		return new ACP_Editing_Model_Disabled( $this );
	}
}
