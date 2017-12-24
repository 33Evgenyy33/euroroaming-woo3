<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_ShopCoupon_CouponCode extends ACP_Editing_Model {

	public function get_view_settings() {
		return array(
			'type' => 'text',
			'js'   => array(
				'selector' => 'strong > a',
			),
		);
	}

	public function save( $id, $value ) {
		$this->strategy->update( $id, array( 'post_title' => $value ) );
	}

}
