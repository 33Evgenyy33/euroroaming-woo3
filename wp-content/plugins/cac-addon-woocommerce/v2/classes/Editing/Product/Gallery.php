<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_Product_Gallery extends ACP_Editing_Model_Meta {

	public function get_view_settings() {
		$data = array(
			'type'         => 'media',
			'clear_button' => true,
			'attachment'   => array(
				'library' => array(
					'type' => 'image',
				),
			),
			'multiple'     => true,
		);

		return $data;
	}

	public function get_edit_value( $id ) {
		$raw_value = explode( ',', $this->column->get_raw_value( $id ) );

		return array_combine( $raw_value, $raw_value );
	}

	public function save( $id, $value ) {
		parent::save( $id, implode( ',', (array) $value ) );
	}

}
