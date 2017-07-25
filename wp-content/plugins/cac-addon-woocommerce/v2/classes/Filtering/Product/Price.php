<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Filtering_Product_Price extends ACP_Filtering_Model_Meta {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'numeric' );
	}

	public function is_ranged() {
		return true;
	}

	public function is_active() {
		$insert_tax = get_option( 'woocommerce_prices_include_tax' );
		$tax_included = get_option( 'woocommerce_tax_display_shop' );

		if ( ( 'yes' == $insert_tax && 'incl' == $tax_included ) || ( 'no' == $insert_tax && 'excl' == $tax_included ) ) {
			return true;
		}

		return false;
	}

}
