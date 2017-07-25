<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.3.1
 */
class ACA_WC_Column_ShopOrder_Product extends AC_Column
	implements ACP_Column_SortingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_group( 'woocommerce' );
		$this->set_type( 'column-wc-product' );
		$this->set_label( __( 'Product', 'woocommerce' ) );
	}

	// Display

	public function get_value( $order_id ) {
		$product_ids = (array) $this->get_raw_value( $order_id );

		if ( empty( $product_ids ) ) {
			return $this->get_empty_char();
		}

		$value = $this->get_formatted_value( new AC_Collection( $product_ids ) );

		if ( $value instanceof AC_Collection ) {
			$value = $value->filter()->implode( $this->get_separator() );
		}

		return $value;
	}

	public function get_raw_value( $order_id ) {
		return ac_addon_wc_helper()->get_product_ids_by_order( $order_id );
	}

	// Pro

	public function filtering() {
		if ( in_array( $this->get_product_property(), array( 'title', 'sku' ) ) ) {
			return new ACA_WC_Filtering_ShopOrder_Product( $this );
		}

		return new ACP_Filtering_Model_Disabled( $this );
	}

	public function sorting() {
		return new ACA_WC_Sorting_ShopOrder_Product( $this );
	}

	// Settings

	public function register_settings() {
		$this->add_setting( new ACA_WC_Settings_Product( $this ) );
	}

	// Common

	public function get_product_property() {
		return $this->get_setting( 'post' )->get_value();
	}

}
