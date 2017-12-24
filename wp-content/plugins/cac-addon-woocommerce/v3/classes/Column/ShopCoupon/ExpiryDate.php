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
	}

	// Meta

	public function get_meta_key() {
		return 'expiry_date';
	}

	// Display

	public function get_value( $id ) {
		return null;
	}

	// Pro

	public function sorting() {
		return new ACA_WC_Sorting_ShopCoupon_ExpiryDate( $this );
	}

	public function editing() {
		return new ACA_WC_Editing_ShopCoupon_ExpiryDate( $this );
	}

	public function filtering() {
		return new ACP_Filtering_Model_MetaDate( $this );
	}

}
