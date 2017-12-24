<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ACA_WC_Settings_UserMeta extends AC_Settings_Column_CustomField {

	protected function get_post_type() {
		return false;
	}

	protected function get_meta_type(){
		return 'user';
	}

}
