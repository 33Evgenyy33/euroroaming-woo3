<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Filtering_Product_Featured extends ACP_Filtering_Model {

	public function get_filtering_vars( $vars ) {
		$product_visibility_term_ids = wc_get_product_visibility_term_ids();

		$operator = 'yes' == $this->get_filter_value() ? 'IN' : 'NOT IN';

		$vars['tax_query'] = array(
			array(
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => array( $product_visibility_term_ids['featured'] ),
				'operator' => $operator,
			),
		);

		return $vars;
	}

	public function get_filtering_data() {
		return array(
			'options' => array(
				'no'  => __( 'No' ),
				'yes' => __( 'Yes' ),
			),
		);
	}

}
