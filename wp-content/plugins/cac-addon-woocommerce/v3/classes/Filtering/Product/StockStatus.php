<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Filtering_Product_StockStatus extends ACP_Filtering_Model_Meta {

	public function get_filtering_data() {
		return array(
			'order'   => false,
			'options' => array(
				'instock'    => __( 'In stock', 'woocommerce' ),
				'outofstock' => __( 'Out of stock', 'woocommerce' ),
			),
		);
	}

}
