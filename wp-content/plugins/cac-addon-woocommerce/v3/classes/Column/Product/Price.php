<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ACA_WC_Column_Product_Price extends AC_Column_Meta
	implements ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'price' );
		$this->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	// Meta

	public function get_meta_key() {
		return '_price';
	}

	// Pro

	public function filtering() {
		$insert_tax = get_option( 'woocommerce_prices_include_tax' );
		$tax_included = get_option( 'woocommerce_tax_display_shop' );

		if ( ( 'yes' == $insert_tax && 'incl' == $tax_included ) || ( 'no' == $insert_tax && 'excl' == $tax_included ) ) {
			return new ACA_WC_Filtering_Product_Price( $this );
		}

		return new ACP_Filtering_Model_Disabled( $this );
	}

	public function editing() {
		return new ACA_WC_Editing_Product_Price( $this );
	}

}
