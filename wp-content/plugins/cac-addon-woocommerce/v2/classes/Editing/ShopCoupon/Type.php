<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_ShopCoupon_Type extends ACP_Editing_Model {

	public function get_view_settings() {
		return array(
			'type'    => 'select',
			'options' => wc_get_coupon_types(),
		);
	}

	public function save( $id, $value ) {
		parent::save( $id, wc_clean( $value ) );
	}

}
