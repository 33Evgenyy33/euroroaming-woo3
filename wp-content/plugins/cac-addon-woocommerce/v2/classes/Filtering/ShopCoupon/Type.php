<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_ShopCoupon_Type $column
 */
class ACA_WC_Filtering_ShopCoupon_Type extends ACP_Filtering_Model_Meta {

	public function __construct( ACA_WC_Column_ShopCoupon_Type $column ) {
		parent::__construct( $column );
	}

	public function get_filtering_data() {
		$values = $this->get_meta_values();

		if ( ! $values ) {
			return array();
		}

		$options = array();

		$types = $this->column->get_coupon_types();
		foreach ( $values as $type ) {
			$options[ $type ] = $types[ $type ];
		}

		return array(
			'options' => $options,
		);
	}

}
