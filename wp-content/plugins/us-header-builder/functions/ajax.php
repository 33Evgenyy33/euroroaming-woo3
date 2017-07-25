<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Load elements list HTML to choose from
 */
add_action( 'wp_ajax_ushb_get_elist_html', 'ajax_ushb_get_elist_html' );
function ajax_ushb_get_elist_html() {
	us_load_template( 'templates/elist', array() );

	// We don't use JSON to reduce data size
	die;
}

/**
 * Load shortcode builder elements forms
 */
add_action( 'wp_ajax_ushb_get_ebuilder_html', 'ajax_ushb_get_ebuilder_html' );
function ajax_ushb_get_ebuilder_html() {
	$template_vars = array(
		'titles' => array(),
		'body' => '',
	);

	// Loading all the forms HTML
	foreach ( us_config( 'header-settings.elements', array() ) as $type => $elm ) {
		$template_vars['titles'][ $type ] = isset( $elm['title'] ) ? $elm['title'] : $type;
		$template_vars['body'] .= us_get_template( 'templates/eform', array(
			'type' => $type,
			'params' => $elm['params'],
		) );
	}

	us_load_template( 'templates/ebuilder', $template_vars );

	// We don't use JSON to reduce data size
	die;
}

/**
 * Load header template selector forms
 */
add_action( 'wp_ajax_ushb_get_htemplates_html', 'ajax_ushb_get_htemplates_html' );
function ajax_ushb_get_htemplates_html() {

	us_load_template( 'templates/htemplates' );

	// We don't use JSON to reduce data size
	die;
}

/**
 * Save header
 */
add_action( 'wp_ajax_ushb_save', 'ajax_ushb_save' );
function ajax_ushb_save() {
	$post = array(
		'ID' => isset( $_POST['ID'] ) ? intval( $_POST['ID'] ) : NULL,
		'post_title' => isset( $_POST['post_title'] ) ? $_POST['post_title'] : NULL,
		'post_content' => isset( $_POST['post_content'] ) ? $_POST['post_content'] : NULL,
	);

	if ( ! check_admin_referer( 'ushb-update' ) OR ! current_user_can( 'edit_post', $post['ID'] ) ) {
		wp_send_json_error(
			array(
				'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
			)
		);
	}

	if ( ! $post['ID'] ) {
		wp_send_json_error(
			array(
				'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
			)
		);
	}

	if ( wp_update_post( $post ) !== $post['ID'] ) {
		wp_send_json_error(
			array(
				'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
			)
		);
	}

	wp_send_json_success(
		array(
			'message' => us_translate( 'Changes saved.' ),
		)
	);
}