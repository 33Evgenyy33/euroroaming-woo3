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

	public function get_value( $post_id ) {
		$amount = $this->get_raw_value( $post_id );

		return $amount ? wc_price( $amount ) : $this->get_empty_char();
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

}
