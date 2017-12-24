<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_ShopOrder_Status $column
 */
class ACA_WC_Filtering_ShopOrder_Status extends ACA_WC_Filtering_ShopOrder {

	public function __construct( ACA_WC_Column_ShopOrder_Status $column ) {
		parent::__construct( $column );
	}

	public function get_filtering_vars( $vars ) {
		$vars['post_status'] = ( substr( $this->get_filter_value(), 0, 3 ) == 'wc-' ) ? $this->get_filter_value() : 'wc-' . $this->get_filter_value();

		return $vars;
	}

	public function get_filtering_data() {
		return array(
			'options' => $this->column->get_order_status_options(),
		);
	}

}
