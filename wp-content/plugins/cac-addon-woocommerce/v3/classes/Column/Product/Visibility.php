<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.2
 */
class ACA_WC_Column_Product_Visibility extends AC_Column_Meta
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'column-wc-visibility' );
		$this->set_label( __( 'Visiblity', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		return '_visibility';
	}

	// Display

	public function get_value( $product_id ) {
		$options = wc_get_product_visibility_options();

		$key = $this->get_raw_value( $product_id );

		if ( ! isset( $options[ $key ] ) ) {
			return false;
		}

		return $options[ $key ];
	}

	public function get_raw_value( $product_id ) {
		return wc_get_product( $product_id )->get_catalog_visibility();
	}

	// Pro

	public function editing() {
		return new ACA_WC_Editing_Product_Visibility( $this );
	}

	public function filtering() {
		return new ACA_WC_Filtering_Product_Visibility( $this );
	}

	public function sorting() {
		return new ACP_Sorting_Model_Value( $this );
	}

}
