<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_Product_TaxClass $column
 */
class ACA_WC_Editing_Product_TaxClass extends ACP_Editing_Model_Meta {

	public function __construct( ACA_WC_Column_Product_TaxClass $column ) {
		parent::__construct( $column );
	}

	public function get_view_settings() {
		$options = array( '' => __( 'Standard', 'codepress-admin-columns' ) );
		$options = array_merge( $options, $this->column->get_tax_classes() );

		return array(
			'type'    => 'select',
			'options' => $options,
		);
	}

}
