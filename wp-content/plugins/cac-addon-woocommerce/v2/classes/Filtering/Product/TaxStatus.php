<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_Product_TaxStatus $column
 */
class ACA_WC_Filtering_Product_TaxStatus extends ACP_Filtering_Model_Meta {

	public function __construct( ACA_WC_Column_Product_TaxStatus $column ) {
		parent::__construct( $column );
	}

	public function get_filtering_data() {
		return array(
			'empty_option' => false,
			'options'      => (array) $this->column->get_tax_status(),
		);
	}

}
