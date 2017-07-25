<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Column_ShopOrder_ProductCustomField extends AC_Column_CustomField {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'column-wc-order-productmeta' );
		$this->set_label( __( 'Product - Custom Field', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	// Display

	public function get_raw_value( $order_id ) {
		$values = array();

		foreach ( ac_addon_wc_helper()->get_product_ids_by_order( $order_id ) as $product_id ) {
			$values[] = parent::get_raw_value( $product_id );
		}

		return new AC_Collection( $values );
	}

	// Settings

	public function register_settings() {
		$this->add_setting( new ACA_WC_Settings_ProductMeta( $this ) );
		$this->add_setting( new AC_Settings_Column_BeforeAfter( $this ) );
	}

}
