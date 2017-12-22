<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ACA_WC_Column_Product_Dimensions extends AC_Column
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Export_Column {

	public function __construct() {
		$this->set_type( 'column-wc-dimensions' );
		$this->set_label( __( 'Dimensions', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	// Availability

	public function is_valid() {
		return function_exists( 'wc_product_dimensions_enabled' ) && wc_product_dimensions_enabled();
	}

	// Display

	public function get_value( $post_id ) {
		return $this->human_readable_surface( $this->get_dimensions( $post_id ) );
	}

	public function get_raw_value( $post_id ) {
		return $this->get_dimensions( $post_id );
	}

	// Pro

	public function editing() {
		return new ACA_WC_Editing_Product_Dimensions( $this );
	}

	public function sorting() {
		return new ACA_WC_Sorting_Product_Dimensions( $this );
	}

	// Common

	public function get_dimensions( $post_id ) {
		$product = wc_get_product( $post_id );

		if ( $product->is_virtual() ) {
			return false;
		}

		$dimensions = array(
			'length' => $product->get_length(),
			'width'  => $product->get_width(),
			'height' => $product->get_height(),
		);

		return $dimensions;
	}

	private function get_dimension_label( $dimension ) {
		$labels = array(
			'length' => __( 'Length', 'codepress-admin-columns' ),
			'width'  => __( 'Width', 'codepress-admin-columns' ),
			'height' => __( 'Height', 'codepress-admin-columns' ),
		);

		if ( ! isset( $labels[ $dimension ] ) ) {
			return false;
		}

		return $labels[ $dimension ];
	}

	public function dimensions_used( $dimensions ) {
		$values = array();
		foreach ( array( 'length', 'width', 'height' ) as $d ) {
			if ( ! empty( $dimensions[ $d ] ) ) {
				$label = $this->get_dimension_label( $d );
				$values[ $label ] = $dimensions[ $d ];
			}
		}

		return $values;
	}

	private function human_readable_surface( $dimensions ) {
		if ( empty( $dimensions ) ) {
			return false;
		}

		$values = $this->dimensions_used( $dimensions );

		if ( ! $values ) {
			return false;
		}

		return implode( ' x ', $values ) . ' ' . $this->get_dimension_unit();
	}

	private function get_dimension_unit() {
		return get_option( 'woocommerce_dimension_unit' );
	}

	public function export() {
		return new ACP_Export_Model_StrippedValue( $this );
	}

}
