<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Filtering_Product_BackordersAllowed extends ACP_Filtering_Model_Meta {

	public function get_filtering_data() {
		$available_options = array(
			'no'     => __( 'Do not allow', 'woocommerce' ),
			'notify' => __( 'Allow, but notify customer', 'woocommerce' ),
			'yes'    => __( 'Allow', 'woocommerce' )
		);

		$options = array();

		foreach( $this->get_meta_values() as $value ){
			if( isset( $available_options[ $value ] ) ){
				$options[ $value ] = $available_options[ $value ];
			}
		}

		return array(
			'options' => $options,
		);
	}

}
