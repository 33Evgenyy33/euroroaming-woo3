<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_Product_Price extends ACP_Editing_Model {

	public function get_view_settings() {
		return array(
			'type' => 'wc_price',
		);
	}

	public function get_edit_value( $id ) {
		$product = wc_get_product( $id );

		if ( $product->is_type( array( 'variable', 'grouped' ) ) ) {
			return null;
		}

		return (object) parent::get_edit_value( $id );
	}

	public function save( $id, $value ) {
		if ( ! is_array( $value ) || empty( $value ) ) {
			return;
		}

		$product = wc_get_product( $id );

		if ( ! $product ) {
			return;
		}

		if ( $product->is_type( array( 'variable', 'grouped' ) ) ) {
			return;
		}

		$args = wp_parse_args( $value, array(
			'regular_price'         => $product->get_regular_price(),
			'sale_price'            => $product->get_sale_price(),
			'sale_price_dates_from' => $product->sale_price_dates_from,
			'sale_price_dates_to'   => $product->sale_price_dates_to,
		) );

		$regular_price = ( $args['regular_price'] === '' ) ? '' : wc_format_decimal( $args['regular_price'] );
		$sale_price = ( $args['sale_price'] === '' ) ? '' : wc_format_decimal( $args['sale_price'] );

		update_post_meta( $product->id, '_regular_price', $regular_price );
		update_post_meta( $product->id, '_sale_price', $sale_price );

		$date_from = $args['sale_price_dates_from'] ? $args['sale_price_dates_from'] : '';
		$date_to = $args['sale_price_dates_to'] ? $args['sale_price_dates_to'] : '';

		// Dates
		if ( $date_from ) {
			update_post_meta( $product->id, '_sale_price_dates_from', strtotime( $date_from ) );
		} else {
			update_post_meta( $product->id, '_sale_price_dates_from', '' );
		}

		if ( $date_to ) {
			update_post_meta( $product->id, '_sale_price_dates_to', strtotime( $date_to ) );
		} else {
			update_post_meta( $product->id, '_sale_price_dates_to', '' );
		}

		if ( $date_to && ! $date_from ) {
			update_post_meta( $product->id, '_sale_price_dates_from', strtotime( 'NOW', current_time( 'timestamp' ) ) );
		}

		// Update price if on sale
		if ( $sale_price !== '' && $date_to == '' && $date_from == '' ) {
			update_post_meta( $product->id, '_price', wc_format_decimal( $sale_price ) );
		} else {
			update_post_meta( $product->id, '_price', ( $regular_price === '' ) ? '' : wc_format_decimal( $regular_price ) );
		}

		if ( $sale_price !== '' && $date_from && strtotime( $date_from ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
			update_post_meta( $product->id, '_price', wc_format_decimal( $sale_price ) );
		}

		if ( $date_to && strtotime( $date_to ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
			update_post_meta( $product->id, '_price', ( $regular_price === '' ) ? '' : wc_format_decimal( $regular_price ) );
			update_post_meta( $product->id, '_sale_price_dates_from', '' );
			update_post_meta( $product->id, '_sale_price_dates_to', '' );
		}
	}

}
