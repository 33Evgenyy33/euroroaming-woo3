<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Filtering_Product_ProductTag extends ACP_Filtering_Model_Post_Taxonomy {

	public function get_filtering_vars( $vars ) {
		return $this->strategy->get_filterable_request_vars_taxonomy( $vars, $this->get_filter_value(), 'product_tag' );
	}

	public function get_filtering_data() {
		return array(
			'order' => false,
			'empty_option' => true,
			'options'      => $this->get_terms_list( 'product_tag' ),
		);
	}

}
