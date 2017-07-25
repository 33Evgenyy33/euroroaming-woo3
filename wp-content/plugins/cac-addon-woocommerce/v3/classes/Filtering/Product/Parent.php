<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Filtering_Product_Parent extends ACP_Filtering_Model_Post_Parent {

	public function get_filtering_request_vars( $vars, $value ) {
		$vars['post_parent'] = $value;

		return $vars;
	}

	public function get_filtering_data() {
		$data = array();
		foreach ( $this->strategy->get_values_by_db_field( 'post_parent' ) as $_value ) {
			$data['options'][ $_value ] = ac_helper()->post->get_raw_post_title( $_value );
		}

		return $data;
	}

}
