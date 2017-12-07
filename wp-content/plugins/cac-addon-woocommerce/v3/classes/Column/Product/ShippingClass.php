<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.1
 */
class ACA_WC_Column_Product_ShippingClass extends AC_Column
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface, ACP_Export_Column {

	public function __construct() {
		$this->set_type( 'column-wc-shipping_class' );
		$this->set_label( __( 'Shipping Class', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_taxonomy() {
		return 'product_shipping_class';
	}

	// Display

	public function get_value( $post_id ) {
		$term = get_term_by( 'id', $this->get_raw_value( $post_id ), $this->get_taxonomy() );

		return ac_helper()->taxonomy->get_term_display_name( $term );
	}

	public function get_raw_value( $post_id ) {
		return wc_get_product( $post_id )->get_shipping_class_id();
	}

	// Pro

	public function sorting() {
		return new ACP_Sorting_Model_Post_Taxonomy( $this );
	}

	public function editing() {
		return new ACA_WC_Editing_Product_ShippingClass( $this );
	}

	public function filtering() {
		return new ACP_Filtering_Model_Post_Taxonomy( $this );
	}

	public function export() {
		return new ACP_Export_Model_StrippedValue( $this );
	}

}
