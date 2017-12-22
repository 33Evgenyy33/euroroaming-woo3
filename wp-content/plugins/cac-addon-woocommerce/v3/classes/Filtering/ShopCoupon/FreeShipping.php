<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Filtering_ShopCoupon_FreeShipping extends ACP_Filtering_Model_Meta {

	public function get_filtering_data() {
		return array(
			'options' => array(
				'no'  => __( 'No' ),
				'yes' => __( 'Yes' ),
			),
		);
	}

}
