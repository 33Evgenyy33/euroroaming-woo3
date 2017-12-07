<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACP_ThirdParty_YoastSeo_Column_Score extends ACP_ThirdParty_YoastSeo_Column
	implements ACP_Export_Column {

	public function __construct() {
		parent::__construct();

		$this->set_type( 'wpseo-score' );
	}

	public function register_settings() {
		$width = $this->get_setting( 'width' );

		$width->set_default( 63 );
		$width->set_default( 'px', 'width_unit' );
	}

	public function export() {
		return new ACP_ThirdParty_YoastSeo_Export_Score( $this );
	}

}
