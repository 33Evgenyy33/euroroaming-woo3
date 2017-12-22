<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 4.0
 */
class ACP_Column_Post_BeforeMoreTag extends AC_Column_Post_BeforeMoreTag
	implements ACP_Column_FilteringInterface, ACP_Column_SortingInterface, ACP_Export_Column {

	public function sorting() {
		return new ACP_Sorting_Model( $this );
	}

	public function filtering() {
		return new ACP_Filtering_Model_Post_BeforeMoreTag( $this );
	}

	public function export() {
		return new ACP_Export_Model_StrippedValue( $this );
	}

}
