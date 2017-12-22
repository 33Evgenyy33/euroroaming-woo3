<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 1.2
 */
class ACA_WC_Column_Product_Name extends AC_Column
	implements ACP_Column_EditingInterface, ACP_Export_Column {

	public function __construct() {
		$this->set_type( 'name' );
		$this->set_original( true );
	}

	public function editing() {
		return new ACA_WC_Editing_Product_Name( $this );
	}

	public function export() {
		return new ACP_Export_Model_Post_Title( $this );
	}

}
