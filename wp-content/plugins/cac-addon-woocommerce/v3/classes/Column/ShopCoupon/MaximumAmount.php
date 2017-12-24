<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.1
 */
class ACA_WC_Column_ShopCoupon_MaximumAmount extends ACP_Column_Meta
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'column-wc-maximum_amount' );
		$this->set_label( __( 'Maximum amount', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	// Meta

	public function get_meta_key() {
		return 'maximum_amount';
	}

	// Display

	public function get_value( $id ) {
		$amount = $this->get_raw_value( $id );

		if ( ! $amount ) {
			return $this->get_empty_char();
		}

		return wc_price( $amount );
	}

	// Pro

	public function filtering() {
		return new ACA_WC_Filtering_Numeric( $this );
	}

	public function sorting() {
		return new ACP_Sorting_Model( $this );
	}

	public function editing() {
		return new ACA_WC_Editing_ShopCoupon_MaximumAmount( $this );
	}

	// Common

	public function get_raw_value( $id ) {
		$coupon = new WC_Coupon( $id );

		return $coupon->get_maximum_amount();
	}

}
