<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_ShopCoupon_IncludeProducts extends ACP_Editing_Model {

	public function get_view_settings() {
		return array(
			'type'          => 'select2_dropdown',
			'ajax_populate' => true,
			'multiple'      => true,
		);
	}

	public function get_ajax_options( $request ) {
		return ac_addon_wc_helper()->search_products( $request['search'], array( 'paged' => $request['paged'] ) );
	}

	public function get_edit_value( $id ) {
		return ac_addon_wc_helper()->get_editable_posts_values( $this->column->get_raw_value( $id ) );
	}

	public function save( $id, $value ) {
		$coupon = new WC_Coupon( $id );
		$coupon->set_product_ids( $value );
		$coupon->save();
	}

}
