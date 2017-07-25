<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.2
 */
class ACA_WC_Column_Product_Featured extends AC_Column_Meta
	implements ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'featured' );
		$this->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	// Meta

	public function get_meta_key() {
		return '_featured';
	}

	// Display

	public function get_raw_value( $post_id ) {
		return $this->is_featured( $post_id );
	}

	// Pro

	public function filtering() {
		return new ACA_WC_Filtering_Product_Featured( $this );
	}

	public function editing() {
		return new ACA_WC_Editing_Product_Featured( $this );
	}

	// Common

	private function is_featured( $id ) {
		return wc_get_product( $id )->is_featured();
	}

}
