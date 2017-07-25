<?php

class us_migration_4_0_2 extends US_Migration_Translator {

	// Content
	public function translate_content( &$content ) {
		return $this->_translate_content( $content );
	}

	public function translate_us_gmaps( &$name, &$params, &$content ) {
		$changed = FALSE;

		if ( ! empty( $params['api_key'] ) ) {
			global $usof_options;
			usof_load_options_once();

			$usof_options['gmaps_api_key'] = $params['api_key'];
			usof_save_options( $usof_options );

			unset( $params['api_key'] );
			$changed = TRUE;
		}

		return $changed;
	}

}
