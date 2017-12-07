<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since NEWVERSION
 */
class ACA_WC_Column_ShopCoupon_EmailRestrictions extends AC_Column
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'column-wc-email-restrictions' );
		$this->set_label( __( 'Email restrictions', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	// Display

	public function get_value( $id ) {
		return implode( ', ', $this->get_raw_value( $id ) );
	}

	// Pro
	public function filtering() {
		return new ACP_Filtering_Model_Disabled( $this );
	}

	public function sorting() {
		return new ACP_Sorting_Model( $this );
	}

	public function editing() {
		return new ACA_WC_Editing_ShopCoupon_EmailRestrictions( $this );
	}

	// Common

	public function get_raw_value( $id ) {
		$coupon = new WC_Coupon( $id );

		return $coupon->get_email_restrictions();
	}

}
