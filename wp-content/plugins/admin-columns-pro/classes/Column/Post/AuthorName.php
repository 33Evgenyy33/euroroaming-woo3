<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 4.0
 */
class ACP_Column_Post_AuthorName extends AC_Column_Post_AuthorName
	implements ACP_Column_EditingInterface, ACP_Column_FilteringInterface, ACP_Column_SortingInterface, ACP_Export_Column {

	public function sorting() {
		return new ACP_Sorting_Model_Post_AuthorName( $this );
	}

	public function editing() {
		return new ACP_Editing_Model_Post_Author( $this );
	}

	public function filtering() {
		/* @var AC_Settings_Column_User $setting */
		$setting = $this->get_setting( 'user');

		if ( 'roles' === $setting->get_display_author_as() ) {
			return new ACP_Filtering_Model_Post_Roles( $this );
		}

		return new ACP_Filtering_Model_Post_AuthorName( $this );
	}

	public function export() {
		return new ACP_Export_Model_StrippedValue( $this );
	}

}
