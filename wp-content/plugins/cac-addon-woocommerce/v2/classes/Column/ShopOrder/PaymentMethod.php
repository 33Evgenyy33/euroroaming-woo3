<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Column_ShopOrder_PaymentMethod extends AC_Column_Meta
	implements ACP_Column_SortingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'column-wc-payment_method' );
		$this->set_label( __( 'Payment method', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	// Meta

	public function get_meta_key() {
		return '_payment_method_title';
	}

	// Display

	public function get_value( $post_id ) {
		$title = $this->get_payment_method( get_post_meta( $post_id, '_payment_method', true ) );

		if ( ! $title ) {
			$title = get_post_meta( $post_id, '_payment_method_title', true );
		}

		return $title;
	}

	// Pro

	public function sorting() {
		return new ACP_Sorting_Model_Meta( $this );
	}

	public function filtering() {
		return new ACA_WC_Filtering_ShopOrder_PaymentMethod( $this );
	}

	// Common

	private function get_payment_method( $method ) {
		$payment_gateways = WC()->payment_gateways()->payment_gateways();

		if ( ! isset( $payment_gateways[ $method ] ) ) {
			return false;
		}

		return $payment_gateways[ $method ]->title;
	}

}
