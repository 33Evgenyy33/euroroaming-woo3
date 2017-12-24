<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ACA_WC_Column_ShopCoupon_ExpiryDate extends AC_Column_Meta
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'expiry_date' );
		$this->set_original( true );
		$this->set_group( 'woocommerce' );
	}

	// Display

	public function get_value( $id ) {
		return null;
	}

	public function get_raw_value( $id ) {
		$coupon = ac_addon_wc_helper()->get_coupon_by_id( $id );

		return $coupon->expiry_date;
	}

	public function get_meta_key() {
		return 'expiry_date';
	}

	// Pro

	public function sorting() {
		$sorting = new ACP_Sorting_Model( $this );

		return $sorting->set_data_type( 'numeric' );
	}

	public function editing() {
		return new ACA_WC_Editing_ShopCoupon_ExpiryDate( $this );
	}

	public function filtering() {
		return new ACA_WC_Filtering_Date( $this );
	}

}
