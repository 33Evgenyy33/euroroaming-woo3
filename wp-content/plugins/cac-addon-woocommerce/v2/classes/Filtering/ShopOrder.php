<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class ACA_WC_Filtering_ShopOrder extends ACP_Filtering_Model {

	public function join_by_order_itemmeta( $join, WP_Query $query ) {
		global $wpdb;

		if ( $query->is_main_query() ) {
			$join .= "LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oi ON ( {$wpdb->posts}.ID = oi.order_id ) ";
			$join .= "LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS om ON ( oi.order_item_id = om.order_item_id ) ";
		}

		return $join;
	}

	public function join_by_postmeta( $join, WP_Query $query ) {
		global $wpdb;

		if ( $query->is_main_query() ) {
			return $join . "LEFT JOIN {$wpdb->postmeta} AS pm ON ( pm.post_id = om.meta_value AND om.meta_key = '_product_id' ) ";
		}

		return $join;
	}

}
