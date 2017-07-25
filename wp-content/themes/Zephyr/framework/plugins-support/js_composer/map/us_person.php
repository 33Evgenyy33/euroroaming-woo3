<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_person
 *
 * @var   $shortcode string Current shortcode name
 * @var   $config    array Shortcode's config
 *
 * @param $config    ['atts'] array Shortcode's attributes and default values
 * @param $config    ['content'] string Shortcode's default content
 */
vc_map(
	array(
		'base' => 'us_person',
		'name' => __( 'Person', 'us' ),
		'category' => us_translate( 'Content', 'js_composer' ),
		'weight' => 260,
		'params' => array(
			array(
				'param_name' => 'image',
				'heading' => __( 'Photo', 'us' ),
				'type' => 'attach_image',
				'std' => $config['atts']['image'],
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 200,
			),
			array(
				'param_name' => 'image_hover',
				'heading' => __( 'Photo on hover (optional)', 'us' ),
				'type' => 'attach_image',
				'std' => $config['atts']['image_hover'],
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 190,
			),
			array(
				'param_name' => 'name',
				'heading' => us_translate( 'Name' ),
				'type' => 'textfield',
				'std' => $config['atts']['name'],
				'holder' => 'div',
				'edit_field_class' => 'vc_col-sm-6 newline',
				'weight' => 180,
			),
			array(
				'param_name' => 'role',
				'heading' => __( 'Role', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['role'],
				'edit_field_class' => 'vc_col-sm-6',
				'weight' => 170,
			),
			array(
				'param_name' => 'content',
				'type' => 'textarea',
				'holder' => 'div',
				'heading' => us_translate( 'Description' ),
				'std' => $config['content'],
				'weight' => 160,
			),
			array(
				'param_name' => 'link',
				'heading' => __( 'Link (optional)', 'us' ),
				'description' => __( 'Applies to the Name and to the Photo', 'us' ),
				'type' => 'vc_link',
				'std' => $config['atts']['link'],
				'weight' => 150,
			),
			array(
				'param_name' => 'layout',
				'heading' => __( 'Layout', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					__( 'Simple', 'us' ) => 'simple',
					__( 'Simple (rounded photo)', 'us' ) => 'simple_circle',
					__( 'Compact', 'us' ) => 'square',
					__( 'Compact (rounded photo)', 'us' ) => 'circle',
					__( 'Modern', 'us' ) => 'modern',
					__( 'Trendy', 'us' ) => 'trendy',
				),
				'std' => $config['atts']['layout'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => __( 'More Options', 'us' ),
				'weight' => 110,
			),
			array(
				'param_name' => 'effect',
				'heading' => __( 'Photo Effect', 'us' ),
				'type' => 'dropdown',
				'value' => array(
					us_translate( 'None' ) => 'none',
					__( 'Sepia', 'us' ) => 'sepia',
					__( 'Black & White', 'us' ) => 'bw',
					__( 'Faded', 'us' ) => 'faded',
					__( 'Colored', 'us' ) => 'colored',
				),
				'std' => $config['atts']['effect'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => __( 'More Options', 'us' ),
				'weight' => 100,
			),
			array(
				'param_name' => 'email',
				'heading' => us_translate( 'Email' ),
				'type' => 'textfield',
				'std' => $config['atts']['email'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => __( 'More Options', 'us' ),
				'weight' => 90,
			),
			array(
				'param_name' => 'facebook',
				'heading' => 'Facebook',
				'type' => 'textfield',
				'std' => $config['atts']['facebook'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => __( 'More Options', 'us' ),
				'weight' => 80,
			),
			array(
				'param_name' => 'twitter',
				'heading' => 'Twitter',
				'type' => 'textfield',
				'std' => $config['atts']['twitter'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => __( 'More Options', 'us' ),
				'weight' => 70,
			),
			array(
				'param_name' => 'google_plus',
				'heading' => 'Google+',
				'type' => 'textfield',
				'std' => $config['atts']['google_plus'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => __( 'More Options', 'us' ),
				'weight' => 60,
			),
			array(
				'param_name' => 'linkedin',
				'heading' => 'LinkedIn',
				'type' => 'textfield',
				'std' => $config['atts']['linkedin'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => __( 'More Options', 'us' ),
				'weight' => 50,
			),
			array(
				'param_name' => 'skype',
				'heading' => 'Skype',
				'type' => 'textfield',
				'std' => $config['atts']['skype'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => __( 'More Options', 'us' ),
				'weight' => 40,
			),
			array(
				'param_name' => 'custom_link',
				'heading' => __( 'Custom Link', 'us' ),
				'type' => 'textfield',
				'std' => $config['atts']['custom_link'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => __( 'More Options', 'us' ),
				'weight' => 30,
			),
			array(
				'param_name' => 'custom_icon',
				'heading' => __( 'Custom Link Icon', 'us' ),
				'description' => sprintf( __( '%s or %s icon name', 'us' ), '<a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a>', '<a href="https://material.io/icons/" target="_blank">Material</a>' ),
				'type' => 'textfield',
				'std' => $config['atts']['custom_icon'],
				'edit_field_class' => 'vc_col-sm-6',
				'group' => __( 'More Options', 'us' ),
				'weight' => 20,
			),
			array(
				'param_name' => 'el_class',
				'heading' => us_translate( 'Extra class name', 'js_composer' ),
				'type' => 'textfield',
				'std' => $config['atts']['el_class'],
				'group' => __( 'More Options', 'us' ),
				'weight' => 10,
			),
		),
	)
);

class WPBakeryShortCode_us_person extends WPBakeryShortCode {

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
				$logo_html .= '<img width="150" height="150" class="attachment-thumbnail ' . $element_icon . ' vc_element-icon"  data-name="' . $param_name . '" alt="" title="" style="display: none;" />';
			}
			$logo_html .= '<span class="no_image_image vc_element-icon ' . $element_icon . ( $img && ! empty( $img['p_img_large'][0] ) ? ' image-exists' : '' ) . '" />';
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
