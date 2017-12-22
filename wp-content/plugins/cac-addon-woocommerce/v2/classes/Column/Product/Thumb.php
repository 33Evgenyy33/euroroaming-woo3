<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.0
 */
class ACA_WC_Column_Product_Thumb extends AC_Column_Meta
	implements ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'thumb' );
		$this->set_original( true );
	}

	// Display

	public function get_value( $id ) {
		return null;
	}

	// Meta

	public function get_meta_key() {
		return '_thumbnail_id';
	}

	// Display

	public function get_raw_value( $post_id ) {
		return has_post_thumbnail( $post_id ) ? get_post_thumbnail_id( $post_id ) : false;
	}

	// Pro

	public function editing() {
		return new ACP_Editing_Model_Post_FeaturedImage( $this );
	}

	public function filtering() {
		return new ACA_WC_Filtering_Product_Thumb( $this );
	}

}
