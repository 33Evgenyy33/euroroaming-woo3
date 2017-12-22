<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.1
 */
class ACA_WC_Column_Product_ProductType extends AC_Column
	implements ACP_Column_SortingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'column-wc-product_type' );
		$this->set_label( __( 'Product type', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	// Display

	public function get_value( $post_id ) {
		return $this->get_product_type_label( $post_id );
	}

	public function get_raw_value( $post_id ) {

		if ( $terms = wp_get_object_terms( $post_id, 'product_type' ) ) {
			$product_type = sanitize_title( current( $terms )->name );
		}
		else {
			$product_type = apply_filters( 'default_product_type', 'simple' );
		}

		return $product_type;
	}

	// Pro

	public function sorting() {
		return new ACA_WC_Sorting_Product_ProductType( $this );
	}

	public function filtering() {
		$model = new ACP_Filtering_Model_Delegated( $this );
		$model->set_dropdown_attr_id( 'dropdown_product_type' );

		return $model;
	}

	// Common

	public function get_product_type_label( $post_id ) {
		$product_type = $this->get_raw_value( $post_id );

		$types = wc_get_product_types();

		return isset( $types[ $product_type ] ) ? $types[ $product_type ] : $product_type;
	}

}
