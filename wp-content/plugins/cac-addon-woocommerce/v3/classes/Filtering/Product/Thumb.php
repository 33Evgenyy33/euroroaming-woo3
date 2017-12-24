<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Filtering_Product_Thumb extends ACP_Filtering_Model_Meta {

	public function get_filtering_data() {
		return array(
			'empty_option' => array(
				sprintf( __( "Without %s", 'codepress-admin-columns' ), __( "Image", 'codepress-admin-columns' ) ),
				sprintf( __( "Has %s", 'codepress-admin-columns' ), __( "Image", 'codepress-admin-columns' ) ),
			),
		);
	}

}
