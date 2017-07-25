<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: header_builder
 *
 * Advanced header builder.
 *
 * @var $name string Field name
 * @var $id string Field ID
 * @var $field array Field options
 *
 * @var $value array Current value
 */
global $usof_directory, $usof_directory_uri;

if ( ! empty( $value ) AND is_string( $value ) AND $value[0] === '{' ) {
	$value = json_decode( $value, TRUE );
}
$value = us_fix_header_settings( $value );

$output = '<div class="us-hb" data-ajaxurl="' . esc_attr( admin_url( 'admin-ajax.php' ) ) . '">';

// States
$output .= '<div class="us-hb-states">';
$output .= '<div class="us-hb-state for_default active">' . us_translate( 'Default' ) . '</div>';
$output .= '<div class="us-hb-state for_tablets">' . __( 'Tablets', 'us' ) . '</div>';
$output .= '<div class="us-hb-state for_mobiles">' . __( 'Mobiles', 'us' ) . '</div>';
$output .= '</div>';

// Workspace
$output .= '<div class="us-hb-workspace for_default">';

// Editor
if ( ! function_exists( 'ushb_get_elms_placeholders' ) ) {
	/**
	 * Prepare HTML for elements list for a certain elements area
	 *
	 * @param array $layout
	 * @param array $data Elements data
	 * @param string $place
	 *
	 * @return string
	 */
	function ushb_get_elms_placeholders( &$layout, &$data, $place ) {
		$output = '';
		if ( ! isset( $layout[ $place ] ) OR ! is_array( $layout[ $place ] ) ) {
			return $output;
		}
		foreach ( $layout[ $place ] as $elm ) {
			if ( substr( $elm, 1, 7 ) == 'wrapper' ) {
				$output .= '<div class="us-hb-editor-wrapper type_' . ( ( $elm[0] == 'h' ) ? 'horizontal' : 'vertical' );
				if ( ! isset( $layout[ $elm ] ) OR empty( $layout[ $elm ] ) ) {
					$output .= ' empty';
				}
				$output .= '" data-id="' . esc_attr( $elm ) . '">';
				$output .= '<div class="us-hb-editor-wrapper-content">';
				$output .= ushb_get_elms_placeholders( $layout, $data, $elm );
				$output .= '</div>';
				$output .= '<div class="us-hb-editor-wrapper-controls">';
				$output .= '<a href="javascript:void(0)" class="us-hb-editor-control type_add" title="' . esc_attr( __( 'Add element into wrapper', 'us' ) ) . '"></a>';
				$output .= '<a href="javascript:void(0)" class="us-hb-editor-control type_edit" title="' . esc_attr( __( 'Edit wrapper', 'us' ) ) . '"></a>';
				$output .= '<a href="javascript:void(0)" class="us-hb-editor-control type_delete" title="' . esc_attr( __( 'Delete wrapper', 'us' ) ) . '"></a>';
				$output .= '</div>';
				$output .= '</div><!-- .us-hb-editor-wrapper -->';
			} else {
				// Handling standard single element
				$type = strtok( $elm, ':' );
				$values = isset( $data[ $elm ] ) ? $data[ $elm ] : array();
				$output .= '<div class="us-hb-editor-elm type_' . $type . '" data-id="' . esc_attr( $elm ) . '">';
				$output .= '<div class="us-hb-editor-elm-content">';
				if ( $type == 'text' AND isset( $values['text'] ) AND ( ! empty( $values['text'] ) OR ! empty( $values['icon'] ) ) ) {
					if ( isset( $values['icon'] ) AND ! empty( $values['icon'] ) ) {
						$output .= us_prepare_icon_tag( $values['icon'] );
					}
					$output .= strip_tags( $values['text'] );
				} elseif ( $type == 'image' ) {
					if ( isset( $values['img'] ) AND ! empty( $values['img'] ) ) {
						$upload_image = usof_get_image_src( $values['img'] );
						$output .= '<img src="' . esc_attr( $upload_image[0] ) . '" />';
					} else {
						$output .= '<i class="fa fa-image"></i>';
					}
				} elseif ( $type == 'menu' ) {
					if ( isset( $values['source'] ) AND ! empty( $values['source'] ) ) {
						$nav_menus = us_get_nav_menus();
						if ( isset( $nav_menus[ $values['source'] ] ) ) {
							$output .= $nav_menus[ $values['source'] ];
						} else {
							$output .= $values['source'];
						}
					} else {
						$output .= us_translate( 'Menu' );
					}
				} elseif ( $type == 'additional_menu' ) {
					if ( isset( $values['source'] ) AND ! empty( $values['source'] ) ) {
						$nav_menus = us_get_nav_menus();
						if ( isset( $nav_menus[ $values['source'] ] ) ) {
							$output .= $nav_menus[ $values['source'] ];
						} else {
							$output .= $values['source'];
						}
					} else {
						$output .= __( 'Links Menu', 'us' );
					}
				} elseif ( $type == 'search' AND isset( $values['text'] ) AND ! empty( $values['text'] ) ) {
					$output .= strip_tags( $values['text'] );
				} elseif ( $type == 'dropdown' AND isset( $values['source'] ) ) {
					if ( $values['source'] == 'wpml' ) {
						$output .= 'WPML';
					} elseif ( $values['source'] == 'polylang' ) {
						$output .= 'Polylang';
					} elseif ( $values['source'] == 'qtranslate' ) {
						$output .= 'qTranslate X';
					} else {
						$output .= ( isset( $values['link_title'] ) AND ! empty( $values['link_title'] ) ) ? $values['link_title'] : __( 'Dropdown', 'us' );
					}
				} elseif ( $type == 'socials' ) {
					$socialsOutput = '';
					foreach ( $values as $key => $value ) {
						if ( $key == 'style' OR $key == 'color' OR $key == 'hover' OR substr( $key, 0, 7 ) == 'custom_' OR substr( $key, 0, 4 ) == 'size' OR $key == 'design_options' ) {
							continue;
						}
						if ( ! empty( $value ) ) {
							$socialsOutput .= '<i class="fa fa-' . $key . '"></i>';
						}
					}
					if ( isset( $values['custom_icon'] ) AND ! empty( $values['custom_icon'] ) AND isset( $values['custom_url'] ) AND ! empty( $values['custom_url'] ) ) {
						$socialsOutput .= us_prepare_icon_tag( $values['custom_icon'] );
					}
					$output .= empty( $socialsOutput ) ? __( 'Social Links', 'us' ) : $socialsOutput;
				} elseif ( $type == 'btn' ) {
					if ( isset( $values['icon'] ) AND ! empty( $values['icon'] ) ) {
						$output .= us_prepare_icon_tag( $values['icon'] );
					}
					if ( isset( $values['label'] ) AND ! empty( $values['label'] ) ) {
						$output .= strip_tags( $values['label'] );
					} else {
						$output .= __( 'Button', 'us' );
					}
				} elseif ( $type == 'html' ) {
					$output .= 'HTML';
				} elseif ( $type == 'cart' ) {
					if ( isset( $values['icon'] ) AND ! empty( $values['icon'] ) ) {
						$output .= us_prepare_icon_tag( $values['icon'] );
					}
					$output .= __( 'Cart', 'us' );
				} else {
					$output .= ucfirst( $type );
				}
				$output .= '</div>';
				$output .= '<div class="us-hb-editor-elm-controls">';
				$output .= '<a href="javascript:void(0)" class="us-hb-editor-control type_edit" title="' . esc_attr( __( 'Edit element', 'us' ) ) . '"></a>';
				$output .= '<a href="javascript:void(0)" class="us-hb-editor-control type_clone" title="' . esc_attr( __( 'Duplicate element', 'us' ) ) . '"></a>';
				$output .= '<a href="javascript:void(0)" class="us-hb-editor-control type_delete" title="' . esc_attr( __( 'Delete element', 'us' ) ) . '"></a>';
				$output .= '</div>';
				$output .= '</div>';
			}
		}

		return $output;
	}
}
$output .= '<div class="us-hb-editor type_';
$output .= ( us_arr_path( $value, 'default.options.orientation', 'hor' ) == 'ver' ) ? 'ver' : 'hor';
$output .= '">';
foreach ( array( 'top', 'middle', 'bottom' ) as $at_y ) {
	$output .= '<div class="us-hb-editor-row at_' . $at_y;
	if ( ( $at_y == 'top' OR $at_y == 'bottom' ) AND ! us_arr_path( $value, 'default.options.' . $at_y . '_show' ) ) {
		$output .= ' disabled';
	}
	$output .= '">';
	$output .= '<div class="us-hb-editor-row-h">';
	foreach ( array( 'left', 'center', 'right' ) as $at_x ) {
		$output .= '<div class="us-hb-editor-cell at_' . $at_x . '">';
		// Output inner widgets
		$output .= ushb_get_elms_placeholders( $value['default']['layout'], $value['data'], $at_y . '_' . $at_x );
		$output .= '<a href="javascript:void(0)" class="us-hb-editor-add" title="' . esc_attr( __( 'Add element', 'us' ) ) . '"></a>';
		$output .= '</div>';
	}
	$output .= '</div>';
	$output .= '</div><!-- .us-hb-editor-row -->';
}

// Outputting hidden elements
$output .= '<div class="us-hb-editor-row for_hidden">';
$output .= '<div class="us-hb-editor-row-desc">' . __( 'Hidden Elements', 'us' ) . '</div>';
$output .= '<div class="us-hb-editor-row-h">';
$output .= ushb_get_elms_placeholders( $value['default']['layout'], $value['data'], 'hidden' );
$output .= '</div>';
$output .= '</div><!-- .us-hb-editor-row.for_hidden -->';
$output .= '</div><!-- .us-hb-editor -->';

// Options
$output .= '<div class="us-hb-options">';
$hb_options_sections = array(
	'global' => __( 'General Header Settings', 'us' ),
	'top' => __( 'Top Area Settings', 'us' ),
	'middle' => __( 'Main Area Settings', 'us' ),
	'bottom' => __( 'Bottom Area Settings', 'us' ),
);

$options_values = us_arr_path( $value, 'default.options', array() );
// Setting starting state to properly handle show_if rules
$options_values['state'] = 'default';
foreach ( $hb_options_sections as $hb_section => $hb_section_title ) {
	$output .= '<div class="us-hb-options-section' . ( ( $hb_section == 'global' ) ? ' active' : '' ) . '" data-id="' . $hb_section . '">';
	$output .= '<div class="us-hb-options-section-title">' . $hb_section_title . '</div>';
	$output .= '<div class="us-hb-options-section-content" style="display: ' . ( ( $hb_section == 'global' ) ? 'block' : 'none' ) . ';">';
	foreach ( us_config( 'header-settings.options.' . $hb_section, array() ) as $field_name => $fld ) {
		if ( ! isset( $fld['type'] ) ) {
			continue;
		}
		$field_html = us_get_template(
			'vendor/usof/templates/field', array(
				'name' => $field_name,
				'id' => 'hb_opt_' . $field_name,
				'field' => $fld,
				'values' => $options_values,
			)
		);
		// Changing rows' classes to prevent auto-init of these rows as main fields
		$field_html = preg_replace( '~usof\-form\-(row|wrapper) ~', 'usof-subform-$1 ', $field_html );
		$output .= $field_html;
	}
	$output .= '</div><!-- .us-hb-options-section-content -->';
	$output .= '</div><!-- .us-hb-options-section -->';
}
$output .= ' </div ><!-- .us-hb-options -->';

$output .= '<div class="us-hb-params hidden"';
$output .= us_pass_data_to_js(
	array(
		'navMenus' => us_get_nav_menus(),
		// TODO Default values
	)
);
$output .= '></div>';
$output .= '<div class="us-hb-value hidden"' . us_pass_data_to_js( $value ) . '></div>';

// Elements' default values
$elms_titles = array();
$elms_defaults = array();
foreach ( us_config( 'header-settings.elements', array() ) as $type => $elm ) {
	$elms_titles[ $type ] = isset( $elm['title'] ) ? $elm['title'] : $type;
	$elms_defaults[ $type ] = us_get_header_elm_defaults( $type );
}
$output .= '<div class="us-hb-defaults hidden"' . us_pass_data_to_js( $elms_defaults ) . '></div>';
$translations = array(
	'template_replace_confirm' => __( 'Selected template will overwrite all your current elements and settings! Are you sure want to apply it?', 'us' ),
	'orientation_change_confirm' => __( 'Are you sure want to change the header orientation? Some of your elements\' positions may be changed', 'us' ),
	'element_delete_confirm' => __( 'Are you sure want to delete the element?', 'us' ),
	'add_element' => __( 'Add element into wrapper', 'us' ),
	'edit_element' => __( 'Edit element', 'us' ),
	'clone_element' => __( 'Duplicate element', 'us' ),
	'delete_element' => __( 'Delete element', 'us' ),
	'edit_wrapper' => __( 'Edit wrapper', 'us' ),
	'delete_wrapper' => __( 'Delete wrapper', 'us' ),
	'menu' => us_translate( 'Menu' ),
	'additional_menu' => __( 'Links Menu', 'us' ),
	'dropdown' => __( 'Dropdown', 'us' ),
	'social_links' => __( 'Social Links', 'us' ),
	'button' => __( 'Button', 'us' ),
	'cart' => __( 'Cart', 'us' ),
);
$output .= '<div class="us-hb-translations hidden"' . us_pass_data_to_js( $translations ) . '></div>';

$output .= '</div>';

$output .= '<div class="us-screenlock"><div>' . sprintf( __( '<a href="%s">Activate the theme</a> to unlock Header Builder', 'us' ), admin_url( 'admin.php?page=us-home' ) ) . '</div></div>';

// List of elements that can be added
$output .= us_get_template( 'templates/elist' );

// Empty editor window for loading the elements afterwards
$output .= us_get_template(
	'templates/ebuilder', array(
		'titles' => $elms_titles,
		'body' => '',
	)
);

// Export & Import
$output .= us_get_template( 'templates/export_import' );

// Empty header templates window for loading the templates afterwards
$output .= us_get_template(
	'templates/htemplates', array(
		'body' => '',
	)
);

echo $output;
