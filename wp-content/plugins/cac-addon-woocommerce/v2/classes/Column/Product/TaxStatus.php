<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.4
 */
class ACA_WC_Column_Product_TaxStatus extends AC_Column_Meta
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'column-wc-tax_status' );
		$this->set_label( __( 'Tax status', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_meta_key() {
		return '_tax_status';
	}

	// Display

	public function get_value( $post_id ) {
		$value = $this->get_raw_value( $post_id );
		$status = $this->get_tax_status();

		return isset( $status[ $value ] ) ? $status[ $value ] : $value;
	}

	// Pro

	public function filtering() {
		return new ACA_WC_Filtering_Product_TaxStatus( $this );
	}

	public function sorting() {
		return new ACP_Sorting_Model( $this );
	}

	public function editing() {
		return new ACA_WC_Editing_Product_TaxStatus( $this );
	}

	// Common

	public function get_tax_status() {
		$status = array(
			'taxable'  => __( 'Taxable', 'woocommerce' ),
			'shipping' => __( 'Shipping only', 'woocommerce' ),
			'none'     => _x( 'None', 'Tax status', 'woocommerce' ),
		);

		return $status;
	}

}
