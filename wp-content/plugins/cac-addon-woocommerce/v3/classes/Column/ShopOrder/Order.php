<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.0
 */
class ACA_WC_Column_ShopOrder_Order extends AC_Column
	implements ACP_Column_FilteringInterface, ACP_Export_Column {

	public function __construct() {
		$this->set_type( 'order_title' );
		$this->set_original( true );
	}

	public function filtering() {
		return new ACP_Filtering_Model_Post_ID( $this );
	}

	public function export() {
		return new ACA_WC_Export_ShopOrder_Order( $this );
	}

}
