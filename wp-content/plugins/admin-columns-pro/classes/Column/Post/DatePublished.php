<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.4
 */
class ACP_Column_Post_DatePublished extends AC_Column_Post_DatePublished
	implements ACP_Column_SortingInterface, ACP_Column_FilteringInterface {

	public function sorting() {
		$model = new ACP_Sorting_Model( $this );
		$model->set_orderby( 'date' );

		return $model;
	}

	public function filtering() {
		return new ACP_Filtering_Model_Post_Date( $this );
	}

}
