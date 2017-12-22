<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_ShopCoupon_Amount extends ACP_Editing_Model {

	public function save( $id, $value ) {
		$coupon = new WC_Coupon( $id );
		$coupon->set_amount( $value );
		$coupon->save();
	}

}
