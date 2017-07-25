<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.1
 */
class ACA_WC_Column_Product_Attributes extends AC_Column {

	public function __construct() {
		$this->set_type( 'column-wc-attributes' );
		$this->set_label( __( 'Attributes', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_raw_value( $id ) {
		return wc_get_product( $id )->get_attributes();
	}

	// Settings

	public function register_settings() {
		$this->add_setting( new ACA_WC_Settings_ProductAttributes( $this ) );
	}

}
