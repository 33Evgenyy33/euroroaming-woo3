<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_Product_Crosssells extends ACP_Editing_Model_Meta {

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
		$crosssell_ids = array();

		if ( is_array( $value ) ) {
			foreach ( $value as $crosssell_id ) {
				if ( $crosssell_id && $crosssell_id > 0 ) {
					$crosssell_ids[] = $crosssell_id;
				}
			}
		}

		parent::save( $id, $crosssell_ids );
	}

}
