<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ACA_WC_Column_ShopCoupon_Type extends ACP_Column_Meta
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'type' );
		$this->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	public function get_meta_key() {
		return 'discount_type';
	}

	public function get_raw_value( $id ) {
		$coupon = ac_addon_wc_helper()->get_coupon_by_id( $id );

		return $coupon->discount_type;
	}

	// Pro

	public function editing() {
		return new ACA_WC_Editing_ShopCoupon_Type( $this );
	}

	public function sorting() {
		return new ACA_WC_Sorting_ShopCoupon_Type( $this );
	}

	public function filtering() {
		return new ACA_WC_Filtering_ShopCoupon_Type( $this );
	}

}
