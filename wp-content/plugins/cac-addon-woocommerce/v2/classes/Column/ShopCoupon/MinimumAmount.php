<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.1
 */
class ACA_WC_Column_ShopCoupon_MinimumAmount extends ACP_Column_Meta
	implements ACP_Column_EditingInterface, ACP_Column_SortingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'column-wc-minimum_amount' );
		$this->set_label( __( 'Minimum amount', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	// Meta

	public function get_meta_key() {
		return 'minimum_amount';
	}

	// Display

	public function get_value( $post_id ) {
		$amount = $this->get_raw_value( $post_id );

		if ( ! $amount ) {
			return $this->get_empty_char();
		}

		return wc_price( $amount );
	}

	// Pro

	public function filtering() {
		return new ACA_WC_Filtering_Numeric( $this );
	}

	public function editing() {
		return new ACA_WC_Editing_ShopCoupon_MinimumAmount( $this );
	}

	public function sorting() {
		return new ACP_Sorting_Model( $this );
	}

}
