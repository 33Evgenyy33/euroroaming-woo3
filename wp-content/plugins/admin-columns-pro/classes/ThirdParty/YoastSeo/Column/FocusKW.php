<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACP_ThirdParty_YoastSeo_Column_FocusKW extends ACP_ThirdParty_YoastSeo_Column
	implements ACP_Column_EditingInterface, ACP_Export_Column {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'wpseo-focuskw' );
	}

	public function editing() {
		return new ACP_ThirdParty_YoastSeo_Editing_FocusKW( $this );
	}

	public function export() {
		return new ACP_ThirdParty_YoastSeo_Export_FocusKW( $this );
	}

}
