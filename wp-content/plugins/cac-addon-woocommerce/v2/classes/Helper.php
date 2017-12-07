<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ACA_WC_Helper {

	/**
	 * @see   CPAC_Column::get_product()
	 * @since 1.2
	 */
	public function get_product( $post_id ) {
		$product = false;

		if ( function_exists( 'wc_get_product' ) ) {
			$product = wc_get_product( $post_id );
		} // WC 2.0<
		elseif ( function_exists( 'get_product' ) ) {
			$product = get_product( $post_id );
		}

		return $product;
	}

	/**
	 * @param string $search
	 * @param array  $args
	 *
	 * @return array
	 */
	public function search_products( $search, $args ) {

		$defaults = wp_parse_args( $args, array(
			'posts_per_page' => 60,
			'paged'          => 20,
			'post_type'      => 'product',
		) );

		// Search
		$args = wp_parse_args( array(
			's'      => $search,
			'fields' => 'ids',
		), $defaults );

		$product_ids_title = get_posts( $args );

		// Search by SKU
		$args = wp_parse_args( array(
			'fields'     => 'ids',
			'meta_query' => array(
				array(
					'key'     => '_sku',
					'value'   => $search,
					'compare' => 'LIKE',
				),
			),
		), $defaults );

		$product_ids_sku = get_posts( $args );

		$post_ids = array_unique( array_merge( $product_ids_title, $product_ids_sku ) );
		$options = array();

		foreach ( $post_ids as $post_id ) {
			$product = wc_get_product( $post_id );
			$options[ $post_id ] = wp_kses_post( html_entity_decode( $product->get_formatted_name(), ENT_QUOTES, get_bloginfo( 'charset' ) ) );
		}

		return $options;
	}

	/**
	 * @param int[]  $post_ids
	 * @param string $field
	 *
	 * @return array [ int $post_id => string $post_field ]
	 */
	public function get_editable_posts_values( $post_ids, $field = 'post_title' ) {
		$value = array();

		if ( $post_ids ) {
			foreach ( (array) $post_ids as $id ) {
				$value[ $id ] = get_post_field( $field, $id );
			}
		}

		return $value;
	}

	/**
	 * @param int    $user_id
	 * @param string $status
	 *
	 * @return int[]
	 */
	public function get_order_ids_by_user( $user_id, $status = 'wc-completed' ) {
		$order_ids = get_posts( array(
			'fields'         => 'ids',
			'post_type'      => 'shop_order',
			'posts_per_page' => -1,
			'post_status'    => $status,
			'meta_query'     => array(
				array(
					'key'   => '_customer_user',
					'value' => $user_id,
				),
			),
		) );

		if ( ! $order_ids ) {
			return array();
		}

		return $order_ids;
	}

	/**
	 * @param  int   $user_id
	 * @param string $status
	 *
	 * @return WC_Order[]|array
	 */
	public function get_orders_by_user( $user_id, $status = 'wc-completed' ) {
		$orders = array();

		foreach ( $this->get_order_ids_by_user( $user_id, $status ) as $order_id ) {
			$orders[] = new WC_Order( $order_id );
		}

		return $orders;
	}

	/**
	 * @param int $post_id
	 *
	 * @return WC_Coupon
	 */
	public function get_coupon_by_id( $post_id ) {
		return new WC_Coupon( ac_helper()->post->get_raw_post_title( $post_id ) );
	}

	/**
	 * @param int $order_id Order ID
	 *
	 * @return array
	 */
	public function get_product_ids_by_order( $order_id ) {
		global $wpdb;

		$product_ids = $wpdb->get_col( $wpdb->prepare(
			"SELECT DISTINCT om.meta_value
			FROM {$wpdb->prefix}woocommerce_order_items AS oi
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS om ON ( oi.order_item_id = om.order_item_id )
			WHERE om.meta_key = '_product_id'
			AND oi.order_id = %d
			ORDER BY om.meta_value;"
			,
			$order_id ) );

		return $product_ids;
	}

	/**
	 * @param int $coupon_id
	 *
	 * @return int[] Order ID's
	 */
	public function get_order_ids_by_coupon_id( $coupon_id ) {
		return $this->get_order_ids_by_coupon_code( ac_helper()->post->get_raw_post_title( $coupon_id ) );
	}

	/**
	 * @param string $coupon_code
	 *
	 * @return int[] Order ID's
	 */
	public function get_order_ids_by_coupon_code( $coupon_code ) {
		global $wpdb;

		$table = $wpdb->prefix . 'woocommerce_order_items';

		$sql = "
			SELECT {$table}.order_id
			FROM {$table}
			WHERE order_item_type = 'coupon'
			AND order_item_name = %s
		";

		return $wpdb->get_col( $wpdb->prepare( $sql, $coupon_code ) );
	}

	/**
	 * @param string $code Coupon Code
	 *
	 * @return string
	 */
	public function get_coupon_id_from_code( $code ) {
		global $wpdb;

		$sql = $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1;", $code );

		return $wpdb->get_var( $sql );
	}

}
