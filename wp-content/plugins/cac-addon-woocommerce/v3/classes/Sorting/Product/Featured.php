<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_Product_Featured $column
 */
class ACA_WC_Sorting_Product_Featured extends ACP_Sorting_Model {

	public function __construct( $column ) {
		parent::__construct( $column );

	}

	public function get_sorting_vars() {
		$wc_products = new WC_Product_Data_Store_CPT();

		$featured_items = array_keys( $wc_products->get_featured_product_ids() );
		$not_featured_items = get_posts( array(
			'post_type'      => array( 'product' ),
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'post__not_in'   => $featured_items,
		) );

		$ids = array_merge( $featured_items, $not_featured_items );

		if ( 'DESC' === $this->get_order() ) {
			$ids = array_reverse( $ids );
		}

		return array(
			'ids' => $ids,
		);
	}

}
