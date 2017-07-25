<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.0
 */
class ACA_WC_Column_Product_Date extends AC_Column
	implements ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_type( 'date' );
		$this->set_original( true );
	}

	public function filtering() {
		return new ACP_Filtering_Model_Post_Date( $this );
	}

}
