<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.0
 */
class ACA_WC_Column_ShopOrder_Date extends AC_Column
	implements ACP_Column_FilteringInterface, ACP_Export_Column {

	public function __construct() {
		$this->set_type( 'order_date' );
		$this->set_original( true );
	}

	public function filtering() {
		return new ACP_Filtering_Model_Post_Date( $this );
	}

	public function export() {
		return new ACP_Export_Model_Post_Date( $this );
	}

}
