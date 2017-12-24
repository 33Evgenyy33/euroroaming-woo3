<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ACA_WC_Column_ShopCoupon_Amount extends ACP_Column_Meta
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'amount' );
		$this->set_original( true );
	}

	// Meta

	public function get_meta_key() {
		return 'coupon_amount';
	}

	// Display

	public function get_value( $id ) {
		return null;
	}

	public function get_raw_value( $post_id ) {
		return $this->get_coupon_amount( $post_id );
	}

	// Pro

	public function filtering() {
		return new ACA_WC_Filtering_Numeric( $this );
	}

	public function sorting() {
		return new ACA_WC_Sorting_ShopCoupon_Amount( $this );
	}

	public function editing() {
		return new ACA_WC_Editing_ShopCoupon_Amount( $this );
	}

	// Common

	public function get_coupon_amount( $post_id ) {
		$coupon = ac_addon_wc_helper()->get_coupon_by_id( $post_id );

		return $coupon->amount;
	}

}
