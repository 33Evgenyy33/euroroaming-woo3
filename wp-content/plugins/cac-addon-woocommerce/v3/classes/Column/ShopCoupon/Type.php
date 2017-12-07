<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ACA_WC_Column_ShopCoupon_Type extends ACP_Column_Meta
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface, ACP_Export_Column {

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
		$coupon = new WC_Coupon( $id );

		return $coupon->get_discount_type();
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

	public function export() {
		return new ACA_WC_Export_ShopCoupon_Type( $this );
	}

}
