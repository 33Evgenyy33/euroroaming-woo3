<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_ShopOrder_Product $column
 */
class ACA_WC_Filtering_ShopOrder_Product extends ACA_WC_Filtering_ShopOrder {

	public function __construct( ACA_WC_Column_ShopOrder_Product $column ) {
		parent::__construct( $column );
	}

	public function get_filtering_vars( $vars ) {
		switch ( $this->column->get_product_property() ) {

			case 'title' :
				add_filter( 'posts_join', array( $this, 'join_by_order_itemmeta' ) );
				add_filter( 'posts_where', array( $this, 'filter_by_wc_product_title' ) );

				break;
			case 'sku' :
				add_filter( 'posts_join', array( $this, 'join_by_order_itemmeta' ) );
				add_filter( 'posts_join', array( $this, 'join_by_postmeta' ) );
				add_filter( 'posts_where', array( $this, 'filter_by_wc_product_sku' ) );

				break;
		}

		return $vars;
	}

	public function filter_by_wc_product_title( $where ) {
		global $wpdb;

		return $where . $wpdb->prepare( "AND om.meta_value = %d AND om.meta_key = '_product_id'", $this->get_filter_value() );
	}

	public function filter_by_wc_product_sku( $where ) {
		global $wpdb;

		return $where . $wpdb->prepare( "AND pm.meta_value = %s AND pm.meta_key = '_sku'", get_post_meta( $this->get_filter_value(), '_sku', true ) );
	}

	public function get_filtering_data() {
		return array(
			'options' => $this->get_all_ordered_products(),
		);
	}

	/**
	 * @since 1.3.2
	 */
	private function get_all_ordered_products() {
		global $wpdb;

		switch ( $this->column->get_product_property() ) {

			case 'sku' :
				$values = $wpdb->get_results(
					"SELECT DISTINCT p.ID as id, pm.meta_value as value
					FROM {$wpdb->posts} AS p
					INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta om ON ( p.ID = om.meta_value AND om.meta_key = '_product_id' )
					INNER JOIN {$wpdb->postmeta} pm ON ( p.ID = pm.post_id AND pm.meta_key = '_sku' AND pm.meta_value != '' )
					ORDER BY pm.meta_value;"
				);

				break;
			case 'id' :
				$values = $wpdb->get_results(
					"SELECT DISTINCT p.ID as id, p.ID as value
					FROM {$wpdb->posts} AS p
					INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta om ON ( p.ID = om.meta_value AND om.meta_key = '_product_id' )
					ORDER BY p.ID;"
				);

				break;
			case 'title' :
			default:
				$values = $wpdb->get_results(
					"SELECT DISTINCT p.ID AS id, p.post_title AS value
					FROM {$wpdb->posts} AS p
					INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta om ON ( p.ID = om.meta_value AND om.meta_key = '_product_id' )
					ORDER BY post_title;"
				);
				break;
		}

		if ( ! $values ) {
			return array();
		}

		$products = array();
		foreach ( $values as $value ) {
			$products[ $value->id ] = $value->value;
		}

		return $products;
	}

}
