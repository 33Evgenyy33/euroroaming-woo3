<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ACA_WC_Column_ShopCoupon_Usage extends AC_Column
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Export_Column {

	public function __construct() {
		$this->set_type( 'usage' );
		$this->set_original( true );
	}

	// Pro

	public function editing() {
		return new ACA_WC_Editing_ShopCoupon_Usage( $this );
	}

	public function sorting() {
		return new ACA_WC_Sorting_ShopCoupon_Usage( $this );
	}

	public function export() {
		return new ACA_WC_Export_ShopCoupon_Usage( $this );
	}

}
