<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_ShopOrder_ShippingMethod $column
 */
class ACA_WC_Filtering_ShopOrder_ShippingMethod extends ACA_WC_Filtering_ShopOrder {

	public function __construct( ACA_WC_Column_ShopOrder_ShippingMethod $column ) {
		parent::__construct( $column );
	}

	public function filter_by_wc_shipping_method( $where, WP_Query $query ) {
		global $wpdb;

		if( $query->is_main_query() ){
			$where .= $wpdb->prepare( "AND om.meta_value = %s AND om.meta_key = 'method_id'", $this->get_filter_value() );
		}

		return $where;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_join', array( $this, 'join_by_order_itemmeta' ), 10, 2 );
		add_filter( 'posts_where', array( $this, 'filter_by_wc_shipping_method' ), 10, 2 );

		return $vars;
	}

	public function get_filtering_data() {
		$options = array();

		foreach ( WC()->shipping->load_shipping_methods() as $key => $method ) {
			$options[ $key ] = $method->method_title;
		}

		return array(
			'options' => $options,
		);
	}

}
