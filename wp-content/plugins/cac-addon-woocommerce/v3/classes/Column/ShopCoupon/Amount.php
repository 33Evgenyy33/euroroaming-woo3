<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ACA_WC_Column_ShopCoupon_Amount extends ACP_Column_Meta
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface, ACP_Export_Column {

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

	public function get_raw_value( $id ) {
		$coupon = new WC_Coupon( $id );

		return $coupon->get_amount();
	}

	// Pro

	public function filtering() {
		return new ACA_WC_Filtering_Numeric( $this );
	}

	public function sorting() {
		$model = new ACP_Sorting_Model( $this );
		$model->set_data_type( 'numeric' );

		return $model;
	}

	public function editing() {
		return new ACA_WC_Editing_ShopCoupon_Amount( $this );
	}

	public function export() {
		return new ACA_WC_Export_ShopCoupon_Amount( $this );
	}

}
