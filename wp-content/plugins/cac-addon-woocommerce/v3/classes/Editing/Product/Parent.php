<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_Product_Parent extends ACP_Editing_Model {

	public function get_view_settings() {
		return array(
			'type'          => 'select2_dropdown',
			'ajax_populate' => true,
			'clear_button'  => true,
		);
	}

	public function get_ajax_options( $request ) {
		return ac_addon_wc_helper()->search_products( $request['search'], array( 'paged' => $request['paged'], 'post__not_in' => $request['item_id'] ) );
	}

	public function get_edit_value( $id ) {
		$product = wc_get_product( $id );

		if ( $product->is_type( array( 'variable', 'grouped' ) ) ) {
			return null;
		}

		return ac_addon_wc_helper()->get_editable_posts_values( $product->get_parent_id() );
	}

	public function save( $id, $value ) {
		$this->strategy->update( $id, array( 'post_parent' => $value ) );
	}

}
