<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_Product_ShippingClass $column
 */
class ACA_WC_Editing_Product_ShippingClass extends ACP_Editing_Model_Post_Taxonomy {

	public function __construct( ACA_WC_Column_Product_ShippingClass $column ) {
		parent::__construct( $column );
	}

	public function get_edit_value( $id ) {
		$product = wc_get_product( $id );

		if ( ! $product || ! $product->needs_shipping() ) {
			return null;
		}

		return parent::get_edit_value( $id );
	}

	public function get_view_settings() {
		$settings = parent::get_view_settings();

		$settings['type'] = 'select';
		$settings['options'] = $this->get_term_options();

		return $settings;
	}

	public function register_settings() {
		$this->column->add_setting( new ACP_Editing_Settings( $this->column ) );
	}

}
