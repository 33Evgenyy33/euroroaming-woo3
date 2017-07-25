<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ACA_WC_Column_ShopCoupon_Usage extends AC_Column
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface {

	public function __construct() {
		$this->set_type( 'usage' );
		$this->set_original( true );
	}

	// Display
	public function get_value( $id ) {
		return null;
	}

	public function get_raw_value( $id ) {
		$coupon = ac_addon_wc_helper()->get_coupon_by_id( $id );

		return array(
			'usage_limit'          => $coupon->usage_limit,
			'usage_limit_per_user' => $coupon->usage_limit_per_user,
		);
	}

	// Pro

	public function editing() {
		return new ACA_WC_Editing_ShopCoupon_Usage( $this );
	}

	public function sorting() {
		return new ACA_WC_Sorting_ShopCoupon_Usage( $this );
	}

}
