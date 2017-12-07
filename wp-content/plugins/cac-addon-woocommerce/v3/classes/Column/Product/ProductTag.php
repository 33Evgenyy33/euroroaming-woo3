<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Column_Product_ProductTag extends AC_Column
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface, ACP_Export_Column {

	public function __construct() {
		$this->set_original( true );
		$this->set_type( 'product_tag' );
	}

	public function get_taxonomy() {
		return 'product_tag';
	}

	public function sorting() {
		return new ACP_Sorting_Model_Post_Taxonomy( $this );
	}

	public function editing() {
		return new ACP_Editing_Model_Post_Taxonomy( $this );
	}

	public function filtering() {
		return new ACP_Filtering_Model_Post_Taxonomy( $this );
	}

	public function export() {
		return new ACP_Export_Model_Post_Taxonomy( $this );
	}
}
