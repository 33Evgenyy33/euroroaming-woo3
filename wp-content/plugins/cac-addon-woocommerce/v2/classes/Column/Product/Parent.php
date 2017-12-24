<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.1
 */
class ACA_WC_Column_Product_Parent extends AC_Column
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'column-wc-parent' );
		$this->set_label( __( 'Parent product', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	// Display

	public function get_value( $product_id ) {
		$parent = $this->get_raw_value( $product_id );

		if ( ! $parent ) {
			return $this->get_empty_char();
		}

		return $this->get_formatted_value( $parent );
	}

	public function get_raw_value( $post_id ) {
		return AC()->helper()->post->get_raw_field( 'post_parent', $post_id );
	}

	// Pro

	public function sorting() {
		return new ACP_Sorting_Model_Post_Parent( $this );
	}

	public function editing() {
		return new ACA_WC_Editing_Product_Parent( $this );
	}

	public function filtering() {
		return new ACA_WC_Filtering_Product_Parent( $this );
	}

	// Settings

	public function register_settings() {
		$this->add_setting( new ACA_WC_Settings_Product( $this ) );
	}

}
