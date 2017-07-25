<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.1
 */
class ACA_WC_Column_Product_ShippingClass extends ACP_Column_Post_Taxonomy
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'column-wc-shipping_class' );
		$this->set_label( __( 'Shipping Class', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_taxonomy() {
		return 'product_shipping_class';
	}

	// Display

	public function get_value( $post_id ) {
		$value = false;

		if ( $term = get_term_by( 'id', $this->get_raw_value( $post_id ), $this->get_taxonomy() ) ) {
			$value = sanitize_term_field( 'name', $term->name, $term->term_id, $term->taxonomy, 'display' );
		}

		return $value;
	}

	public function get_raw_value( $post_id ) {
		$shipping_id = false;

		if ( $product = ac_addon_wc_helper()->get_product( $post_id ) ) {
			$shipping_id = $product->get_shipping_class_id();
		}

		return $shipping_id;
	}

	// Disable settings

	public function register_settings() {
		return;
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

}
