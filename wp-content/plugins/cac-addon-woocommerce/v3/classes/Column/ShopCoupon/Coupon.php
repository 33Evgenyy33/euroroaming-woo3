<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since NEWVERSION
 */
class ACA_WC_Column_ShopCoupon_Coupon extends AC_Column
	implements ACP_Export_Column {

	public function __construct() {
		$this->set_type( 'coupon' );
		$this->set_original( true );

		//$this->services()->set( 'export', new ACA_WC_Export_ShopCoupon_Coupon( $this ) );
	}

	public function export() {
		return new ACA_WC_Export_ShopCoupon_Coupon( $this );
	}

}
