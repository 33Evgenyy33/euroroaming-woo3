<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_ShopCoupon_IncludeProducts extends ACP_Editing_Model_Meta {

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
		$product_ids = array();

		if ( is_array( $value ) ) {
			foreach ( $value as $product_id ) {
				if ( $product_id && $product_id > 0 ) {
					$product_ids[] = $product_id;
				}
			}
		}

		parent::save( $id, implode( ',', $product_ids ) );
	}

}
