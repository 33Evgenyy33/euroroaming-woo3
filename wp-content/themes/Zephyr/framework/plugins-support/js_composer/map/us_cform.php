<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_cform
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 */
vc_map(
	array(
		'base' => 'us_cform',
		'name' => __( 'Contact Form', 'us' ),
		'description' => '',
		'category' => us_translate( 'Content', 'js_composer' ),
		'weight' => 180,
		'params' => array(
			array(
				'param_name' => 'receiver_email',
				'heading' => __( 'Receiver Email', 'us' ),
				'description' => sprintf( __( 'Requests will be sent to this Email. You can insert multiple comma-separated emails as well.', 'us' ) ),
				'type' => 'textfield',
				'std' => $config['atts']['receiver_email'],
				'admin_label' => TRUE,
			),
			array(
				'param_name' => 'name_field',
				'heading' => __( 'Name field', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Shown, required', 'us' ) => 'required',
					__( 'Shown, not required', 'us' ) => 'shown',
					__( 'Hidden', 'us' ) => 'hidden',
				),
				'std' => $config['atts']['name_field'],
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'email_field',
				'heading' => __( 'Email field', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Shown, required', 'us' ) => 'required',
					__( 'Shown, not required', 'us' ) => 'shown',
					__( 'Hidden', 'us' ) => 'hidden',
				),
				'std' => $config['atts']['email_field'],
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'phone_field',
				'heading' => __( 'Phone field', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Shown, required', 'us' ) => 'required',
					__( 'Shown, not required', 'us' ) => 'shown',
					__( 'Hidden', 'us' ) => 'hidden',
				),
				'std' => $config['atts']['phone_field'],
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'message_field',
				'heading' => __( 'Message field', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Shown, required', 'us' ) => 'required',
					__( 'Shown, not required', 'us' ) => 'shown',
					__( 'Hidden', 'us' ) => 'hidden',
				),
				'std' => $config['atts']['message_field'],
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'captcha_field',
				'heading' => __( 'Captcha field', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Hidden', 'us' ) => 'hidden',
					__( 'Shown, required', 'us' ) => 'required',
				),
				'std' => $config['atts']['captcha_field'],
			),
			array(
				'param_name' => 'button_text',
				'heading' => __( 'Button Label', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['button_text'],
				'group' => __( 'Button', 'us' ),
			),
			array(
				'param_name' => 'button_size',
				'heading' => __( 'Button Size', 'us' ),
				'description' => sprintf( __( 'Examples: %s', 'us' ), '26px, 1.3em, 200%' ),
				'type' => 'textfield',
				'std' => $config['atts']['button_size'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => __( 'Button', 'us' ),
			),
			array(
				'param_name' => 'button_align',
				'heading' => __( 'Button Alignment', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'Left' ) => 'left',
					us_translate( 'Center' ) => 'center',
					us_translate( 'Right' ) => 'right',
				),
				'std' => $config['atts']['button_align'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => __( 'Button', 'us' ),
			),
			array(
				'param_name' => 'button_style',
				'heading' => __( 'Button Style', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Solid', 'us' ) => 'solid',
					__( 'Outlined', 'us' ) => 'outlined',
				),
				'std' => $config['atts']['button_style'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => __( 'Button', 'us' ),
			),
			array(
				'param_name' => 'button_color',
				'heading' => __( 'Button Color', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Primary (theme color)', 'us' ) => 'primary',
					__( 'Secondary (theme color)', 'us' ) => 'secondary',
					__( 'Border (theme color)', 'us' ) => 'light',
					__( 'Text (theme color)', 'us' ) => 'contrast',
					us_translate( 'Black' ) => 'black',
					us_translate( 'White' ) => 'white',
					__( 'Purple', 'us' ) => 'purple',
					__( 'Pink', 'us' ) => 'pink',
					__( 'Red', 'us' ) => 'red',
					__( 'Yellow', 'us' ) => 'yellow',
					__( 'Lime', 'us' ) => 'lime',
					__( 'Green', 'us' ) => 'green',
					__( 'Teal', 'us' ) => 'teal',
					__( 'Blue', 'us' ) => 'blue',
					__( 'Navy', 'us' ) => 'navy',
					__( 'Midnight', 'us' ) => 'midnight',
					__( 'Brown', 'us' ) => 'brown',
					__( 'Cream', 'us' ) => 'cream',
					__( 'Transparent', 'us' ) => 'transparent',
				),
				'std' => $config['atts']['button_color'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => __( 'Button', 'us' ),
			),
			array(
				'param_name' => 'el_class',
				'heading' => us_translate( 'Extra class name', 'js_composer' ),
				'type' => 'textfield',
				'std' => $config['atts']['el_class'],
			),
		),
	)
);
