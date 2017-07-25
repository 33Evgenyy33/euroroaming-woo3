<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.0
 */
class ACA_WC_Column_ShopOrder_Purchased extends AC_Column {

	public function __construct() {
		$this->set_type( 'order_items' );
		$this->set_original( true );
	}

}
