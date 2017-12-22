<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ACA_WC_Column_ShopCoupon_CouponDescription
 *
 * Custom Implementation of the description column
 */
class ACA_WC_Column_ShopCoupon_CouponDescription extends AC_Column
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Export_Column {

	public function __construct() {
		$this->set_type( 'column-wc-coupon_description' );
		$this->set_label( __( 'Description', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	public function get_raw_value( $post_id ) {
		return get_post_field( 'post_excerpt', $post_id, 'raw' );
	}

	public function register_settings() {
		$this->add_setting( new AC_Settings_Column_WordLimit( $this ) );
	}

	// Pro

	public function editing() {
		return new ACA_WC_Editing_ShopCoupon_Description( $this );
	}

	public function sorting() {
		return new ACP_Sorting_Model( $this );
	}

	public function export() {
		return new ACA_WC_Export_ShopCoupon_Description( $this );
	}

}
