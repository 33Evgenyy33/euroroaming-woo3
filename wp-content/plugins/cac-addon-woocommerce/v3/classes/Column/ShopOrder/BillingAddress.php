<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Column_ShopOrder_BillingAddress extends AC_Column
	implements ACP_Export_Column {

	public function __construct() {
		$this->set_type( 'billing_address' );
		$this->set_original( true );
	}

	public function export() {
		return new ACA_WC_Export_ShopOrder_BillingAddress( $this );
	}

}
