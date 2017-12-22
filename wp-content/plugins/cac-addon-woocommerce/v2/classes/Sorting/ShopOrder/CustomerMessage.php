<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_ShopOrder_CustomerMessage $column
 */
class ACA_WC_Sorting_ShopOrder_CustomerMessage extends ACP_Sorting_Model {

	public function __construct( ACA_WC_Column_ShopOrder_CustomerMessage $column ) {
		parent::__construct( $column );
	}

	public function get_sorting_vars() {
		$post_ids = array();

		foreach ( $this->strategy->get_results() as $post_id ) {
			$post_ids[ $post_id ] = $this->column->get_message( $post_id );
		}

		return array(
			'ids' => $this->sort( $post_ids ),
		);

	}

}
