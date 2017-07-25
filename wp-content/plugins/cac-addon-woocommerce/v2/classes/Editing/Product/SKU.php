<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Editing_Product_SKU extends ACP_Editing_Model_Meta {

	public function save( $id, $value ) {
		$current_sku = $this->column->get_raw_value( $id );
		$new_sku = wc_clean( $value );

		if ( empty( $new_sku ) ) {
			$new_sku = '';
		}

		if ( $new_sku != $current_sku ) {
			global $wpdb;

			$existing_id = $wpdb->get_var( $wpdb->prepare( "
						SELECT $wpdb->posts.ID
					    FROM $wpdb->posts
					    LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id)
					    WHERE $wpdb->posts.post_type = 'product'
					    AND $wpdb->posts.post_status = 'publish'
					    AND $wpdb->postmeta.meta_key = '_sku' AND $wpdb->postmeta.meta_value = %s
					", $new_sku ) );

			if ( $existing_id ) {
				return new WP_Error( 'cacie_error_sku_exists', __( 'The SKU must be unique.', 'codepress-admin-columns' ) );
			}

			parent::save( $id, $new_sku );

			return true;
		}

		return new WP_Error( 'cacie_error_sku_exists', __( 'The SKU is the same.', 'codepress-admin-columns' ) );
	}

}
