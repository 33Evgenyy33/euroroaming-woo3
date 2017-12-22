<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.1
 */
class ACA_WC_Column_Product_OrderCount extends AC_Column {

	public function __construct() {
		$this->set_type( 'column-wc-order_count' );
		$this->set_label( __( 'Number of orders', 'woocommerce' ) );
		$this->set_group( 'woocommerce' );
	}

	// Display

	public function get_value( $post_id ) {
		$value = $this->get_raw_value( $post_id );

		if ( ! $value ) {
			$value = $this->get_empty_char();
		}

		return $value;
	}

	public function get_raw_value( $post_id ) {
		global $wpdb;

		$num_orders = $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT( 1 )
			FROM {$wpdb->prefix}woocommerce_order_items wc_oi
			JOIN {$wpdb->prefix}woocommerce_order_itemmeta wc_oim ON wc_oi.order_item_id = wc_oim.order_item_id
			WHERE wc_oim.meta_key = '_product_id'
			AND wc_oim.meta_value = %d",
			$post_id
		) );

		return $num_orders;
	}

}
