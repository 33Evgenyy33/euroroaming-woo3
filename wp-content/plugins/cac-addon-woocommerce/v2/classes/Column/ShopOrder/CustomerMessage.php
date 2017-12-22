<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ACA_WC_Column_ShopOrder_CustomerMessage extends AC_Column
	implements ACP_Column_SortingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'customer_message' );
		$this->set_original( true );
	}

	// Display

	public function get_value( $id ) {
		return null;
	}

	public function get_raw_value( $post_id ) {
		$order = new WC_Order( $post_id );

		return $order->get_status();
	}

	// Pro

	public function sorting() {
		return new ACA_WC_Sorting_ShopOrder_CustomerMessage( $this );
	}

	public function filtering() {
		return new ACA_WC_Filtering_ShopOrder_CustomerMessage( $this );
	}

	// Common

	public function get_message( $id ) {
		$value = get_post_field( 'post_excerpt', $id );
		if ( ! $value ) {
			$value = get_post_field( 'post_content', $id );
		}

		return $value;
	}

}
