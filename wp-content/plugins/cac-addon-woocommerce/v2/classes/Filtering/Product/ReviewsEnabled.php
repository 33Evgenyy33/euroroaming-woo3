<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Filtering_Product_ReviewsEnabled extends ACP_Filtering_Model {

	public function filter_by_wc_reviews_enabled( $where ) {
		global $wpdb;

		return $where . $wpdb->prepare( "AND {$wpdb->posts}.comment_status = %s", $this->get_filter_value() );
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'posts_where', array( $this, 'filter_by_wc_reviews_enabled' ) );

		return $vars;
	}

	public function get_filtering_data() {
		return array(
			'options' => array(
				'open'   => __( 'Open' ),
				'closed' => __( 'Closed' ),
			),
		);
	}

}
