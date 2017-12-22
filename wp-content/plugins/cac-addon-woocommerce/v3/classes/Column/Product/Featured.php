<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.2
 */
class ACA_WC_Column_Product_Featured extends AC_Column
	implements ACP_Column_FilteringInterface, ACP_Column_SortingInterface {

	public function __construct() {
		$this->set_type( 'featured' );
		$this->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	// Display

	public function get_raw_value( $id ) {
		return wc_get_product( $id )->is_featured();
	}

	// Pro

	public function filtering() {
		return new ACA_WC_Filtering_Product_Featured( $this );
	}

	public function sorting() {
		return new ACA_WC_Sorting_Product_Featured( $this );
	}

}
