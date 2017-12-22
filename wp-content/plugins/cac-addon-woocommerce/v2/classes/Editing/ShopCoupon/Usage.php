<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_ShopCoupon_Usage extends ACP_Editing_Model {

	public function get_edit_value( $id ) {
		return (object) parent::get_edit_value( $id );
	}

	public function get_view_settings() {
		return array(
			'type' => 'wc_usage',
		);
	}

	public function save( $id, $value ) {
		update_post_meta( $id, 'usage_limit', wc_clean( $value['usage_limit'] ) );
		update_post_meta( $id, 'usage_limit_per_user', wc_clean( $value['usage_limit_per_user'] ) );
	}

}
