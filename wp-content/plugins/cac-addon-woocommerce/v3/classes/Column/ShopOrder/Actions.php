<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.0
 */
class ACA_WC_Column_ShopOrder_Actions extends AC_Column
	implements ACP_Export_Column {

	public function __construct() {
		$this->set_type( 'order_actions' );
		$this->set_original( true );
	}

	public function export() {
		return new ACP_Export_Model_Disabled( $this );
	}

}
