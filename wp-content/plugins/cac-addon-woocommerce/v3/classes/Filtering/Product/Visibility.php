<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Filtering_Product_Visibility extends ACP_Filtering_Model_Post_Taxonomy {

	public function get_filtering_vars( $vars ) {

		switch ( $this->get_filter_value() ) {
			case 'search':
				$vars['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => array( 'exclude-from-search' ),
					'operator' => 'NOT IN',
				);
				$vars['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => array( 'exclude-from-catalog' ),
					'operator' => 'IN',
				);

				break;
			case 'catalog':
				$vars['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => array( 'exclude-from-catalog' ),
					'operator' => 'NOT IN',
				);
				$vars['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => array( 'exclude-from-search' ),
					'operator' => 'IN',
				);
				break;
			case 'visible':
				$vars['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => array( 'exclude-from-catalog', 'exclude-from-search' ),
					'operator' => 'NOT IN',
				);
				break;
			case 'hidden':
				$vars['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'slug',
					'terms'    => array( 'exclude-from-catalog', 'exclude-from-search' ),
					'operator' => 'AND',
				);
				break;
		}

		return $vars;
	}

	public function get_filtering_data() {
		return array(
			'options' => wc_get_product_visibility_options(),
		);
	}

}
