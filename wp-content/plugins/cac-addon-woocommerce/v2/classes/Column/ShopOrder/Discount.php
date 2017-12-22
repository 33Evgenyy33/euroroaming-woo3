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

	// Display

	public function get_value( $post_id ) {
		$order = new WC_Order( $post_id );

		if ( ! ac_addon_wc()->is_woocommerce_version_gte( '2.3' ) ) {
			return $order->get_order_discount_to_display();
		}

		return $order->get_discount_to_display();
	}

	public function get_raw_value( $post_id ) {
		$order = new WC_Order( $post_id );

		if ( ! ac_addon_wc()->is_woocommerce_version_gte( '2.3' ) ) {
			return $order->get_order_discount();
		}

		return $order->get_total_discount();
	}

	// Meta

	public function get_meta_key() {
		return '_cart_discount';
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
