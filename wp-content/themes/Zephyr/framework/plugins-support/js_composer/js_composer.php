<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * WPBakery Page Builder support
 *
 * @link http://codecanyon.net/item/visual-composer-page-builder-for-wordpress/242431?ref=UpSolution
 */

if ( ! class_exists( 'Vc_Manager' ) ) {

	/**
	 * @param $width
	 *
	 * @since 4.2
	 * @return bool|string
	 */
	function us_wpb_translateColumnWidthToSpan( $width ) {
		preg_match( '/(\d+)\/(\d+)/', $width, $matches );
		if ( ! empty( $matches ) ) {
			$part_x = (int) $matches[1];
			$part_y = (int) $matches[2];
			if ( $part_x > 0 && $part_y > 0 ) {
				$value = ceil( $part_x / $part_y * 12 );
				if ( $value > 0 && $value <= 12 ) {
					$width = 'vc_col-sm-' . $value;
				}
			}
		}

		return $width;
	}

	/**
	 * @param $column_offset
	 * @param $width
	 *
	 * @return mixed|string
	 */
	function us_vc_column_offset_class_merge( $column_offset, $width ) {
		if ( preg_match( '/vc_col\-sm\-\d+/', $column_offset ) ) {
			return $column_offset;
		}

		return $width . ( empty( $column_offset ) ? '' : ' ' . $column_offset );
	}

	/**
	 * @param            $subject
	 * @param            $property
	 * @param bool|false $strict
	 *
	 * @since 4.9
	 * @return bool
	 */
	function us_vc_shortcode_custom_css_has_property( $subject, $property, $strict = FALSE ) {
		$styles = array();
		$pattern = '/\{([^\}]*?)\}/i';
		preg_match( $pattern, $subject, $styles );
		if ( array_key_exists( 1, $styles ) ) {
			$styles = explode( ';', $styles[1] );
		}
		$new_styles = array();
		foreach ( $styles as $val ) {
			$val = explode( ':', $val );
			if ( is_array( $property ) ) {
				foreach ( $property as $prop ) {
					$pos = strpos( $val[0], $prop );
					$full = ( $strict ) ? ( $pos === 0 && strlen( $val[0] ) === strlen( $prop ) ) : TRUE;
					if ( $pos !== FALSE && $full ) {
						$new_styles[] = $val;
					}
				}
			} else {
				$pos = strpos( $val[0], $property );
				$full = ( $strict ) ? ( $pos === 0 && strlen( $val[0] ) === strlen( $property ) ) : TRUE;
				if ( $pos !== FALSE && $full ) {
					$new_styles[] = $val;
				}
			}
		}

		return ! empty( $new_styles );
	}

	return;
}

add_action( 'vc_before_init', 'us_vc_set_as_theme' );
function us_vc_set_as_theme() {
	vc_set_as_theme();
}

add_action( 'vc_after_init', 'us_vc_after_init' );
function us_vc_after_init() {
	$updater = vc_manager()->updater();
	$updateManager = $updater->updateManager();

	remove_filter( 'upgrader_pre_download', array( $updater, 'preUpgradeFilter' ) );
	remove_filter( 'pre_set_site_transient_update_plugins', array( $updateManager, 'check_update' ) );
	remove_filter( 'plugins_api', array( $updateManager, 'check_info' ) );
	remove_action( 'in_plugin_update_message-' . vc_plugin_name(), array( $updateManager, 'addUpgradeMessageLink' ) );
}

add_action( 'vc_after_set_mode', 'us_vc_after_set_mode' );
function us_vc_after_set_mode() {

	do_action( 'us_before_js_composer_mappings' );

	$shortcodes_config = us_config( 'shortcodes', array() );

	if ( us_get_option( 'enable_portfolio', 1 ) == 0 ) {
		unset( $shortcodes_config['us_portfolio'] );
	}

	if ( us_get_option( 'enable_testimonials', 1 ) == 0 ) {
		unset( $shortcodes_config['us_testimonials'] );
	}

	// Mapping WPBakery Page Builder backend behaviour for used shortcodes
	if ( vc_mode() != 'page' ) {
		foreach ( $shortcodes_config as $shortcode => $config ) {
			if ( isset( $config['custom_vc_map'] ) AND ! empty( $config['custom_vc_map'] ) ) {
				require $config['custom_vc_map'];
			}
		}
	}

	if ( us_get_option( 'disable_extra_vc', 1 ) == 1 ) {
		// Removing the elements that are not supported at the moment by the theme
		if ( is_admin() ) {
			foreach ( $shortcodes_config as $shortcode => $config ) {
				if ( isset( $config['supported'] ) AND ! $config['supported'] ) {
					vc_remove_element( $shortcode );
				}
			}
		} else {
			add_action( 'template_redirect', 'us_vc_disable_extra_sc', 100 );
		}

	}

	if ( ! vc_is_page_editable() ) {
		// Removing original VC styles and scripts
		add_action( 'wp_enqueue_scripts', 'us_remove_vc_base_css_js', 15 );
		function us_remove_vc_base_css_js() {
			global $us_template_directory_uri;
			if ( wp_style_is( 'font-awesome', 'registered' ) ) {
				wp_deregister_style( 'font-awesome' );
			}
			if ( us_get_option( 'disable_extra_vc', 1 ) == 1 ) {
				if ( wp_style_is( 'js_composer_front', 'registered' ) ) {
					wp_dequeue_style( 'js_composer_front' );
					wp_deregister_style( 'js_composer_front' );
				}
				if ( wp_script_is( 'wpb_composer_front_js', 'registered' ) ) {
					wp_deregister_script( 'wpb_composer_front_js' );
				}
			}
		}
	}

	if ( vc_is_page_editable() ) {
		// Disabling some of the shortcodes for front-end edit mode
		US_Shortcodes::instance()->vc_front_end_compatibility();
	}

	if ( is_admin() AND us_get_option( 'disable_extra_vc', 1 ) == 1 ) {
		// Removing grid elements
		add_action( 'admin_menu', 'us_remove_vc_grid_elements_submenu' );
		function us_remove_vc_grid_elements_submenu() {
			remove_submenu_page( VC_PAGE_MAIN_SLUG, 'edit.php?post_type=vc_grid_item' );
		}
	}

	// Disabling Front End editor for Footers
	add_action( 'current_screen', 'us_disable_frontend_for_footers' );

	do_action( 'us_after_js_composer_mappings' );
}

function us_disable_frontend_for_footers() {
	if ( function_exists( 'get_current_screen' ) ) {
		$screen = get_current_screen();
		if ( $screen->post_type == 'us_footer' ) {
			vc_disable_frontend();
		}
	}
}

function us_vc_disable_extra_sc() {
	$shortcodes_config = us_config( 'shortcodes', array() );

	foreach ( $shortcodes_config as $shortcode => $config ) {
		if ( isset( $config['supported'] ) AND ! $config['supported'] ) {
			remove_shortcode( $shortcode );
		}
	}
}

// Disabling redirect to VC welcome page
remove_action( 'init', 'vc_page_welcome_redirect' );

add_action( 'after_setup_theme', 'us_vc_init_vendor_woocommerce', 99 );
function us_vc_init_vendor_woocommerce() {
	remove_action( 'wp_enqueue_scripts', 'vc_woocommerce_add_to_cart_script' );
}

/**
 * Get image size values for selector
 *
 * @param array [$size_names] List of size names
 *
 * @return array
 */
function us_image_sizes_select_values( $size_names = NULL ) {
	if ( $size_names === NULL ) {
		$size_names = array_merge(
			array( 'full', 'large', 'medium_large' ), array_keys( us_config( 'image-sizes' ) ), array( 'medium', 'thumbnail' )
		);
	}
	$image_sizes = array();
	// For translation purposes
	$size_titles = array(
		'full' => us_translate( 'Full Size' ),
	);
	foreach ( $size_names as $size_name ) {
		$size_title = isset( $size_titles[$size_name] ) ? $size_titles[$size_name] : ucwords( $size_name );
		if ( $size_name != 'full' ) {
			// Detecting size
			$size = us_get_intermediate_image_size( $size_name );
			$size_title = ( ( $size['width'] == 0 ) ? __( 'any', 'us' ) : $size['width'] );
			$size_title .= ' x ';
			$size_title .= ( $size['height'] == 0 ) ? __( 'any', 'us' ) : $size['height'];
			if ( $size['crop'] ) {
				$size_title .= ' ' . __( 'cropped', 'us' );
			}
		}
		$image_sizes[$size_title] = $size_name;
	}

	// Custom sizes
	$custom_tnail_sizes = us_get_option( 'img_size' );
	if ( is_array( $custom_tnail_sizes ) ) {
		foreach ( $custom_tnail_sizes as $size_index => $size ) {
			$size_name = 'us_img_size_' . $size_index;
			$crop = ( ! empty( $size['crop'][0] ) );
			$width = ( ! empty( $size['width'] ) AND intval( $size['width'] ) > 0 ) ? intval( $size['width'] ) : 0;
			$height = ( ! empty( $size['height'] ) AND intval( $size['height'] ) > 0 ) ? intval( $size['height'] ) : 0;
			
			$size_title = ( ( $width == 0 ) ? __( 'any', 'us' ) : $width );
			$size_title .= ' x ';
			$size_title .= ( $height == 0 ) ? __( 'any', 'us' ) : $height;
			if ( $crop ) {
				$size_title .= ' ' . __( 'cropped', 'us' );
			}

			$image_sizes[$size_title] = $size_name;
		}
	}
	
	return apply_filters( 'us_image_sizes_select_values', $image_sizes );
}

// add_action( 'vc_load_default_templates_action', 'us_custom_template_for_vc' ); // Hook in
// function us_custom_template_for_vc() {
	// global $us_template_directory;
	// $templates = require $us_template_directory . '/framework/plugins-support/js_composer/templates.php';
	// foreach ( $templates as $template ) {
		// vc_add_default_templates( $template );
	// }
// }

add_action( 'vc_after_mapping', 'us_testimonial_map_shortcodes' );

function us_testimonial_map_shortcodes () {
	add_filter( 'vc_autocomplete_us_testimonials_ids_callback', 'us_testimonials_ids_autocomplete_suggester', 10, 1 );
	function us_testimonials_ids_autocomplete_suggester ( $query ) {
		global $wpdb;
		$testimonial_id = (int) $query;
		$post_meta_infos = $wpdb->get_results( $wpdb->prepare( "SELECT a.ID AS id, a.post_title AS title
					FROM {$wpdb->posts} AS a
					WHERE a.post_type = 'us_testimonial' AND ( a.ID = '%d' OR a.post_title LIKE '%%%s%%' )", $testimonial_id > 0 ? $testimonial_id : - 1, stripslashes( $query ), stripslashes( $query ) ), ARRAY_A );

		$results = array();
		if ( is_array( $post_meta_infos ) && ! empty( $post_meta_infos ) ) {
			foreach ( $post_meta_infos as $value ) {
				$data = array();
				$data['value'] = $value['id'];
				$data['label'] = $value['id'] . ' - ' . ( ( strlen( $value['title'] ) > 0 ) ? $value['title'] : us_translate( '(no title)' ) );
				$results[] = $data;
			}
		}

		return $results;
	}

	add_filter( 'vc_autocomplete_us_testimonials_ids_render', 'us_testimonials_ids_render', 10, 1 );

	function us_testimonials_ids_render( $query ) {
		$query = trim( $query['value'] ); // get value from requested
		if ( ! empty( $query ) ) {
			// get post
			$post_object = get_post( (int) $query );
			if ( is_object( $post_object ) ) {
				$post_title = $post_object->post_title;
				$post_id = $post_object->ID;
				$data = array();
				$data['value'] = $post_id;
				$data['label'] = $post_id . ' - ' . ( ( strlen( $post_title ) > 0 ) ? $post_title : us_translate( '(no title)' ) );

				return ! empty( $data ) ? $data : false;
			}

			return false;
		}

		return false;
	}
}

add_filter( 'us_footer_the_content', 'us_VC_fixPContent', 11 );
function us_VC_fixPContent( $content = null ) {
	if ( $content ) {
		$s = array(
			'/' . preg_quote( '</div>', '/' ) . '[\s\n\f]*' . preg_quote( '</p>', '/' ) . '/i',
			'/' . preg_quote( '<p>', '/' ) . '[\s\n\f]*' . preg_quote( '<div ', '/' ) . '/i',
			'/' . preg_quote( '<p>', '/' ) . '[\s\n\f]*' . preg_quote( '<section ', '/' ) . '/i',
			'/' . preg_quote( '</section>', '/' ) . '[\s\n\f]*' . preg_quote( '</p>', '/' ) . '/i',
		);
		$r = array(
			'</div>',
			'<div ',
			'<section ',
			'</section>',
		);
		$content = preg_replace( $s, $r, $content );

		return $content;
	}

	return null;
}

$list = array(
	'page',
	'us_portfolio',
	'us_footer',
);
vc_set_default_editor_post_types( $list );

// Hiding activation notice
add_action( 'admin_notices', 'us_hide_js_composer_activation_notice', 100 );
function us_hide_js_composer_activation_notice() {
	?>
	<script type="text/javascript">
		(function($) {
			var setCookie = function (c_name, value, exdays) {
				var exdate = new Date();
				exdate.setDate(exdate.getDate() + exdays);
				var c_value = encodeURIComponent(value) + ((null === exdays)?"":"; expires=" + exdate.toUTCString());
				document.cookie = c_name + "=" + c_value;
			};
			setCookie('vchideactivationmsg_vc11', '100', 30);
			$('#vc_license-activation-notice').remove();
		})(window.jQuery);
	</script>
	<?php
}

// Removing support for headers
add_filter( 'vc_settings_exclude_post_type', 'us_vc_settings_exclude_post_type' );
function us_vc_settings_exclude_post_type( $types ) {
	$types[] = 'us_header';

	return $types;
}

add_action( 'current_screen', 'us_header_vc_check_post_type_validation_fix' );
function us_header_vc_check_post_type_validation_fix( $current_screen ) {
	global $pagenow;
	if ( $pagenow == 'post.php' AND $current_screen->post_type == 'us_header' ) {
		add_filter( 'vc_check_post_type_validation', '__return_false', 12 );
	}
}

