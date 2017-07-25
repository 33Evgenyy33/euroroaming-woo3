<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Column_Product_ProductCat extends ACP_Column_Post_Taxonomy
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'product_cat' );
		$this->set_label( null ); // overwrite tax label
		$this->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	// Tax

	public function get_taxonomy() {
		return 'product_cat';
	}

}
