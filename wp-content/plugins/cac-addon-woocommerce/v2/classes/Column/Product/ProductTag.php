<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Column_Product_ProductTag extends ACA_WC_Column_Product_ProductCat
	implements ACP_Column_SortingInterface, ACP_Column_EditingInterface, ACP_Column_FilteringInterface {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'product_tag' );
	}

	public function get_taxonomy() {
		return 'product_tag';
	}

}
