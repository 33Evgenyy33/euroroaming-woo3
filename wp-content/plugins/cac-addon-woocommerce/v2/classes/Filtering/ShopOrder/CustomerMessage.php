<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Filtering_ShopOrder_CustomerMessage extends ACP_Filtering_Model {

	public function filter_by_excerpt( $where ) {
		global $wpdb;

		$sql = $this->get_filter_value() ? "!= ''" : "=''";

		return "{$where} AND {$wpdb->posts}.post_excerpt " . $sql;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_where', array( $this, 'filter_by_excerpt' ) );

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
