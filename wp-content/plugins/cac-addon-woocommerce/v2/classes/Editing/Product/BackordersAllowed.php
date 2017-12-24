<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_Product_BackordersAllowed extends ACP_Editing_Model_Meta {

	public function get_view_settings() {
		return array(
			'type'    => 'select',
			'options' => array(
				'no'     => __( 'Do not allow', 'woocommerce' ),
				'notify' => __( 'Allow, but notify customer', 'woocommerce' ),
				'yes'    => __( 'Allow', 'woocommerce' ),
			),
		);
	}

	public function save( $id, $value ) {
		if ( in_array( $value, array( 'no', 'yes', 'notify' ) ) ) {
			parent::save( $id, $value );
		}
	}

}
