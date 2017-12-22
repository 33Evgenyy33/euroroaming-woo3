<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Filtering_ShopOrder_CustomerMessage extends ACP_Filtering_Model {

	public function filter_by_excerpt( $where, WP_Query $query ) {
		global $wpdb;

		if ( $query->is_main_query() ) {
			$sql = $this->get_filter_value() ? "!= ''" : "=''";
			$where = "{$where} AND {$wpdb->posts}.post_excerpt " . $sql;
		}

		return $where;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_where', array( $this, 'filter_by_excerpt' ), 10, 2 );

		return $vars;
	}

	public function get_filtering_data() {
		return array(
			'options' => array(
				0 => __( 'Empty', 'codepress-admin-columns' ),
				1 => __( 'Has customer message', 'codepress-admin-columns' ),
			),
		);
	}
}
