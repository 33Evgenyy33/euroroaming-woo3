<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_Product_TaxClass $column
 */
class ACA_WC_Filtering_Product_TaxClass extends ACP_Filtering_Model_Meta {

	public function __construct( ACA_WC_Column_Product_TaxClass $column ) {
		parent::__construct( $column );
	}

	public function get_filtering_data() {
		return array(
			'empty_option' => true,
			'options'      => $this->column->get_tax_classes(),
		);
	}

}
