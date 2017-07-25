<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.0
 */
class ACA_WC_Column_ShopOrder_Total extends AC_Column_Meta
	implements ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'order_total' );
		$this->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	// Meta

	public function get_meta_key() {
		return '_order_total';
	}

	// Pro

	public function filtering() {
		return new ACA_WC_Filtering_Numeric( $this );
	}

}
