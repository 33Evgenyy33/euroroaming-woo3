<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.0
 */
class ACA_WC_Column_Product_Type extends AC_Column
	implements ACP_Export_Column {

	public function __construct() {
		$this->set_type( 'product_type' );
		$this->set_original( true );
	}

	public function export() {
		return new ACA_WC_Export_Product_Type( $this );
	}

}
