<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Filtering_ShopOrder_CouponUsed extends ACP_Filtering_Model_Meta {

	public function get_filtering_data() {
		return array(
			'empty_option' => array(
				__( 'No' ),
				__( 'Yes' )
			)
		);
	}

}
