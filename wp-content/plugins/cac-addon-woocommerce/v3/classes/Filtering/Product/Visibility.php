<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Filtering_Product_Visibility extends ACP_Filtering_Model_Meta {

	public function get_filtering_data() {
		return array(
			'options' => wc_get_product_visibility_options(),
		);
	}

}
