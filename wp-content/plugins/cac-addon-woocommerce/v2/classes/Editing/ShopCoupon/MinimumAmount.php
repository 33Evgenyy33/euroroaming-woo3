<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_ShopCoupon_MinimumAmount extends ACP_Editing_Model_Meta {

	public function save( $id, $value ) {
		parent::save( $id, wc_format_decimal( $value ) );
	}

}
