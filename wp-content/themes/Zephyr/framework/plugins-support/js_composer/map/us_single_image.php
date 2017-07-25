<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_single_image
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 */
vc_map(
	array(
		'base' => 'us_single_image',
		'name' => __( 'Single Image', 'us' ),
		'icon' => 'icon-wpb-single-image',
		'category' => us_translate( 'Content', 'js_composer' ),
		'weight' => 370,
		'params' => array(
			array(
				'param_name' => 'image',
				'heading' => us_translate( 'Image' ),
				'type' => 'attach_image',
				'std' => $config['atts']['image'],
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'size',
				'heading' => __( 'Image Size', 'us' ),
				'type' => 'dropdown',
				'value' => us_image_sizes_select_values(),
				'std' => $config['atts']['size'],
				'edit_field_class' => 'vc_col-sm-6',
				'admin_label' => TRUE,
			),
			array(
				'param_name' => 'align',
				'heading' => __( 'Image Alignment', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'Default' ) => '',
					us_translate( 'Left' ) => 'left',
					us_translate( 'Center' ) => 'center',
					us_translate( 'Right' ) => 'right',
				),
				'std' => $config['atts']['align'],
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'style',
				'heading' => __( 'Image Style', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'None' ) => '',
					__( 'Outlined', 'us' ) => 'outlined',
					__( 'Simple Shadow', 'us' ) => 'shadow-1',
					__( 'Colored Shadow', 'us' ) => 'shadow-2',
					__( 'Phone 6 Black Realistic', 'us' ) => 'phone6-1',
					__( 'Phone 6 White Realistic', 'us' ) => 'phone6-2',
					__( 'Phone 6 Black Flat', 'us' ) => 'phone6-3',
					__( 'Phone 6 White Flat', 'us' ) => 'phone6-4',
				),
				'std' => $config['atts']['style'],
				'edit_field_class' => 'vc_col-sm-6',
			),
			array(
				'param_name' => 'meta',
				'type' => 'checkbox',
				'value' => array( __( 'Show image title and description', 'us' ) => TRUE ),
				( ( $config['atts']['meta'] !== FALSE ) ? 'std' : '_std' ) => $config['atts']['meta'],
			),
			array(
				'param_name' => 'meta_style',
				'heading' => __( 'Title and Description Style', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Simple', 'us' ) => 'simple',
					__( 'Modern', 'us' ) => 'modern',
				),
				'std' => $config['atts']['meta_style'],
				'dependency' => array( 'element' => 'meta', 'not_empty' => TRUE ),
			),
			array(
				'param_name' => 'onclick',
				'heading' => __( 'On click action', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'None' ) => '',
					__( 'Open original image in a popup', 'us' ) => 'lightbox',
					__( 'Open custom link', 'us' ) => 'custom_link',
				),
				'std' => '',
			),
			array(
				'param_name' => 'link',
				'type' => 'vc_link',
				'std' => $config['atts']['link'],
				'dependency' => array( 'element' => 'onclick', 'value' => 'custom_link' ),
			),
			array(
				'param_name' => 'animate',
				'heading' => __( 'Animation', 'us' ),
				'description' => __( 'Select animation type if you want this element to be animated when it enters into the browsers viewport. Note: Works only in modern browsers.', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'None' ) => '',
					__( 'Fade', 'us' ) => 'fade',
					__( 'Appear From Center', 'us' ) => 'afc',
					__( 'Appear From Left', 'us' ) => 'afl',
					__( 'Appear From Right', 'us' ) => 'afr',
					__( 'Appear From Bottom', 'us' ) => 'afb',
					__( 'Appear From Top', 'us' ) => 'aft',
					__( 'Height From Center', 'us' ) => 'hfc',
					__( 'Width From Center', 'us' ) => 'wfc',
				),
				'std' => $config['atts']['animate'],
				'admin_label' => TRUE,
			),
			array(
				'param_name' => 'animate_delay',
				'heading' => __( 'Animation Delay', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'None' ) => '',
					__( '0.2 second', 'us' ) => '0.2',
					__( '0.4 second', 'us' ) => '0.4',
					__( '0.6 second', 'us' ) => '0.6',
					__( '0.8 second', 'us' ) => '0.8',
					__( '1 second', 'us' ) => '1',
				),
				'std' => $config['atts']['animate_delay'],
				'dependency' => array( 'element' => 'animate', 'not_empty' => TRUE ),
				'admin_label' => TRUE,
			),
			array(
				'param_name' => 'el_class',
				'heading' => us_translate( 'Extra class name', 'js_composer' ),
				'type' => 'textfield',
				'std' => $config['atts']['el_class'],
			),
			array(
				'param_name' => 'css',
				'heading' => 'CSS',
				'type' => 'css_editor',
				'std' => $config['atts']['css'],
				'group' => us_translate( 'Design Options', 'js_composer' ),
			),
		),
	)
);
vc_remove_element( 'vc_single_image' );

class WPBakeryShortCode_us_single_image extends WPBakeryShortCode {

	public function singleParamHtmlHolder( $param, $value ) {
		$output = '';
		// Compatibility fixes
		$param_name = isset( $param['param_name'] ) ? $param['param_name'] : '';
		$type = isset( $param['type'] ) ? $param['type'] : '';
		$class = isset( $param['class'] ) ? $param['class'] : '';

		if ( $type == 'attach_image' AND $param_name == 'image' ) {
			$output .= '<input type="hidden" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . $value . '" />';
			$element_icon = $this->settings( 'icon' );
			$img = wpb_getImageBySize(
				array(
					'attach_id' => (int) preg_replace( '/[^\d]/', '', $value ),
					'thumb_size' => 'thumbnail',
				)
			);
			$logo_html = '';

			if ( $img ) {
				$logo_html .= $img['thumbnail'];
			} else {
				$logo_html .= '<img width="150" height="150" class="attachment-thumbnail icon-wpb-single-image vc_element-icon"  data-name="' . $param_name . '" alt="" title="" style="display: none;" />';
			}
			$logo_html .= '<span class="no_image_image vc_element-icon' . ( ! empty( $element_icon ) ? ' ' . $element_icon : '' ) . ( $img && ! empty( $img['p_img_large'][0] ) ? ' image-exists' : '' ) . '" />';
			$this->setSettings( 'logo', $logo_html );
			$output .= $this->outputTitleTrue( $this->settings['name'] );
		} elseif ( ! empty( $param['holder'] ) ) {
			if ( $param['holder'] == 'input' ) {
				$output .= '<' . $param['holder'] . ' readonly="true" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="' . $value . '">';
			} elseif ( in_array( $param['holder'], array( 'img', 'iframe' ) ) ) {
				$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" src="' . $value . '">';
			} elseif ( $param['holder'] !== 'hidden' ) {
				$output .= '<' . $param['holder'] . ' class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '">' . $value . '</' . $param['holder'] . '>';
			}
		}

		if ( ! empty( $param['admin_label'] ) && $param['admin_label'] === TRUE ) {
			$output .= '<span class="vc_admin_label admin_label_' . $param['param_name'] . ( empty( $value ) ? ' hidden-label' : '' ) . '"><label>' . __( $param['heading'], 'js_composer' ) . '</label>: ' . $value . '</span>';
		}

		return $output;
	}

	public function getImageSquereSize( $img_id, $img_size ) {
		if ( preg_match_all( '/(\d+)x(\d+)/', $img_size, $sizes ) ) {
			$exact_size = array(
				'width' => isset( $sizes[1][0] ) ? $sizes[1][0] : '0',
				'height' => isset( $sizes[2][0] ) ? $sizes[2][0] : '0',
			);
		} else {
			$image_downsize = image_downsize( $img_id, $img_size );
			$exact_size = array(
				'width' => $image_downsize[1],
				'height' => $image_downsize[2],
			);
		}
		if ( isset( $exact_size['width'] ) && (int) $exact_size['width'] !== (int) $exact_size['height'] ) {
			$img_size = (int) $exact_size['width'] > (int) $exact_size['height'] ? $exact_size['height'] . 'x' . $exact_size['height'] : $exact_size['width'] . 'x' . $exact_size['width'];
		}

		return $img_size;
	}

	protected function outputTitle( $title ) {
		return '';
	}

	protected function outputTitleTrue( $title ) {
		return '<h4 class="wpb_element_title">' . __( $title, 'us' ) . ' ' . $this->settings( 'logo' ) . '</h4>';
	}
}
