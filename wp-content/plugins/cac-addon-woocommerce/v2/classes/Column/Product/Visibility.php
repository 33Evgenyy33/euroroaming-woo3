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

	public function get_value( $post_id ) {
		$options = $this->get_visibility_options();
		$key = $this->get_product_visibility( $post_id );

		if ( ! isset( $options[ $key ] ) ) {
			return false;
		}

		return $options[ $key ];
	}

	public function get_raw_value( $post_id ) {
		return $this->get_product_visibility( $post_id );
	}

	// Pro

	public function editing() {
		return new ACA_WC_Editing_Product_Visibility( $this );
	}

	public function filtering() {
		return new ACA_WC_Filtering_Product_Visibility( $this );
	}

	public function sorting() {
		return new ACA_WC_Sorting_Product_Visibility( $this );
	}

	// Common

	public function get_visibility_options() {
		return apply_filters( 'woocommerce_product_visibility_options', array(
			'visible' => __( 'Catalog/search', 'woocommerce' ),
			'catalog' => __( 'Catalog', 'woocommerce' ),
			'search'  => __( 'Search', 'woocommerce' ),
			'hidden'  => __( 'Hidden', 'woocommerce' ),
		) );
	}

	private function get_product_visibility( $post_id ) {
		$product = wc_get_product( $post_id );

		return $product ? $product->visibility : false;
	}

}
