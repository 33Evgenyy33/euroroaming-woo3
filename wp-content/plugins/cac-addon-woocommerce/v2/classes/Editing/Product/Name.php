<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_Product_Name extends ACP_Editing_Model {

	public function get_view_settings() {
		return array(
			'type'         => 'text',
			'js'           => array(
				'selector' => 'a.row-title',
			),
			'display_ajax' => false,
		);
	}

	public function get_edit_value( $id ) {
		return ac_helper()->post->get_raw_field( 'post_title', $id );
	}

	public function save( $id, $value ) {
		$this->strategy->update( $id, array( 'post_title' => $value ) );
	}

}
