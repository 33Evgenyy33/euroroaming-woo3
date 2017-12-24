<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.4
 */
class ACA_WC_Column_Product_TaxClass extends AC_Column_Meta
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'column-wc-tax_class' );
		$this->set_label( __( 'Tax Class', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	// Meta

	public function get_meta_key() {
		return '_tax_class';
	}

	// Display

	public function get_value( $post_id ) {
		$value = $this->get_raw_value( $post_id );

		$classes = $this->get_tax_classes();

		if ( isset( $classes[ $value ] ) ) {
			$value = $classes[ $value ];
		}

		if ( ! $value ) {
			return $this->get_empty_char();
		}

		return $value;
	}

	public function get_raw_value( $post_id ) {
		return wc_get_product( $post_id )->get_tax_class();
	}

	// Pro

	public function filtering() {
		return new ACA_WC_Filtering_Product_TaxClass( $this );
	}

	public function editing() {
		return new ACA_WC_Editing_Product_TaxClass( $this );
	}

	public function sorting() {
		return new ACP_Sorting_Model( $this );
	}

	// Common

	public function get_tax_classes() {
		$classes = array();

		foreach ( WC_Tax::get_tax_classes() as $tax_class ) {
			$classes[ WC_Tax::format_tax_rate_class( $tax_class ) ] = $tax_class;
		}

		return $classes;
	}

}
