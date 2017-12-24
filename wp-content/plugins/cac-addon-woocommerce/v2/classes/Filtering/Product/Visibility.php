<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_Product_Visibility $column
 */
class ACA_WC_Filtering_Product_Visibility extends ACP_Filtering_Model_Meta {

	public function __construct( ACA_WC_Column_Product_Visibility $column ) {
		parent::__construct( $column );
	}

	public function get_filtering_data() {
		return array(
			'options' => $this->column->get_visibility_options(),
		);
	}

}
