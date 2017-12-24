<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.0
 */
class ACA_WC_Column_Product_Gallery extends AC_Column_Meta
	implements ACP_Column_EditingInterface {

	public function __construct() {
		$this->set_group( 'woocommerce' );
		$this->set_type( 'column-wc-product-gallery' );
		$this->set_label( __( 'Product Gallery', 'woocommerce' ) );
	}

	// Meta

	public function get_meta_key() {
		return '_product_image_gallery';
	}

	// Display

	public function get_value( $id ) {
		$value = $this->get_formatted_value( new AC_Collection( explode( ',', $this->get_raw_value( $id ) ) ) );

		if ( $value instanceof AC_Collection ) {
			$value = $value->filter()->implode();
		}

		return $value;
	}

	// Pro

	public function editing() {
		return new ACA_WC_Editing_Product_Gallery( $this );
	}

	// Settings

	public function register_settings() {
		$this->add_setting( new AC_Settings_Column_Image( $this ) );
	}

}
