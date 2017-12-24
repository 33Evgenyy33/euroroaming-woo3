<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.0
 */
class ACA_WC_Column_Product_Title extends AC_Column {

	public function __construct() {
		$this->set_type( 'title' );
		$this->set_original( true );
	}

}
