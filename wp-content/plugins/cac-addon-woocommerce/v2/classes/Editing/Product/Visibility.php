<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_Product_Visibility $column
 */
class ACA_WC_Editing_Product_Visibility extends ACP_Editing_Model_Meta {

	public function __construct( ACA_WC_Column_Product_Visibility $column ) {
		parent::__construct( $column );
	}

	public function get_view_settings() {
		return $data = array(
			'type'    => 'select',
			'options' => $this->column->get_visibility_options(),
		);
	}

}
