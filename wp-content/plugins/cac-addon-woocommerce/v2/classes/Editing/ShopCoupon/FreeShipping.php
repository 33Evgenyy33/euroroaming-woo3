<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_ShopCoupon_FreeShipping extends ACP_Editing_Model_Meta {

	public function get_view_settings() {
		return array(
			'type'    => 'togglable',
			'options' => array( 'no', 'yes' ),
		);
	}

	public function save( $id, $value ) {
		parent::save( $id, 'yes' === $value ? 'yes' : 'no' );
	}

}
