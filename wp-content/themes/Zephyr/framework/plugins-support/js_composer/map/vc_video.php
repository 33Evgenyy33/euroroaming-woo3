<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Modifying shortcode: vc_video
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 */

vc_remove_param( 'vc_video', 'title' );
vc_remove_param( 'vc_video', 'el_width' );
vc_remove_param( 'vc_video', 'el_aspect' );
vc_remove_param( 'vc_video', 'css_animation' );
vc_add_params(
	'vc_video', array(
	array(
		'param_name' => 'link',
		'heading' => __( 'Video link', 'us' ),
		'description' => sprintf( __( 'Check supported formats on %s', 'us' ), '<a href="http://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F" target="_blank">WordPress Codex</a>' ),
		'type' => 'textfield',
		'std' => $config['atts']['link'],
		'admin_label' => TRUE,
		'weight' => 60,
	),
	array(
		'param_name' => 'video_related',
		'type' => 'checkbox',
		'value' => array( __( 'Show suggested videos when the video finishes', 'us' ) => TRUE ),
		( ( $config['atts']['video_related'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['video_related'],
		'weight' => 54,
	),
	array(
		'param_name' => 'video_title',
		'type' => 'checkbox',
		'value' => array( __( 'Show video title and player actions', 'us' ) => TRUE ),
		( ( $config['atts']['video_title'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['video_title'],
		'weight' => 52,
	),
	array(
		'param_name' => 'ratio',
		'heading' => __( 'Aspect Ratio', 'us' ),
		'type' => 'dropdown',
		'value' => array(
			'21:9' => '21x9',
			'16:9' => '16x9',
			'4:3' => '4x3',
			'3:2' => '3x2',
			'1:1' => '1x1',
		),
		'std' => $config['atts']['ratio'],
		'weight' => 50,
	),
	array(
		'param_name' => 'max_width',
		'heading' => __( 'Max Width in pixels', 'us' ),
		'type' => 'textfield',
		'std' => $config['atts']['max_width'],
		'admin_label' => TRUE,
		'weight' => 40,
	),
	array(
		'param_name' => 'align',
		'heading' => __( 'Video Alignment', 'us' ),
		'type' => 'dropdown',
		'value' => array(
			us_translate( 'Left' ) => 'left',
			us_translate( 'Center' ) => 'center',
			us_translate( 'Right' ) => 'right',
		),
		'std' => $config['atts']['align'],
		'dependency' => array( 'element' => 'max_width', 'not_empty' => TRUE ),
		'weight' => 30,
	),
)
);

// Setting proper shortcode order in VC shortcodes listing
vc_map_update( 'vc_video', array( 'weight' => 210 ) );
