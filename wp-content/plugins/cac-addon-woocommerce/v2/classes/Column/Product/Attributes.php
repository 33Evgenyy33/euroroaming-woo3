<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.1
 */
class ACA_WC_Column_Product_Attributes extends AC_Column_Meta {

	public function __construct() {
		$this->set_type( 'column-wc-attributes' );
		$this->set_label( __( 'Attributes', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	// Meta

	public function get_meta_key() {
		return '_product_attributes';
	}

	// Settings

	public function register_settings() {
		$this->add_setting( new ACA_WC_Settings_ProductAttributes( $this ) );
	}

}
