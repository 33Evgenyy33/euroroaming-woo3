<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Column_Product_Comments extends AC_Column {

	public function __construct() {
		$this->set_type( 'comments' );
		$this->set_original( true );
	}

}
