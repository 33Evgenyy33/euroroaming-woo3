<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

add_action( 'wp_ajax_usof_save', 'usof_ajax_save' );
function usof_ajax_save() {

	if ( ! check_admin_referer( 'usof-actions' ) ) {
		wp_send_json_error(
			array(
				'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
			)
		);
	}

	do_action( 'usof_before_ajax_save' );

	global $usof_options, $usof_supported_cpt;
	usof_load_options_once();

	$usof_supported_cpt = us_get_option( 'custom_post_types_support', array() );

	$config = us_config( 'theme-options', array(), TRUE );

	// Logic do not seek here, young padawan. For WPML string translation compability such copying method is used.
	// If result of array_merge is put directly to $updated_options, the options will not save.
	$usof_options_fallback = array_merge( usof_defaults(), $usof_options );
	$updated_options = array();
	foreach ( $usof_options_fallback as $key => $val ) {
		$updated_options[$key] = $val;
	}

	$post_options = us_maybe_get_post_json( 'usof_options' );

	if ( empty( $post_options ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'There\'s no options to save', 'us' ),
			)
		);
	}
	//Preparing regex for group fields
	$group_regexes = array();
	$group_fields = array();
	foreach( $config as $section_id => $section ) {
		foreach ( $section['fields'] as $field_id => $field ) {
			if ( $field['type'] == 'group' ) {
				$group_fields[] = $field_id;
				foreach ( $field['params'] as $param_id => $param ) {
					$group_regexes[] = '/(' . $field_id . ')_([0-9]+)_(' . $param_id . ')/';
				}
			}
		}
	}

	foreach ( $post_options as $key => $value ) {
		// Regular Fields Values
		if ( isset( $updated_options[$key] ) ) {
			$updated_options[$key] = $value;
		}
		// Group Fields
		foreach ( $group_regexes as $regex ) {
			if ( preg_match( $regex, $key, $matches ) ) {
				if ( ! isset( $updated_options[$matches[1]] ) OR ( ! is_array( $updated_options[$matches[1]] ) ) ) {
					$updated_options[$matches[1]] = array();
				}
				if ( ! isset( $updated_options[$matches[1]][$matches[2]] ) OR ( ! is_array( $updated_options[$matches[1]][$matches[2]] ) ) ) {
					$updated_options[$matches[1]][$matches[2]] = array();
				}
				$updated_options[$matches[1]][$matches[2]][$matches[3]] = $value;
			}
		}
	}

	// Removing deleted Groups from Group Fields
	foreach ( $group_fields as $field_id ) {
		if ( is_array( $updated_options[$field_id] ) ) {
			foreach ( $updated_options[$field_id] as $index => $group ) {
				$is_null = TRUE;
				foreach ( $group as $value ) {
					if ( $value !== NULL ) {
						$is_null = FALSE;
					}
				}
				if ( $is_null ) {
					unset( $updated_options[$field_id][$index] );
				}
			}
		}

	}

	usof_save_options( $updated_options );

	do_action( 'usof_after_ajax_save' );

	wp_send_json_success(
		array(
			'message' => us_translate( 'Changes saved.' ),
		)
	);
}

add_action( 'wp_ajax_usof_reset', 'usof_ajax_reset' );
function usof_ajax_reset() {

	if ( ! check_admin_referer( 'usof-actions' ) ) {
		wp_send_json_error(
			array(
				'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
			)
		);
	}

	$updated_options = usof_defaults();
	usof_save_options( $updated_options );
	wp_send_json_success(
		array(
			'message' => __( 'Options were reset', 'us' ),
			'usof_options' => $updated_options,
		)
	);
}

add_action( 'wp_ajax_usof_backup', 'usof_ajax_backup' );
function usof_ajax_backup() {

	if ( ! check_admin_referer( 'usof-actions' ) ) {
		wp_send_json_error(
			array(
				'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
			)
		);
	}

	global $usof_options;
	usof_load_options_once();

	$theme = wp_get_theme();
	if ( is_child_theme() ) {
		$theme = wp_get_theme( $theme->get( 'Template' ) );
	}
	$theme_name = $theme->get( 'Name' );

	$backup = array(
		'time' => current_time( 'mysql', TRUE ),
		'usof_options' => $usof_options,
	);
	$backup_time = strtotime( $backup['time'] ) + get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;

	update_option( 'usof_backup_' . $theme_name, $backup, FALSE );

	wp_send_json_success(
		array(
			'status' => __( 'Last Backup', 'us' ) . ': <span>' . date_i18n( 'F j, Y - G:i T', $backup_time ) . '</span>',
		)
	);
}

add_action( 'wp_ajax_usof_restore_backup', 'usof_ajax_restore_backup' );
function usof_ajax_restore_backup() {

	if ( ! check_admin_referer( 'usof-actions' ) ) {
		wp_send_json_error(
			array(
				'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
			)
		);
	}

	global $usof_options;

	$theme = wp_get_theme();
	if ( is_child_theme() ) {
		$theme = wp_get_theme( $theme->get( 'Template' ) );
	}
	$theme_name = $theme->get( 'Name' );

	$backup = get_option( 'usof_backup_' . $theme_name );
	if ( ! $backup OR ! is_array( $backup ) OR ! isset( $backup['usof_options'] ) ) {
		wp_send_json_error(
			array(
				'message' => __( 'There\'s no backup to restore', 'us' ),
			)
		);
	}

	$usof_options = $backup['usof_options'];
	update_option( 'usof_options_' . $theme_name, $usof_options, TRUE );

	wp_send_json_success(
		array(
			'message' => __( 'Backup was restored', 'us' ),
			'usof_options' => $usof_options,
		)
	);
}

add_action( 'wp_ajax_usof_save_style_scheme', 'usof_ajax_save_style_scheme' );
function usof_ajax_save_style_scheme() {

	if ( ! check_admin_referer( 'usof-actions' ) ) {
		wp_send_json_error(
			array(
				'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
			)
		);
	}

	$theme = wp_get_theme();
	if ( is_child_theme() ) {
		$theme = wp_get_theme( $theme->get( 'Template' ) );
	}
	$theme_name = $theme->get( 'Name' );

	$custom_style_schemes = get_option( 'usof_style_schemes_' . $theme_name );

	if ( ! is_array( $custom_style_schemes ) ) {
		$custom_style_schemes = array();
	}

	$scheme = us_maybe_get_post_json( 'scheme' );
	if ( isset( $scheme[id] ) ) {
		$scheme_id = $scheme[id];
	} else {
		$max_index = 0;
		if ( count( $custom_style_schemes ) > 0 ) {
			$max_index = intval( max( array_keys( $custom_style_schemes ) ) );
		}
		$scheme_id = $max_index + 1;
	}

	$custom_style_schemes[$scheme_id] = array( 'title' => $scheme['name'], 'values' => $scheme['colors'] );
	update_option( 'usof_style_schemes_' . $theme_name, $custom_style_schemes, TRUE );

	$style_schemes = us_config( 'style-schemes' );

	$value = NULL;

	$output = '';
	foreach ( $style_schemes as $key => &$style_scheme ) {
		$active_class = '';
		if ( $key == $value ) {
			$active_class = ' active';
		}
		$output .= '<li class="usof-schemes-item' . $active_class . '" data-id="' . $key . '">';
		$output .= '<div class="usof-schemes-item-preview" style="background-color:' . $style_scheme['values']['color_content_bg'] . ';">';
		$output .= '<span class="preview_header" style="background-color:' . $style_scheme['values']['color_header_middle_bg'] . ';"></span>';
		$output .= '<span class="preview_primary" style="background-color:' . $style_scheme['values']['color_content_primary'] . ';"></span>';
		$output .= '<span class="preview_secondary" style="background-color:' . $style_scheme['values']['color_content_secondary'] . ';"></span>';
		$output .= '<span class="preview_text" style="color:' . $style_scheme['values']['color_content_text'] . ';">' . $style_scheme['title'] . '</span>';
		$output .= '</div></li>';
	}
	foreach ( $custom_style_schemes as $key => &$style_scheme ) {
		$active_class = '';
		if ( $key == $value ) {
			$active_class = ' active';
		}
		$output .= '<li class="usof-schemes-item type_custom' . $active_class . '" data-id="' . $key . '">';
		$output .= '<div class="usof-schemes-item-delete"></div>';
		$output .= '<div class="usof-schemes-item-preview" style="background-color:' . $style_scheme['values']['color_content_bg'] . ';">';
		$output .= '<span class="preview_header" style="background-color:' . $style_scheme['values']['color_header_middle_bg'] . ';"></span>';
		$output .= '<span class="preview_primary" style="background-color:' . $style_scheme['values']['color_content_primary'] . ';"></span>';
		$output .= '<span class="preview_secondary" style="background-color:' . $style_scheme['values']['color_content_secondary'] . ';"></span>';
		$output .= '<span class="preview_text" style="color:' . $style_scheme['values']['color_content_text'] . ';">' . $style_scheme['title'] . '</span>';
		$output .= '</div></li>';
	}

	wp_send_json_success(
		array(
			'schemes' => $style_schemes,
			'customSchemes' => $custom_style_schemes,
			'schemesHtml' => $output,
		)
	);
}


add_action( 'wp_ajax_usof_delete_style_scheme', 'usof_ajax_delete_style_scheme' );
function usof_ajax_delete_style_scheme() {
	if ( ! check_admin_referer( 'usof-actions' ) ) {
		wp_send_json_error(
			array(
				'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
			)
		);
	}

	$theme = wp_get_theme();
	if ( is_child_theme() ) {
		$theme = wp_get_theme( $theme->get( 'Template' ) );
	}
	$theme_name = $theme->get( 'Name' );

	$scheme = sanitize_text_field( $_POST['scheme'] );

	$custom_style_schemes = get_option( 'usof_style_schemes_' . $theme_name );

	if ( ! is_array( $custom_style_schemes ) ) {
		$custom_style_schemes = array();
	}
	if ( isset( $custom_style_schemes[$scheme] ) ) {
		unset( $custom_style_schemes[$scheme] );
	}
	update_option( 'usof_style_schemes_' . $theme_name, $custom_style_schemes, TRUE );

	$style_schemes = us_config( 'style-schemes' );

	$value = NULL;

	$output = '';
	foreach ( $style_schemes as $key => &$style_scheme ) {
		$active_class = '';
		if ( $key == $value ) {
			$active_class = ' active';
		}
		$output .= '<li class="usof-schemes-item' . $active_class . '" data-id="' . $key . '">';
		$output .= '<div class="usof-schemes-item-preview" style="background-color:' . $style_scheme['values']['color_content_bg'] . ';">';
		$output .= '<span class="preview_header" style="background-color:' . $style_scheme['values']['color_header_middle_bg'] . ';"></span>';
		$output .= '<span class="preview_primary" style="background-color:' . $style_scheme['values']['color_content_primary'] . ';"></span>';
		$output .= '<span class="preview_secondary" style="background-color:' . $style_scheme['values']['color_content_secondary'] . ';"></span>';
		$output .= '<span class="preview_text" style="color:' . $style_scheme['values']['color_content_text'] . ';">' . $style_scheme['title'] . '</span>';
		$output .= '</div></li>';
	}
	foreach ( $custom_style_schemes as $key => &$style_scheme ) {
		$active_class = '';
		if ( $key == $value ) {
			$active_class = ' active';
		}
		$output .= '<li class="usof-schemes-item type_custom' . $active_class . '" data-id="' . $key . '">';
		$output .= '<div class="usof-schemes-item-delete"></div>';
		$output .= '<div class="usof-schemes-item-preview" style="background-color:' . $style_scheme['values']['color_content_bg'] . ';">';
		$output .= '<span class="preview_header" style="background-color:' . $style_scheme['values']['color_header_middle_bg'] . ';"></span>';
		$output .= '<span class="preview_primary" style="background-color:' . $style_scheme['values']['color_content_primary'] . ';"></span>';
		$output .= '<span class="preview_secondary" style="background-color:' . $style_scheme['values']['color_content_secondary'] . ';"></span>';
		$output .= '<span class="preview_text" style="color:' . $style_scheme['values']['color_content_text'] . ';">' . $style_scheme['title'] . '</span>';
		$output .= '</div></li>';
	}

	wp_send_json_success(
		array(
			'schemes' => $style_schemes,
			'customSchemes' => $custom_style_schemes,
			'schemesHtml' => $output,
		)
	);
}

add_action( 'wp_ajax_usof_add_group_params', 'usof_ajax_add_group_params' );
function usof_ajax_add_group_params() {
	$section = sanitize_text_field( $_POST['section'] );
	$group = sanitize_text_field( $_POST['group'] );
	$index = sanitize_text_field( $_POST['index'] );

	$config = us_config( 'theme-options', array() );

	if ( isset( $config[$section]['fields'][$group] ) ) {
		$field = $config[$section]['fields'][$group];
		$result_html = '<div class="usof-form-wrapper">';
		$result_html .= '<div class="usof-form-wrapper-cont">';
		ob_start();
		foreach ( $field['params'] as $param_name => $param ) {
			us_load_template(
				'vendor/usof/templates/field', array(
					'name' => $group.'_'.$index.'_'.$param_name,
					'id' => 'usof_' . $group.'_'.$index.'_'.$param_name,
					'field' => $param,
					'values' => array(),
				)
			);
		}
		$result_html .= ob_get_clean();
		$result_html .= '</div>';
		$result_html .= '<div class="usof-form-group-delete" title="' . us_translate( 'Delete' ) . '"></div>';
		$result_html .= '</div>';

		wp_send_json_success(
			array(
				'paramsHtml' => $result_html,
			)
		);
	} else {
		wp_send_json_error(
			array(
				'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
			)
		);
	}

}
