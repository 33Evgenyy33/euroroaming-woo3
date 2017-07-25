<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Settings_ProductMeta extends AC_Settings_Column_CustomField {

	protected function get_post_type() {
		return 'product';
	}

}
