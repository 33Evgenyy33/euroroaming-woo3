<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.0
 */
class ACA_WC_Column_ShopOrder_ShippingAddress extends AC_Column
	implements ACP_Export_Column {

	public function __construct() {
		$this->set_type( 'shipping_address' );
		$this->set_original( true );
	}

	public function export() {
		return new ACA_WC_Export_ShopOrder_ShippingAddress( $this );
	}

}
