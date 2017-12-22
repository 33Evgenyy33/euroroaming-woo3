<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.2
 */
class ACA_WC_Column_ShopCoupon_Products extends AC_Column
	implements ACP_Export_Column {

	public function __construct() {
		$this->set_type( 'products' );
		$this->set_original( true );
	}

	public function export() {
		return new ACA_WC_Export_ShopCoupon_Products( $this );
	}

}
