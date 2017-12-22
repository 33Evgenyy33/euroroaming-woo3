<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Column_Product_Weight extends AC_Column_Meta
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'column-wc-weight' );
		$this->set_label( __( 'Weight', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	// Meta

	public function get_meta_key() {
		return '_weight';
	}

	// Display

	public function get_value( $post_id ) {
		$weight = wc_get_product( $post_id )->get_weight();

		if ( ! $weight ) {
			return $this->get_empty_char();
		}

		return wc_format_weight( $weight );

	}

	// Conditional

	public function is_valid() {
		return function_exists( 'wc_product_weight_enabled' ) && wc_product_weight_enabled();
	}

	// Pro

	public function sorting() {
		$model = new ACP_Sorting_Model_Meta( $this );
		$model->set_data_type( 'numeric' );

		return $model;
	}

	public function editing() {
		return new ACA_WC_Editing_Product_Weight( $this );
	}

	public function filtering() {
		return new ACA_WC_Filtering_Numeric( $this );
	}

}
