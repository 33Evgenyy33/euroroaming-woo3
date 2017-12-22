<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Filtering_Product_Featured extends ACP_Filtering_Model_Meta {

	public function get_filtering_vars( $vars ) {
		$vars['meta_query'][] = array(
			'key'   => $this->column->get_meta_key(),
			'value' => 'yes' === $this->get_filter_value() ? 'yes' : 'no',
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
