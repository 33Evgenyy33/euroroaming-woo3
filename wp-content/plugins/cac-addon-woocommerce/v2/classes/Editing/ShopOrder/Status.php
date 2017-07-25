<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_ShopOrder_Status $column
 */
class ACA_WC_Editing_ShopOrder_Status extends ACP_Editing_Model {

	public function __construct( ACA_WC_Column_ShopOrder_Status $column ) {
		parent::__construct( $column );
	}

	public function get_view_settings() {
		return array(
			'type'    => 'select',
			'options' => $this->column->get_order_status_options(),
		);
	}

	public function get_edit_value( $id ) {
		$raw_value = $this->column->get_raw_value( $id );
		if ( substr( $raw_value, 0, 3 ) != 'wc-' ) {
			$raw_value = 'wc-' . $raw_value;
		}

		return $raw_value;
	}

	public function save( $id, $value ) {
		$order = new WC_Order( $id );
		$order->update_status( $value );
	}
}
