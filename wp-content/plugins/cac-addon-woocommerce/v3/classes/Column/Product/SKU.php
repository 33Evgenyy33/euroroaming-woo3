<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Column_Product_SKU extends AC_Column
	implements ACP_Column_EditingInterface {

	public function __construct() {
		$this->set_type( 'sku' );
		$this->set_original( true );
	}

	// Pro

	public function editing() {
		return new ACA_WC_Editing_Product_SKU( $this );
	}

}
