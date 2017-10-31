<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme's Options
 *
 * @filter us_config_theme-options
 */

global $us_template_directory_uri, $wp_registered_sidebars, $usof_img_sizes, $usof_enable_portfolio, $usof_wp_pages, $usof_supported_cpt, $usof_footers_list;
if ( ! isset( $usof_img_sizes ) ) {
	$usof_img_sizes = array();
}
if ( ! isset( $usof_enable_portfolio ) ) {
	$usof_enable_portfolio = 1;
}
if ( ! isset( $usof_wp_pages ) ) {
	$usof_wp_pages = array();
}
if ( ! isset( $usof_supported_cpt ) ) {
	$usof_supported_cpt = array();
}
if ( ! isset( $usof_footers_list ) ) {
	$usof_footers_list = array();
}

$usof_assets = array();
$assets_config = us_config( 'assets', array() );
foreach ( $assets_config as $component => $component_atts ) {
	if ( isset( $component_atts['hidden'] ) AND $component_atts['hidden'] ) {
		continue;
	}
	$usof_assets[$component] = array(
		'title' => $component_atts['title'],
		'css_size' => $component_atts['css_size'],
		'group' => ( isset( $component_atts['group'] ) ) ? $component_atts['group'] : NULL,
	);
	if ( isset( $component_atts['apply_if'] ) ) {
		$usof_assets[$component]['apply_if'] = $component_atts['apply_if'];
	}
}

$optimize_assets_add_class = ' blocked';
$optimize_assets_alert_add_class = '';
$upload_dir = wp_upload_dir();
if ( wp_is_writable( $upload_dir['basedir'] ) ) {
	$optimize_assets_add_class = '';
	$optimize_assets_alert_add_class = ' hidden';
}

// Getting Sidebars
$sidebars_options = array();
if ( is_array( $wp_registered_sidebars ) && ! empty( $wp_registered_sidebars ) ) {
	foreach ( $wp_registered_sidebars as $sidebar ) {
		// Add default sidebar to the beginning
		if ( $sidebar['id'] == 'default_sidebar' ) {
			$sidebars_options = array_merge( array( $sidebar['id'] => $sidebar['name'] ), $sidebars_options );
		} else {
			$sidebars_options[$sidebar['id']] = $sidebar['name'];
		}
	}
}

// Getting Custom Post Types
$post_type_args = array(
	'public' => TRUE,
	'_builtin' => FALSE,
);
$post_types = get_post_types( $post_type_args, 'objects', 'and' );
$supported_post_types = array(
	// Theme
	'us_portfolio',
	'us_testimonial',
	'us_header',
	'us_footer',
	// WooCommerce
	'product',
	// bbPress
	'forum',
	'topic',
	'reply',
	// The Events Calendar
	'tribe_events',
	'tribe-ea-record'
);
$supported_cpt_values = array();
foreach ( $post_types as $post_type_name => $post_type ) {
	if ( ! in_array( $post_type_name, $supported_post_types ) ) {
		$supported_cpt_values[$post_type_name] = $post_type->labels->name;
	}
}

// Getting Social Links
$social_links = us_config( 'social_links' );
$social_links_config = array();
foreach ( $social_links as $name => $title ) {
	$social_links_config['header_socials_' . $name] = array(
		'placeholder' => $title,
		'type' => 'text',
		'std' => '',
		'classes' => 'for_social cols_3',
	);
}

// Additional Sidebar Options for supported CPT
$usof_cpt_sidebars_config = array();
foreach ( $usof_supported_cpt as $cpt_name ) {
	if ( ! isset( $supported_cpt_values[$cpt_name] ) ) {
		continue;
	}
	$usof_cpt_sidebars_config = array_merge( $usof_cpt_sidebars_config, array(
		'h_sidebar_' . $cpt_name => array(
			'title' => $supported_cpt_values[$cpt_name],
			'type' => 'heading',
			'classes' => 'with_separator',
		),
		'sidebar_' . $cpt_name => array(
			'type' => 'switch',
			'text' => __( 'Show Sidebar', 'us' ),
			'std' => 0,
			'classes' => 'width_full',
		),
		'sidebar_' . $cpt_name . '_id' => array(
			'title' => __( 'Sidebar', 'us' ),
			'description' => sprintf( __( 'You can edit selected sidebar or create a new one on the %s page', 'us' ), '<a href="' . admin_url( 'widgets.php' ) . '" target="_blank">' . us_translate( 'Widgets' ) . '</a>' ),
			'type' => 'select',
			'options' => $sidebars_options,
			'std' => 'default_sidebar',
			'show_if' => array( 'sidebar_' . $cpt_name, '=', 1 ),
		),
		'sidebar_' . $cpt_name . '_pos' => array(
			'title' => __( 'Sidebar Position', 'us' ),
			'type' => 'radio',
			'options' => array(
				'left' => us_translate( 'Left' ),
				'right' => us_translate( 'Right' ),
			),
			'std' => 'right',
			'show_if' => array( 'sidebar_' . $cpt_name, '=', 1 ),
		),
	) );
}

// Options Config
return array(
	'general' => array(
		'title' => us_translate_x( 'General', 'settings screen' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/mixer',
		'fields' => array(
			'maintenance_mode' => array(
				'title' => __( 'Maintenance Mode', 'us' ),
				'description' => __( 'When this option is ON, all not logged in users will see only specific page selected by you. This is useful when your site is under construction.', 'us' ),
				'type' => 'switch',
				'text' => __( 'Show for site visitors only specific page', 'us' ),
				'std' => 0,
				'classes' => 'color_yellow',
			),
			'maintenance_page' => array(
				'type' => 'select',
				'options' => $usof_wp_pages,
				'std' => 'maintenance-page',
				'classes' => 'for_above',
				'show_if' => array( 'maintenance_mode', '=', TRUE ),
			),
			'maintenance_503' => array(
				'description' => __( 'When this option is ON, your site will send HTTP 503 response to search engines. Use this option only for short period of time.', 'us' ),
				'type' => 'switch',
				'text' => __( 'Enable "503 Service Unavailable" status', 'us' ),
				'std' => 0,
				'classes' => 'for_above',
				'show_if' => array( 'maintenance_mode', '=', TRUE ),
			),
			'site_icon' => array(
				'title' => us_translate( 'Site Icon' ),
				'description' => sprintf( us_translate( 'The Site Icon is used as a browser and app icon for your site. Icons must be square, and at least %s pixels wide and tall.' ), '<strong>512</strong>' ),
				'type' => 'upload',
				'extension' => 'png,jpg,jpeg,gif',
			),
			'preloader' => array(
				'title' => __( 'Preloader Screen', 'us' ),
				'type' => 'select',
				'options' => array(
					'disabled' => __( 'Disabled', 'us' ),
					'1' => sprintf( __( 'Shows Preloader %d', 'us' ), 1 ),
					'2' => sprintf( __( 'Shows Preloader %d', 'us' ), 2 ),
					'3' => sprintf( __( 'Shows Preloader %d', 'us' ), 3 ),
					'4' => sprintf( __( 'Shows Preloader %d', 'us' ), 4 ),
					'5' => sprintf( __( 'Shows Preloader %d', 'us' ), 5 ),
					'custom' => __( 'Shows Custom Image', 'us' ),
				),
				'std' => 'disabled',
			),
			'preloader_image' => array(
				'title' => '',
				'type' => 'upload',
				'extension' => 'png,jpg,jpeg,gif,svg',
				'classes' => 'for_above',
				'show_if' => array( 'preloader', '=', 'custom' ),
			),
			'rounded_corners' => array(
				'title' => __( 'Rounded Corners', 'us' ),
				'type' => 'switch',
				'text' => __( 'Enable rounded corners of theme elements', 'us' ),
				'std' => 1,
			),
			'links_underline' => array(
				'title' => __( 'Links Underline', 'us' ),
				'type' => 'switch',
				'text' => __( 'Underline text links on hover', 'us' ),
				'std' => 0,
			),
			'back_to_top' => array(
				'title' => __( '"Back to top" Button', 'us' ),
				'type' => 'switch',
				'text' => __( 'Enable button which allows scroll a page back to the top', 'us' ),
				'std' => 1,
			),
			'wrapper_back_to_top_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'force_right',
				'show_if' => array( 'back_to_top', '=', TRUE ),
			),
			'back_to_top_pos' => array(
				'title' => __( 'Button Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'right',
				'classes' => 'width_full cols_2',
			),
			'back_to_top_color' => array(
				'type' => 'color',
				'title' => __( 'Button Color', 'us' ),
				'std' => 'rgba(0,0,0,0.3)',
				'classes' => 'width_full cols_2',
			),
			'back_to_top_display' => array(
				'title' => __( 'Show Button after page is scrolled to', 'us' ),
				'description' => __( '1vh equals 1% of the screen height', 'us' ),
				'type' => 'slider',
				'min' => 10,
				'max' => 200,
				'step' => 10,
				'std' => 100,
				'postfix' => 'vh',
				'classes' => 'width_full',
			),
			'wrapper_back_to_top_end' => array(
				'type' => 'wrapper_end',
			),
			'gmaps_api_key' => array(
				'title' => __( 'Google Maps API Key', 'us' ),
				'description' => __( 'The API key is required for the domains created after June 22, 2016.', 'us' ) . ' <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">' . __( 'Get API key', 'us' ) . '</a>',
				'type' => 'text',
				'std' => '',
			),
			'custom_post_types_support' => array(
				'title' => __( 'Support of Custom Post Types', 'us' ),
				'description' => __( 'Select custom post type to enable customization of its Header, Sidebar, Title Bar and Footer.', 'us' ),
				'type' => 'checkboxes',
				'options' => $supported_cpt_values,
				'classes' => ( count( $supported_cpt_values ) == 0 ) ? 'hidden' : '',
				'std' => array(),
			),
		),
	),
	'layout' => array(
		'title' => __( 'Site Layout', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/layout',
		'fields' => array(
			'responsive_layout' => array(
				'title' => __( 'Responsive Layout', 'us' ),
				'type' => 'switch',
				'text' => __( 'Enable responsive layout', 'us' ),
				'std' => 1,
			),
			'canvas_layout' => array(
				'title' => __( 'Site Canvas Layout', 'us' ),
				'type' => 'imgradio',
				'options' => array(
					'wide' => 'framework/admin/img/usof/canvas-wide',
					'boxed' => 'framework/admin/img/usof/canvas-boxed',
				),
				'std' => 'wide',
			),
			'color_body_bg' => array(
				'type' => 'color',
				'title' => __( 'Body Background Color', 'us' ),
				'std' => '#eeeeee',
				'show_if' => array( 'canvas_layout', '=', 'boxed' ),
			),
			'body_bg_image' => array(
				'title' => __( 'Body Background Image', 'us' ),
				'type' => 'upload',
				'show_if' => array( 'canvas_layout', '=', 'boxed' ),
			),
			'wrapper_body_bg_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'force_right',
				'show_if' => array(
					array( 'canvas_layout', '=', 'boxed' ),
					'and',
					array( 'body_bg_image', '!=', '' ),
				),
			),
			'body_bg_image_size' => array(
				'title' => __( 'Background Image Size', 'us' ),
				'type' => 'radio',
				'options' => array(
					'cover' => __( 'Fill Area', 'us' ),
					'contain' => __( 'Fit to Area', 'us' ),
					'initial' => __( 'Initial', 'us' ),
				),
				'std' => 'cover',
				'classes' => 'width_full',
			),
			'body_bg_image_repeat' => array(
				'title' => __( 'Background Image Repeat', 'us' ),
				'type' => 'radio',
				'options' => array(
					'repeat' => __( 'Repeat', 'us' ),
					'repeat-x' => __( 'Horizontally', 'us' ),
					'repeat-y' => __( 'Vertically', 'us' ),
					'no-repeat' => us_translate( 'None' ),
				),
				'std' => 'repeat',
				'classes' => 'width_full',
			),
			'body_bg_image_position' => array(
				'title' => __( 'Background Image Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'top left' => '<span class="dashicons dashicons-arrow-left-alt"></span>',
					'top center' => '<span class="dashicons dashicons-arrow-up-alt"></span>',
					'top right' => '<span class="dashicons dashicons-arrow-right-alt"></span>',
					'center left' => '<span class="dashicons dashicons-arrow-left-alt"></span>',
					'center center' => '<span class="dashicons dashicons-marker"></span>',
					'center right' => '<span class="dashicons dashicons-arrow-right-alt"></span>',
					'bottom left' => '<span class="dashicons dashicons-arrow-left-alt"></span>',
					'bottom center' => '<span class="dashicons dashicons-arrow-down-alt"></span>',
					'bottom right' => '<span class="dashicons dashicons-arrow-right-alt"></span>',
				),
				'std' => 'top left',
				'classes' => 'bgpos width_full',
			),
			'body_bg_image_attachment' => array(
				'type' => 'switch',
				'text' => us_translate( 'Scroll with Page' ),
				'std' => 1,
				'classes' => 'width_full',
			),
			'wrapper_body_bg_end' => array(
				'type' => 'wrapper_end',
			),
			'site_canvas_width' => array(
				'title' => __( 'Site Canvas Width', 'us' ),
				'type' => 'slider',
				'min' => 1000,
				'max' => 1700,
				'step' => 10,
				'std' => 1300,
				'postfix' => 'px',
				'show_if' => array( 'canvas_layout', '=', 'boxed' ),
			),
			'site_content_width' => array(
				'title' => __( 'Site Content Width', 'us' ),
				'type' => 'slider',
				'min' => 900,
				'max' => 1600,
				'step' => 10,
				'std' => 1140,
				'postfix' => 'px',
			),
			'sidebar_width' => array(
				'title' => __( 'Sidebar Width', 'us' ),
				'description' => sprintf( __( 'Relative to the value of "%s" option.', 'us' ), __( 'Site Content Width', 'us' ) ) . ' ' . __( 'Used for pages with sidebar.', 'us' ),
				'type' => 'slider',
				'min' => 20,
				'max' => 50,
				'std' => 25,
				'postfix' => '%',
			),
			'content_width' => array(
				'title' => __( 'Content Width', 'us' ),
				'description' => sprintf( __( 'Relative to the value of "%s" option.', 'us' ), __( 'Site Content Width', 'us' ) ) . ' ' . __( 'Used for pages with sidebar.', 'us' ),
				'type' => 'slider',
				'min' => 50,
				'max' => 80,
				'std' => 70,
				'postfix' => '%',
			),
			'columns_stacking_width' => array(
				'title' => __( 'Columns Stacking Width', 'us' ),
				'description' => __( 'When screen width is less than this value, all columns within a row will become a single column.', 'us' ),
				'type' => 'slider',
				'min' => 768,
				'max' => 1025,
				'std' => 768,
				'postfix' => 'px',
			),
			'disable_effects_width' => array(
				'title' => __( 'Effects Disabling Width', 'us' ),
				'description' => __( 'When screen width is less than this value, vertical parallax and animation of elements appearance will be disabled.', 'us' ),
				'type' => 'slider',
				'min' => 300,
				'max' => 1025,
				'std' => 900,
				'postfix' => 'px',
			),
			'row_height' => array(
				'title' => __( 'Row Height by default', 'us' ),
				'type' => 'select',
				'options' => array(
					'auto' => __( 'Equals the content height', 'us' ),
					'small' => __( 'Small', 'us' ),
					'medium' => __( 'Medium', 'us' ),
					'large' => __( 'Large', 'us' ),
					'huge' => __( 'Huge', 'us' ),
					'full' => __( 'Full Screen', 'us' ),
				),
				'std' => 'medium',
			),
		),
	),
	'colors' => array(
		'title' => us_translate( 'Colors' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/colors',
		'fields' => array(

			// Color Schemes
			'color_style' => array(
				'title' => __( 'Choose Website Color Scheme', 'us' ),
				'type' => 'style_scheme',
			),

			'change_colors_start' => array(
				'type' => 'wrapper_start',
			),

			// Header colors
			'change_header_colors_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'type_simple',
			),
			'h_colors_1' => array(
				'title' => __( 'Header colors', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'color_header_top_bg' => array(
				'type' => 'color',
				'text' => __( 'Top Area Background Color', 'us' ),
			),
			'color_header_top_text' => array(
				'type' => 'color',
				'text' => __( 'Top Area Text Color', 'us' ),
			),
			'color_header_top_text_hover' => array(
				'type' => 'color',
				'text' => __( 'Top Area Text Hover Color', 'us' ),
			),
			'color_header_middle_bg' => array(
				'type' => 'color',
				'text' => __( 'Main Area Background Color', 'us' ),
			),
			'color_header_middle_text' => array(
				'type' => 'color',
				'text' => __( 'Main Area Text Color', 'us' ),
			),
			'color_header_middle_text_hover' => array(
				'type' => 'color',
				'text' => __( 'Main Area Text Hover Color', 'us' ),
			),
			'color_header_bottom_bg' => array(
				'type' => 'color',
				'text' => __( 'Bottom Area Background Color', 'us' ),
			),
			'color_header_bottom_text' => array(
				'type' => 'color',
				'text' => __( 'Bottom Area Text Color', 'us' ),
			),
			'color_header_bottom_text_hover' => array(
				'type' => 'color',
				'text' => __( 'Bottom Area Text Hover Color', 'us' ),
			),
			'color_header_transparent_text' => array(
				'type' => 'color',
				'text' => __( 'Transparent Header Text Color', 'us' ),
			),
			'color_header_transparent_text_hover' => array(
				'type' => 'color',
				'text' => __( 'Transparent Header Hover Text Color', 'us' ),
			),
			'color_header_search_bg' => array(
				'type' => 'color',
				'text' => __( 'Search Field Background Color', 'us' ),
			),
			'color_header_search_text' => array(
				'type' => 'color',
				'text' => __( 'Search Field Text Color', 'us' ),
			),
			'change_header_colors_end' => array(
				'type' => 'wrapper_end',
			),

			// Header Menu colors
			'change_menu_colors_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'type_simple',
			),
			'h_colors_2' => array(
				'title' => __( 'Header Menu colors', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'color_menu_transparent_active_text' => array(
				'type' => 'color',
				'text' => __( 'Transparent Menu Active Text Color', 'us' ),
			),
			'color_menu_active_bg' => array(
				'type' => 'color',
				'text' => __( 'Menu Active Background Color', 'us' ),
			),
			'color_menu_active_text' => array(
				'type' => 'color',
				'text' => __( 'Menu Active Text Color', 'us' ),
			),
			'color_menu_hover_bg' => array(
				'type' => 'color',
				'text' => __( 'Menu Hover Background Color', 'us' ),
			),
			'color_menu_hover_text' => array(
				'type' => 'color',
				'text' => __( 'Menu Hover Text Color', 'us' ),
			),
			'color_drop_bg' => array(
				'type' => 'color',
				'text' => __( 'Dropdown Background Color', 'us' ),
			),
			'color_drop_text' => array(
				'type' => 'color',
				'text' => __( 'Dropdown Text Color', 'us' ),
			),
			'color_drop_hover_bg' => array(
				'type' => 'color',
				'text' => __( 'Dropdown Hover Background Color', 'us' ),
			),
			'color_drop_hover_text' => array(
				'type' => 'color',
				'text' => __( 'Dropdown Hover Text Color', 'us' ),
			),
			'color_drop_active_bg' => array(
				'type' => 'color',
				'text' => __( 'Dropdown Active Background Color', 'us' ),
			),
			'color_drop_active_text' => array(
				'type' => 'color',
				'text' => __( 'Dropdown Active Text Color', 'us' ),
			),
			'color_menu_button_bg' => array(
				'type' => 'color',
				'text' => __( 'Menu Button Background Color', 'us' ),
			),
			'color_menu_button_text' => array(
				'type' => 'color',
				'text' => __( 'Menu Button Text Color', 'us' ),
			),
			'color_menu_button_hover_bg' => array(
				'type' => 'color',
				'text' => __( 'Menu Button Hover Background Color', 'us' ),
			),
			'color_menu_button_hover_text' => array(
				'type' => 'color',
				'text' => __( 'Menu Button Hover Text Color', 'us' ),
			),
			'change_menu_colors_end' => array(
				'type' => 'wrapper_end',
			),

			// Content colors
			'change_content_colors_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'type_simple',
			),
			'h_colors_3' => array(
				'title' => __( 'Content colors', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'color_content_bg' => array(
				'type' => 'color',
				'text' => __( 'Background Color', 'us' ),
			),
			'color_content_bg_alt' => array(
				'type' => 'color',
				'text' => __( 'Alternate Background Color', 'us' ),
			),
			'color_content_border' => array(
				'type' => 'color',
				'text' => __( 'Border Color', 'us' ),
			),
			'color_content_heading' => array(
				'type' => 'color',
				'text' => __( 'Heading Color', 'us' ),
			),
			'color_content_text' => array(
				'type' => 'color',
				'text' => __( 'Text Color', 'us' ),
			),
			'color_content_link' => array(
				'type' => 'color',
				'text' => __( 'Link Color', 'us' ),
			),
			'color_content_link_hover' => array(
				'type' => 'color',
				'text' => __( 'Link Hover Color', 'us' ),
			),
			'color_content_primary' => array(
				'type' => 'color',
				'text' => __( 'Primary Color', 'us' ),
			),
			'color_content_secondary' => array(
				'type' => 'color',
				'text' => __( 'Secondary Color', 'us' ),
			),
			'color_content_faded' => array(
				'type' => 'color',
				'text' => __( 'Faded Elements Color', 'us' ),
			),
			'change_content_colors_end' => array(
				'type' => 'wrapper_end',
			),

			// Alternate Content colors
			'change_alt_content_colors_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'type_simple',
			),
			'h_colors_4' => array(
				'title' => __( 'Alternate Content colors', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'color_alt_content_bg' => array(
				'type' => 'color',
				'text' => __( 'Background Color', 'us' ),
			),
			'color_alt_content_bg_alt' => array(
				'type' => 'color',
				'text' => __( 'Alternate Background Color', 'us' ),
			),
			'color_alt_content_border' => array(
				'type' => 'color',
				'text' => __( 'Border Color', 'us' ),
			),
			'color_alt_content_heading' => array(
				'type' => 'color',
				'text' => __( 'Heading Color', 'us' ),
			),
			'color_alt_content_text' => array(
				'type' => 'color',
				'text' => __( 'Text Color', 'us' ),
			),
			'color_alt_content_link' => array(
				'type' => 'color',
				'text' => __( 'Link Color', 'us' ),
			),
			'color_alt_content_link_hover' => array(
				'type' => 'color',
				'text' => __( 'Link Hover Color', 'us' ),
			),
			'color_alt_content_primary' => array(
				'type' => 'color',
				'text' => __( 'Primary Color', 'us' ),
			),
			'color_alt_content_secondary' => array(
				'type' => 'color',
				'text' => __( 'Secondary Color', 'us' ),
			),
			'color_alt_content_faded' => array(
				'type' => 'color',
				'text' => __( 'Faded Elements Color', 'us' ),
			),
			'change_alt_content_colors_end' => array(
				'type' => 'wrapper_end',
			),

			// Top Footer colors
			'change_subfooter_colors_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'type_simple',
			),
			'h_colors_5' => array(
				'title' => __( 'Top Footer colors', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'color_subfooter_bg' => array(
				'type' => 'color',
				'text' => __( 'Background Color', 'us' ),
			),
			'color_subfooter_bg_alt' => array(
				'type' => 'color',
				'text' => __( 'Alternate Background Color', 'us' ),
			),
			'color_subfooter_border' => array(
				'type' => 'color',
				'text' => __( 'Border Color', 'us' ),
			),
			'color_subfooter_text' => array(
				'type' => 'color',
				'text' => __( 'Text Color', 'us' ),
			),
			'color_subfooter_link' => array(
				'type' => 'color',
				'text' => __( 'Link Color', 'us' ),
			),
			'color_subfooter_link_hover' => array(
				'type' => 'color',
				'text' => __( 'Link Hover Color', 'us' ),
			),
			'change_subfooter_colors_end' => array(
				'type' => 'wrapper_end',
			),

			// Bottom Footer colors
			'change_footer_colors_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'type_simple',
			),
			'h_colors_6' => array(
				'title' => __( 'Bottom Footer colors', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'color_footer_bg' => array(
				'type' => 'color',
				'text' => __( 'Background Color', 'us' ),
			),
			'color_footer_bg_alt' => array(
				'type' => 'color',
				'text' => __( 'Alternate Background Color', 'us' ),
			),
			'color_footer_border' => array(
				'type' => 'color',
				'text' => __( 'Border Color', 'us' ),
			),
			'color_footer_text' => array(
				'type' => 'color',
				'text' => __( 'Text Color', 'us' ),
			),
			'color_footer_link' => array(
				'type' => 'color',
				'text' => __( 'Link Color', 'us' ),
			),
			'color_footer_link_hover' => array(
				'type' => 'color',
				'text' => __( 'Link Hover Color', 'us' ),
			),
			'change_footer_colors_end' => array(
				'type' => 'wrapper_end',
			),
			'change_colors_end' => array(
				'type' => 'wrapper_end',
			),
		),
	),
	'header' => array(
		'title' => _x( 'Header', 'site top area', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/header',
		'fields' => array_merge(
			array(
				'h_header_1' => array(
					'title' => __( 'Layout', 'us' ),
					'type' => 'heading',
					'classes' => 'with_separator',
				),
				'header_layout' => array(
					'type' => 'imgradio',
					'options' => array(
						'simple_1' => 'framework/admin/img/usof/ht-standard',
						'extended_1' => 'framework/admin/img/usof/ht-extended',
						'extended_2' => 'framework/admin/img/usof/ht-advanced',
						'centered_1' => 'framework/admin/img/usof/ht-centered',
						'vertical_1' => 'framework/admin/img/usof/ht-sided',
					),
					'std' => 'simple_1',
					'classes' => 'width_full',
				),
				'header_sticky' => array(
					'title' => __( 'Sticky Header', 'us' ),
					'type' => 'checkboxes',
					'options' => array(
						'default' => __( 'On Desktops', 'us' ),
						'tablets' => __( 'On Tablets', 'us' ),
						'mobiles' => __( 'On Mobiles', 'us' ),
					),
					'description' => __( 'Fix the header at the top of a page during scroll', 'us' ),
					'std' => array( 'default', 'tablets', 'mobiles' ),
					'show_if' => array( 'header_layout', '!=', 'vertical_1' ),
				),
				'header_transparent' => array(
					'title' => __( 'Transparent Header', 'us' ),
					'type' => 'switch',
					'text' => __( 'Make the header transparent at its initial position', 'us' ),
					'std' => 0,
					'show_if' => array( 'header_layout', '!=', 'vertical_1' ),
				),
				'header_fullwidth' => array(
					'title' => __( 'Full Width Content', 'us' ),
					'type' => 'switch',
					'text' => __( 'Stretch header content to the screen width', 'us' ),
					'std' => 0,
					'show_if' => array( 'header_layout', '!=', 'vertical_1' ),
				),
				'header_top_height' => array(
					'title' => __( 'Top Area Height', 'us' ),
					'type' => 'slider',
					'min' => 36,
					'max' => 300,
					'std' => 40,
					'postfix' => 'px',
					'show_if' => array( 'header_layout', '=', 'extended_1' ),
				),
				'header_top_sticky_height' => array(
					'title' => __( 'Top Sticky Area Height', 'us' ),
					'type' => 'slider',
					'min' => 0,
					'max' => 300,
					'std' => 0,
					'postfix' => 'px',
					'show_if' => array(
						array( 'header_sticky', 'has', 'default' ),
						'and',
						array( 'header_layout', '=', 'extended_1' ),
					),
				),
				'header_middle_height' => array(
					'title' => __( 'Main Area Height', 'us' ),
					'type' => 'slider',
					'min' => 50,
					'max' => 300,
					'std' => 100,
					'postfix' => 'px',
					'show_if' => array( 'header_layout', '!=', 'vertical_1' ),
				),
				'header_middle_sticky_height' => array(
					'title' => __( 'Main Sticky Area Height', 'us' ),
					'type' => 'slider',
					'min' => 0,
					'max' => 300,
					'std' => 50,
					'postfix' => 'px',
					'show_if' => array(
						array( 'header_sticky', 'has', 'default' ),
						'and',
						array( 'header_layout', '!=', 'vertical_1' ),
					),
				),
				'header_bottom_height' => array(
					'title' => __( 'Bottom Area Height', 'us' ),
					'type' => 'slider',
					'min' => 36,
					'max' => 300,
					'std' => 50,
					'postfix' => 'px',
					'show_if' => array( 'header_layout', 'in', array( 'extended_2', 'centered_1' ) ),
				),
				'header_bottom_sticky_height' => array(
					'title' => __( 'Bottom Sticky Area Height', 'us' ),
					'type' => 'slider',
					'min' => 0,
					'max' => 300,
					'std' => 50,
					'postfix' => 'px',
					'show_if' => array(
						array( 'header_sticky', 'has', 'default' ),
						'and',
						array( 'header_layout', 'in', array( 'extended_2', 'centered_1' ) ),
					),
				),
				'header_main_width' => array(
					'title' => __( 'Header Width', 'us' ),
					'type' => 'slider',
					'min' => 200,
					'max' => 400,
					'std' => 300,
					'postfix' => 'px',
					'show_if' => array( 'header_layout', '=', 'vertical_1' ),
				),
				'header_invert_logo_pos' => array(
					'title' => __( 'Inverted Logo Position', 'us' ),
					'type' => 'switch',
					'text' => __( 'Place Logo to the right side of the Header', 'us' ),
					'std' => 0,
					'show_if' => array( 'header_layout', 'in', array( 'simple_1', 'extended_1', 'extended_2' ) ),
				),
				'h_header_2' => array(
					'title' => __( 'Header Elements', 'us' ),
					'type' => 'heading',
					'classes' => 'with_separator',
				),
				'header_search_show' => array(
					'type' => 'switch',
					'text' => us_translate( 'Search' ),
					'std' => 1,
					'classes' => 'width_full',
				),
				'wrapper_search_start' => array(
					'type' => 'wrapper_start',
					'show_if' => array( 'header_search_show', '=', TRUE ),
				),
				'header_search_layout' => array(
					'title' => __( 'Layout', 'us' ),
					'type' => 'select',
					'options' => array(
						'simple' => __( 'Simple', 'us' ),
						'modern' => __( 'Modern', 'us' ),
						'fullwidth' => __( 'Full Width', 'us' ),
						'fullscreen' => __( 'Full Screen', 'us' ),
					),
					'std' => 'fullscreen',
				),
				'wrapper_search_end' => array(
					'type' => 'wrapper_end',
				),
				'header_contacts_show' => array(
					'type' => 'switch',
					'text' => us_translate( 'Contact Info' ),
					'std' => 0,
					'show_if' => array( 'header_layout', 'not in', array( 'simple_1', 'centered_1' ) ),
					'classes' => 'width_full',
				),
				'wrapper_contacts_start' => array(
					'type' => 'wrapper_start',
					'show_if' => array(
						array( 'header_layout', 'not in', array( 'simple_1', 'centered_1' ) ),
						'and',
						array( 'header_contacts_show', '=', TRUE ),
					),
				),
				'header_contacts_phone' => array(
					'title' => __( 'Phone Number', 'us' ),
					'type' => 'text',
					'classes' => 'cols_2 width_full',
				),
				'header_contacts_email' => array(
					'title' => us_translate( 'Email' ),
					'type' => 'text',
					'classes' => 'cols_2 width_full',
				),
				'header_contacts_custom_icon' => array(
					'title' => __( 'Icon', 'us' ),
					'description' => sprintf( __( '%s or %s icon name', 'us' ), '<a href="http://fontawesome.io/icons/" target="_blank">Font Awesome</a>', '<a href="https://material.io/icons/" target="_blank">Material</a>' ),
					'type' => 'text',
					'std' => '',
					'classes' => 'cols_2 width_full desc_1',
				),
				'header_contacts_custom_text' => array(
					'title' => us_translate( 'Text' ),
					'description' => __( 'Enter your content, HTML tags are allowed.', 'us' ),
					'type' => 'text',
					'classes' => 'cols_2 width_full desc_1',
				),
				'wrapper_contacts_end' => array(
					'type' => 'wrapper_end',
				),
				'header_socials_show' => array(
					'type' => 'switch',
					'text' => __( 'Social Links', 'us' ),
					'std' => 0,
					'show_if' => array( 'header_layout', 'not in', array( 'simple_1', 'centered_1' ) ),
					'classes' => 'width_full',
				),
				'wrapper_socials_start' => array(
					'type' => 'wrapper_start',
					'show_if' => array(
						array( 'header_layout', 'not in', array( 'simple_1', 'centered_1' ) ),
						'and',
						array( 'header_socials_show', '=', TRUE ),
					),
				),
			),
			$social_links_config,
			array(
				'header_socials_custom_url' => array(
					'title' => __( 'Custom Link', 'us' ),
					'type' => 'text',
					'classes' => 'cols_3 width_full',
				),
				'header_socials_custom_icon' => array(
					'title' => __( 'Custom Link Icon', 'us' ),
					'description' => sprintf( __( '%s or %s icon name', 'us' ), '<a href="http://fontawesome.io/icons/" target="_blank">Font Awesome</a>', '<a href="https://material.io/icons/" target="_blank">Material</a>' ),
					'type' => 'text',
					'classes' => 'cols_3 width_full desc_1',
				),
				'header_socials_custom_color' => array(
					'type' => 'color',
					'title' => __( 'Custom Link Color', 'us' ),
					'std' => '#1abc9c',
					'classes' => 'cols_3 width_full',
				),
				'wrapper_socials_end' => array(
					'type' => 'wrapper_end',
				),
				'header_language_show' => array(
					'type' => 'switch',
					'text' => __( 'Dropdown', 'us' ),
					'std' => 0,
					'show_if' => array( 'header_layout', 'not in', array( 'simple_1', 'centered_1' ) ),
					'classes' => 'width_full',
				),
				'wrapper_lang_start' => array(
					'type' => 'wrapper_start',
					'show_if' => array(
						array( 'header_layout', 'not in', array( 'simple_1', 'centered_1' ) ),
						'and',
						array( 'header_language_show', '=', TRUE ),
					),
				),
				'header_language_source' => array(
					'title' => us_translate( 'Source' ),
					'type' => 'select',
					'options' => array(
						'own' => __( 'My own links', 'us' ),
						'wpml' => 'WPML',
					),
					'std' => 'own',
				),
				'header_link_title' => array(
					'title' => __( 'Links Title', 'us' ),
					'description' => __( 'This text will be shown as the first active item of the dropdown menu.', 'us' ),
					'type' => 'text',
					'show_if' => array( 'header_language_source', '=', 'own' ),
				),
				'header_link_qty' => array(
					'title' => __( 'Links Quantity', 'us' ),
					'type' => 'radio',
					'options' => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
						'9' => '9',
					),
					'std' => '2',
					'show_if' => array( 'header_language_source', '=', 'own' ),
				),
				'header_link_1_label' => array(
					'placeholder' => __( 'Link Label', 'us' ),
					'type' => 'text',
					'std' => '',
					'classes' => 'cols_2 width_full',
					'show_if' => array( 'header_language_source', '=', 'own' ),
				),
				'header_link_1_url' => array(
					'placeholder' => us_translate( 'Enter the URL' ),
					'type' => 'text',
					'std' => '',
					'classes' => 'cols_2 width_full',
					'show_if' => array( 'header_language_source', '=', 'own' ),
				),
				'header_link_2_label' => array(
					'placeholder' => __( 'Link Label', 'us' ),
					'type' => 'text',
					'std' => '',
					'classes' => 'cols_2 width_full',
					'show_if' => array(
						array( 'header_language_source', '=', 'own' ),
						'and',
						array( 'header_link_qty', '>', 1 ),
					),
				),
				'header_link_2_url' => array(
					'placeholder' => us_translate( 'Enter the URL' ),
					'type' => 'text',
					'std' => '',
					'classes' => 'cols_2 width_full',
					'show_if' => array(
						array( 'header_language_source', '=', 'own' ),
						'and',
						array( 'header_link_qty', '>', 1 ),
					),
				),
				'header_link_3_label' => array(
					'placeholder' => __( 'Link Label', 'us' ),
					'type' => 'text',
					'std' => '',
					'classes' => 'cols_2 width_full',
					'show_if' => array(
						array( 'header_language_source', '=', 'own' ),
						'and',
						array( 'header_link_qty', '>', 2 ),
					),
				),
				'header_link_3_url' => array(
					'placeholder' => us_translate( 'Enter the URL' ),
					'type' => 'text',
					'std' => '',
					'classes' => 'cols_2 width_full',
					'show_if' => array(
						array( 'header_language_source', '=', 'own' ),
						'and',
						array( 'header_link_qty', '>', 2 ),
					),
				),
				'header_link_4_label' => array(
					'placeholder' => __( 'Link Label', 'us' ),
					'type' => 'text',
					'std' => '',
					'classes' => 'cols_2 width_full',
					'show_if' => array(
						array( 'header_language_source', '=', 'own' ),
						'and',
						array( 'header_link_qty', '>', 3 ),
					),
				),
				'header_link_4_url' => array(
					'placeholder' => us_translate( 'Enter the URL' ),
					'type' => 'text',
					'std' => '',
					'classes' => 'cols_2 width_full',
					'show_if' => array(
						array( 'header_language_source', '=', 'own' ),
						'and',
						array( 'header_link_qty', '>', 3 ),
					),
				),
				'header_link_5_label' => array(
					'placeholder' => __( 'Link Label', 'us' ),
					'type' => 'text',
					'std' => '',
					'classes' => 'cols_2 width_full',
					'show_if' => array(
						array( 'header_language_source', '=', 'own' ),
						'and',
						array( 'header_link_qty', '>', 4 ),
					),
				),
				'header_link_5_url' => array(
					'placeholder' => us_translate( 'Enter the URL' ),
					'type' => 'text',
					'std' => '',
					'classes' => 'cols_2 width_full',
					'show_if' => array(
						array( 'header_language_source', '=', 'own' ),
						'and',
						array( 'header_link_qty', '>', 4 ),
					),
				),
				'header_link_6_label' => array(
					'placeholder' => __( 'Link Label', 'us' ),
					'type' => 'text',
					'std' => '',
					'classes' => 'cols_2 width_full',
					'show_if' => array(
						array( 'header_language_source', '=', 'own' ),
						'and',
						array( 'header_link_qty', '>', 5 ),
					),
				),
				'header_link_6_url' => array(
					'placeholder' => us_translate( 'Enter the URL' ),
					'type' => 'text',
					'std' => '',
					'classes' => 'cols_2 width_full',
					'show_if' => array(
						array( 'header_language_source', '=', 'own' ),
						'and',
						array( 'header_link_qty', '>', 5 ),
					),
				),
				'header_link_7_label' => array(
					'placeholder' => __( 'Link Label', 'us' ),
					'type' => 'text',
					'std' => '',
					'classes' => 'cols_2 width_full',
					'show_if' => array(
						array( 'header_language_source', '=', 'own' ),
						'and',
						array( 'header_link_qty', '>', 6 ),
					),
				),
				'header_link_7_url' => array(
					'placeholder' => us_translate( 'Enter the URL' ),
					'type' => 'text',
					'std' => '',
					'classes' => 'cols_2 width_full',
					'show_if' => array(
						array( 'header_language_source', '=', 'own' ),
						'and',
						array( 'header_link_qty', '>', 6 ),
					),
				),
				'header_link_8_label' => array(
					'placeholder' => __( 'Link Label', 'us' ),
					'type' => 'text',
					'std' => '',
					'classes' => 'cols_2 width_full',
					'show_if' => array(
						array( 'header_language_source', '=', 'own' ),
						'and',
						array( 'header_link_qty', '>', 7 ),
					),
				),
				'header_link_8_url' => array(
					'placeholder' => us_translate( 'Enter the URL' ),
					'type' => 'text',
					'std' => '',
					'classes' => 'cols_2 width_full',
					'show_if' => array(
						array( 'header_language_source', '=', 'own' ),
						'and',
						array( 'header_link_qty', '>', 7 ),
					),
				),
				'header_link_9_label' => array(
					'placeholder' => __( 'Link Label', 'us' ),
					'type' => 'text',
					'std' => '',
					'classes' => 'cols_2 width_full',
					'show_if' => array(
						array( 'header_language_source', '=', 'own' ),
						'and',
						array( 'header_link_qty', '>', 8 ),
					),
				),
				'header_link_9_url' => array(
					'placeholder' => us_translate( 'Enter the URL' ),
					'std' => '',
					'type' => 'text',
					'classes' => 'cols_2 width_full',
					'show_if' => array(
						array( 'header_language_source', '=', 'own' ),
						'and',
						array( 'header_link_qty', '>', 8 ),
					),
				),
				'wrapper_lang_end' => array(
					'type' => 'wrapper_end',
				),
				'h_header_3' => array(
					'title' => __( 'Logo', 'us' ),
					'type' => 'heading',
					'classes' => 'with_separator',
				),
				'logo_type' => array(
					'type' => 'radio',
					'options' => array(
						'text' => us_translate( 'Text' ),
						'img' => us_translate( 'Image' ),
					),
					'std' => 'text',
					'classes' => 'width_full',
				),
				'logo_text' => array(
					'title' => us_translate( 'Text' ),
					'type' => 'text',
					'std' => 'LOGO',
					'show_if' => array( 'logo_type', '=', 'text' ),
				),
				'logo_font_size' => array(
					'title' => __( 'Font Size', 'us' ),
					'type' => 'slider',
					'min' => 12,
					'max' => 50,
					'std' => 26,
					'postfix' => 'px',
					'show_if' => array( 'logo_type', '=', 'text' ),
				),
				'logo_font_size_tablets' => array(
					'title' => __( 'Font Size on Tablets', 'us' ),
					'description' => __( 'This option is enabled when screen width is less than 900px', 'us' ),
					'type' => 'slider',
					'min' => 12,
					'max' => 50,
					'std' => 24,
					'postfix' => 'px',
					'show_if' => array( 'logo_type', '=', 'text' ),
				),
				'logo_font_size_mobiles' => array(
					'title' => __( 'Font Size on Mobiles', 'us' ),
					'description' => __( 'This option is enabled when screen width is less than 600px', 'us' ),
					'type' => 'slider',
					'min' => 12,
					'max' => 50,
					'std' => 20,
					'postfix' => 'px',
					'show_if' => array( 'logo_type', '=', 'text' ),
				),
				'logo_image' => array(
					'title' => us_translate( 'Image' ),
					'type' => 'upload',
					'extension' => 'png,jpg,jpeg,gif,svg',
					'show_if' => array( 'logo_type', '=', 'img' ),
				),
				'logo_height' => array(
					'title' => us_translate( 'Height' ),
					'type' => 'slider',
					'min' => 20,
					'max' => 300,
					'std' => 60,
					'postfix' => 'px',
					'show_if' => array( 'logo_type', '=', 'img' ),
				),
				'logo_height_sticky' => array(
					'title' => __( 'Height in the Sticky Header', 'us' ),
					'type' => 'slider',
					'min' => 20,
					'max' => 300,
					'std' => 60,
					'postfix' => 'px',
					'show_if' => array(
						array( 'logo_type', '=', 'img' ),
						'and',
						array( 'header_layout', '!=', 'vertical_1' ),
					),
				),
				'logo_height_tablets' => array(
					'title' => __( 'Height on Tablets', 'us' ),
					'description' => __( 'This option is enabled when screen width is less than 900px', 'us' ),
					'type' => 'slider',
					'min' => 20,
					'max' => 300,
					'std' => 40,
					'postfix' => 'px',
					'show_if' => array( 'logo_type', '=', 'img' ),
				),
				'logo_height_mobiles' => array(
					'title' => __( 'Height on Mobiles', 'us' ),
					'description' => __( 'This option is enabled when screen width is less than 600px', 'us' ),
					'type' => 'slider',
					'min' => 20,
					'max' => 300,
					'std' => 30,
					'postfix' => 'px',
					'show_if' => array( 'logo_type', '=', 'img' ),
				),
				'logo_image_transparent' => array(
					'title' => __( 'Different Image for Transparent Header', 'us' ),
					'type' => 'upload',
					'extension' => 'png,jpg,jpeg,gif,svg',
					'show_if' => array( 'logo_type', '=', 'img' ),
				),
				'logo_image_tablets' => array(
					'title' => __( 'On Tablets', 'us' ),
					'type' => 'upload',
					'extension' => 'png,jpg,jpeg,gif,svg',
					'show_if' => array( 'logo_type', '=', 'img' ),
				),
				'logo_image_mobiles' => array(
					'title' => __( 'On Mobiles', 'us' ),
					'type' => 'upload',
					'extension' => 'png,jpg,jpeg,gif,svg',
					'show_if' => array( 'logo_type', '=', 'img' ),
				),
				'h_header_4' => array(
					'title' => us_translate( 'Menu' ),
					'type' => 'heading',
					'classes' => 'with_separator',
				),
				'menu_source' => array(
					'title' => us_translate( 'Menu' ),
					'description' => sprintf( __( 'You can edit selected menu or create a new one on the %s page', 'us' ), '<a href="' . admin_url( 'nav-menus.php' ) . '" target="_blank">' . us_translate( 'Menus' ) . '</a>' ),
					'type' => 'select',
					'options' => us_get_nav_menus(),
					'std' => 'main',
				),
				'menu_fontsize' => array(
					'title' => __( 'Main Items Font Size', 'us' ),
					'type' => 'slider',
					'min' => 12,
					'max' => 50,
					'std' => 16,
					'postfix' => 'px',
				),
				'menu_indents' => array(
					'title' => __( 'Distance Between Main Items', 'us' ),
					'type' => 'slider',
					'min' => 10,
					'max' => 100,
					'step' => 2,
					'std' => 40,
					'postfix' => 'px',
				),
				'menu_height' => array(
					'title' => __( 'Main Items Height', 'us' ),
					'type' => 'switch',
					'text' => __( 'Stretch menu items to the full height of the header', 'us' ),
					'std' => 0,
				),
				'menu_sub_fontsize' => array(
					'title' => __( 'Dropdown Font Size', 'us' ),
					'type' => 'slider',
					'min' => 12,
					'max' => 50,
					'std' => 15,
					'postfix' => 'px',
				),
				'menu_mobile_width' => array(
					'title' => __( 'Enable mobile layout when screen width is less than', 'us' ),
					'type' => 'slider',
					'min' => 300,
					'max' => 2000,
					'std' => 900,
					'postfix' => 'px',
				),
				'menu_togglable_type' => array(
					'title' => __( 'Dropdown Behavior', 'us' ),
					'description' => __( 'When this option is OFF, mobile menu dropdown will be shown by click on an arrow only.', 'us' ),
					'type' => 'switch',
					'text' => __( 'Show dropdown by click on menu item title', 'us' ),
					'std' => 1,
				),
			)
		),
	),
	'titlebar' => array(
		'title' => __( 'Title Bars', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/titlebar',
		'fields' => array(

			// Titlebar Defaults
			'h_titlebar_1' => array(
				'title' => __( 'Defaults', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'titlebar' => array(
				'type' => 'switch',
				'text' => __( 'Show Title Bar', 'us' ),
				'std' => 1,
				'classes' => 'width_full',
			),
			'titlebar_size' => array(
				'title' => __( 'Title Bar Size', 'us' ),
				'type' => 'radio',
				'options' => array(
					'small' => __( 'Small', 'us' ),
					'medium' => __( 'Medium', 'us' ),
					'large' => __( 'Large', 'us' ),
					'huge' => __( 'Huge', 'us' ),
				),
				'std' => 'medium',
				'show_if' => array( 'titlebar', '=', '1' ),
			),
			'titlebar_color' => array(
				'title' => __( 'Title Bar Color Style', 'us' ),
				'type' => 'select',
				'options' => array(
					'default' => __( 'Content colors', 'us' ),
					'alternate' => __( 'Alternate Content colors', 'us' ),
					'primary' => __( 'Primary bg & White text', 'us' ),
					'secondary' => __( 'Secondary bg & White text', 'us' ),
					'footer-top' => __( 'Top Footer colors', 'us' ),
					'footer-bottom' => __( 'Bottom Footer colors', 'us' ),
				),
				'std' => 'alternate',
				'show_if' => array( 'titlebar', '=', '1' ),
			),
			'titlebar_breadcrumbs' => array(
				'title' => __( 'Breadcrumbs', 'us' ),
				'type' => 'switch',
				'text' => __( 'Show Breadcrumbs', 'us' ),
				'std' => 1,
				'show_if' => array( 'titlebar', '=', '1' ),
			),
			'titlebar_bg_image' => array(
				'title' => __( 'Background Image', 'us' ),
				'type' => 'upload',
				'show_if' => array( 'titlebar', '=', '1' ),
			),
			'wrapper_titlebar_bg_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'force_right',
				'show_if' => array(
					array( 'titlebar', '=', '1' ),
					'and',
					array( 'titlebar_bg_image', '!=', '' ),
				),
			),
			'titlebar_bg_size' => array(
				'title' => __( 'Background Image Size', 'us' ),
				'type' => 'radio',
				'options' => array(
					'cover' => __( 'Fill Area', 'us' ),
					'contain' => __( 'Fit to Area', 'us' ),
					'initial' => __( 'Initial', 'us' ),
				),
				'std' => 'cover',
				'classes' => 'width_full',
			),
			'titlebar_bg_repeat' => array(
				'title' => __( 'Background Image Repeat', 'us' ),
				'type' => 'radio',
				'options' => array(
					'repeat' => __( 'Repeat', 'us' ),
					'repeat-x' => __( 'Horizontally', 'us' ),
					'repeat-y' => __( 'Vertically', 'us' ),
					'no-repeat' => us_translate( 'None' ),
				),
				'std' => 'repeat',
				'classes' => 'width_full',
			),
			'titlebar_bg_position' => array(
				'title' => __( 'Background Image Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'top left' => '<span class="dashicons dashicons-arrow-left-alt"></span>',
					'top center' => '<span class="dashicons dashicons-arrow-up-alt"></span>',
					'top right' => '<span class="dashicons dashicons-arrow-right-alt"></span>',
					'center left' => '<span class="dashicons dashicons-arrow-left-alt"></span>',
					'center center' => '<span class="dashicons dashicons-marker"></span>',
					'center right' => '<span class="dashicons dashicons-arrow-right-alt"></span>',
					'bottom left' => '<span class="dashicons dashicons-arrow-left-alt"></span>',
					'bottom center' => '<span class="dashicons dashicons-arrow-down-alt"></span>',
					'bottom right' => '<span class="dashicons dashicons-arrow-right-alt"></span>',
				),
				'std' => 'center center',
				'classes' => 'bgpos width_full',
			),
			'titlebar_bg_parallax' => array(
				'title' => __( 'Parallax Effect', 'us' ),
				'type' => 'select',
				'options' => array(
					'none' => us_translate( 'None' ),
					'vertical' => __( 'Vertical Parallax', 'us' ),
					'vertical_reversed' => __( 'Vertical Reversed Parallax', 'us' ),
					'horizontal' => __( 'Horizontal Parallax', 'us' ),
					'still' => __( 'Fixed', 'us' ),
				),
				'std' => 'none',
				'classes' => 'width_full',
			),
			'wrapper_titlebar_bg_end' => array(
				'type' => 'wrapper_end',
			),
			'titlebar_overlay_color' => array(
				'title' => __( 'Overlay Color', 'us' ),
				'type' => 'color',
				'show_if' => array( 'titlebar', '=', '1' ),
			),

			// Titlebar for Portfolio Pages
			'h_titlebar_2' => array(
				'title' => __( 'Portfolio Pages', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
				'place_if' => ( $usof_enable_portfolio == 1 ),
			),
			'titlebar_portfolio' => array(
				'type' => 'switch',
				'text' => __( 'Show Title Bar', 'us' ),
				'std' => 0,
				'classes' => 'width_full',
				'place_if' => ( $usof_enable_portfolio == 1 ),
			),
			'titlebar_portfolio_defaults' => array(
				'type' => 'switch',
				'text' => __( 'Use Defaults', 'us' ),
				'std' => 1,
				'classes' => 'width_full',
				'show_if' => array( 'titlebar_portfolio', '=', '1' ),
				'place_if' => ( $usof_enable_portfolio == 1 ),
			),
			'titlebar_portfolio_size' => array(
				'title' => __( 'Title Bar Size', 'us' ),
				'type' => 'radio',
				'options' => array(
					'small' => __( 'Small', 'us' ),
					'medium' => __( 'Medium', 'us' ),
					'large' => __( 'Large', 'us' ),
					'huge' => __( 'Huge', 'us' ),
				),
				'std' => 'medium',
				'show_if' => array(
					array( 'titlebar_portfolio', '=', '1' ),
					'and',
					array( 'titlebar_portfolio_defaults', '=', 0 ),
				),
				'place_if' => ( $usof_enable_portfolio == 1 ),
			),
			'titlebar_portfolio_color' => array(
				'title' => __( 'Title Bar Color Style', 'us' ),
				'type' => 'select',
				'options' => array(
					'default' => __( 'Content colors', 'us' ),
					'alternate' => __( 'Alternate Content colors', 'us' ),
					'primary' => __( 'Primary bg & White text', 'us' ),
					'secondary' => __( 'Secondary bg & White text', 'us' ),
					'footer-top' => __( 'Top Footer colors', 'us' ),
					'footer-bottom' => __( 'Bottom Footer colors', 'us' ),
				),
				'std' => 'alternate',
				'show_if' => array(
					array( 'titlebar_portfolio', '=', '1' ),
					'and',
					array( 'titlebar_portfolio_defaults', '=', 0 ),
				),
				'place_if' => ( $usof_enable_portfolio == 1 ),
			),
			'titlebar_portfolio_breadcrumbs' => array(
				'title' => __( 'Breadcrumbs', 'us' ),
				'type' => 'switch',
				'text' => __( 'Show Breadcrumbs', 'us' ),
				'std' => 0,
				'show_if' => array(
					array( 'titlebar_portfolio', '=', '1' ),
					'and',
					array( 'titlebar_portfolio_defaults', '=', 0 ),
				),
				'place_if' => ( $usof_enable_portfolio == 1 ),
			),
			'titlebar_portfolio_bg_image' => array(
				'title' => __( 'Background Image', 'us' ),
				'type' => 'upload',
				'show_if' => array(
					array( 'titlebar_portfolio', '=', '1' ),
					'and',
					array( 'titlebar_portfolio_defaults', '=', 0 ),
				),
				'place_if' => ( $usof_enable_portfolio == 1 ),
			),
			'wrapper_titlebar_portfolio_bg_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'force_right',
				'show_if' => array(
					array( 'titlebar_portfolio', '=', '1' ),
					'and',
					array( 'titlebar_portfolio_defaults', '=', 0 ),
					'and',
					array( 'titlebar_portfolio_bg_image', '!=', '' ),
				),
				'place_if' => ( $usof_enable_portfolio == 1 ),
			),
			'titlebar_portfolio_bg_size' => array(
				'title' => __( 'Background Image Size', 'us' ),
				'type' => 'radio',
				'options' => array(
					'cover' => __( 'Fill Area', 'us' ),
					'contain' => __( 'Fit to Area', 'us' ),
					'initial' => __( 'Initial', 'us' ),
				),
				'std' => 'cover',
				'classes' => 'width_full',
				'place_if' => ( $usof_enable_portfolio == 1 ),
			),
			'titlebar_portfolio_bg_repeat' => array(
				'title' => __( 'Background Image Repeat', 'us' ),
				'type' => 'radio',
				'options' => array(
					'repeat' => __( 'Repeat', 'us' ),
					'repeat-x' => __( 'Horizontally', 'us' ),
					'repeat-y' => __( 'Vertically', 'us' ),
					'no-repeat' => us_translate( 'None' ),
				),
				'std' => 'repeat',
				'classes' => 'width_full',
				'place_if' => ( $usof_enable_portfolio == 1 ),
			),
			'titlebar_portfolio_bg_position' => array(
				'title' => __( 'Background Image Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'top left' => '<span class="dashicons dashicons-arrow-left-alt"></span>',
					'top center' => '<span class="dashicons dashicons-arrow-up-alt"></span>',
					'top right' => '<span class="dashicons dashicons-arrow-right-alt"></span>',
					'center left' => '<span class="dashicons dashicons-arrow-left-alt"></span>',
					'center center' => '<span class="dashicons dashicons-marker"></span>',
					'center right' => '<span class="dashicons dashicons-arrow-right-alt"></span>',
					'bottom left' => '<span class="dashicons dashicons-arrow-left-alt"></span>',
					'bottom center' => '<span class="dashicons dashicons-arrow-down-alt"></span>',
					'bottom right' => '<span class="dashicons dashicons-arrow-right-alt"></span>',
				),
				'std' => 'center center',
				'classes' => 'bgpos width_full',
				'place_if' => ( $usof_enable_portfolio == 1 ),
			),
			'titlebar_portfolio_bg_parallax' => array(
				'title' => __( 'Parallax Effect', 'us' ),
				'type' => 'select',
				'options' => array(
					'none' => us_translate( 'None' ),
					'vertical' => __( 'Vertical Parallax', 'us' ),
					'vertical_reversed' => __( 'Vertical Reversed Parallax', 'us' ),
					'horizontal' => __( 'Horizontal Parallax', 'us' ),
					'still' => __( 'Fixed', 'us' ),
				),
				'std' => 'none',
				'classes' => 'width_full',
				'place_if' => ( $usof_enable_portfolio == 1 ),
			),
			'wrapper_titlebar_portfolio_bg_end' => array(
				'type' => 'wrapper_end',
				'place_if' => ( $usof_enable_portfolio == 1 ),
			),
			'titlebar_portfolio_overlay_color' => array(
				'title' => __( 'Overlay Color', 'us' ),
				'type' => 'color',
				'show_if' => array(
					array( 'titlebar_portfolio', '=', '1' ),
					'and',
					array( 'titlebar_portfolio_defaults', '=', 0 ),
				),
				'place_if' => ( $usof_enable_portfolio == 1 ),
			),

			// Titlebar for Posts
			'h_titlebar_3' => array(
				'title' => us_translate_x( 'Posts', 'post type general name' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'titlebar_post' => array(
				'type' => 'switch',
				'text' => __( 'Show Title Bar', 'us' ),
				'std' => 0,
				'classes' => 'width_full',
			),
			'titlebar_post_defaults' => array(
				'type' => 'switch',
				'text' => __( 'Use Defaults', 'us' ),
				'std' => 1,
				'classes' => 'width_full',
				'show_if' => array( 'titlebar_post', '=', '1' ),
			),
			'titlebar_post_title' => array(
				'title' => __( 'Title Bar Title', 'us' ),
				'type' => 'text',
				'std' => 'Blog',
				'show_if' => array( 'titlebar_post', '=', '1' ),
			),
			'titlebar_post_size' => array(
				'title' => __( 'Title Bar Size', 'us' ),
				'type' => 'radio',
				'options' => array(
					'small' => __( 'Small', 'us' ),
					'medium' => __( 'Medium', 'us' ),
					'large' => __( 'Large', 'us' ),
					'huge' => __( 'Huge', 'us' ),
				),
				'std' => 'medium',
				'show_if' => array(
					array( 'titlebar_post', '=', '1' ),
					'and',
					array( 'titlebar_post_defaults', '=', 0 ),
				),
			),
			'titlebar_post_color' => array(
				'title' => __( 'Title Bar Color Style', 'us' ),
				'type' => 'select',
				'options' => array(
					'default' => __( 'Content colors', 'us' ),
					'alternate' => __( 'Alternate Content colors', 'us' ),
					'primary' => __( 'Primary bg & White text', 'us' ),
					'secondary' => __( 'Secondary bg & White text', 'us' ),
					'footer-top' => __( 'Top Footer colors', 'us' ),
					'footer-bottom' => __( 'Bottom Footer colors', 'us' ),
				),
				'std' => 'alternate',
				'show_if' => array(
					array( 'titlebar_post', '=', '1' ),
					'and',
					array( 'titlebar_post_defaults', '=', 0 ),
				),
			),
			'titlebar_post_breadcrumbs' => array(
				'title' => __( 'Breadcrumbs', 'us' ),
				'type' => 'switch',
				'text' => __( 'Show Breadcrumbs', 'us' ),
				'std' => 0,
				'show_if' => array(
					array( 'titlebar_post', '=', '1' ),
					'and',
					array( 'titlebar_post_defaults', '=', 0 ),
				),
			),
			'titlebar_post_bg_image' => array(
				'title' => __( 'Background Image', 'us' ),
				'type' => 'upload',
				'show_if' => array(
					array( 'titlebar_post', '=', '1' ),
					'and',
					array( 'titlebar_post_defaults', '=', 0 ),
				),
			),
			'wrapper_titlebar_post_bg_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'force_right',
				'show_if' => array(
					array( 'titlebar_post', '=', '1' ),
					'and',
					array( 'titlebar_post_defaults', '=', 0 ),
					'and',
					array( 'titlebar_post_bg_image', '!=', '' ),
				),
			),
			'titlebar_post_bg_size' => array(
				'title' => __( 'Background Image Size', 'us' ),
				'type' => 'radio',
				'options' => array(
					'cover' => __( 'Fill Area', 'us' ),
					'contain' => __( 'Fit to Area', 'us' ),
					'initial' => __( 'Initial', 'us' ),
				),
				'std' => 'cover',
				'classes' => 'width_full',
			),
			'titlebar_post_bg_repeat' => array(
				'title' => __( 'Background Image Repeat', 'us' ),
				'type' => 'radio',
				'options' => array(
					'repeat' => __( 'Repeat', 'us' ),
					'repeat-x' => __( 'Horizontally', 'us' ),
					'repeat-y' => __( 'Vertically', 'us' ),
					'no-repeat' => us_translate( 'None' ),
				),
				'std' => 'repeat',
				'classes' => 'width_full',
			),
			'titlebar_post_bg_position' => array(
				'title' => __( 'Background Image Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'top left' => '<span class="dashicons dashicons-arrow-left-alt"></span>',
					'top center' => '<span class="dashicons dashicons-arrow-up-alt"></span>',
					'top right' => '<span class="dashicons dashicons-arrow-right-alt"></span>',
					'center left' => '<span class="dashicons dashicons-arrow-left-alt"></span>',
					'center center' => '<span class="dashicons dashicons-marker"></span>',
					'center right' => '<span class="dashicons dashicons-arrow-right-alt"></span>',
					'bottom left' => '<span class="dashicons dashicons-arrow-left-alt"></span>',
					'bottom center' => '<span class="dashicons dashicons-arrow-down-alt"></span>',
					'bottom right' => '<span class="dashicons dashicons-arrow-right-alt"></span>',
				),
				'std' => 'center center',
				'classes' => 'bgpos width_full',
			),
			'titlebar_post_bg_parallax' => array(
				'title' => __( 'Parallax Effect', 'us' ),
				'type' => 'select',
				'options' => array(
					'none' => us_translate( 'None' ),
					'vertical' => __( 'Vertical Parallax', 'us' ),
					'vertical_reversed' => __( 'Vertical Reversed Parallax', 'us' ),
					'horizontal' => __( 'Horizontal Parallax', 'us' ),
					'still' => __( 'Fixed', 'us' ),
				),
				'std' => 'none',
				'classes' => 'width_full',
			),
			'wrapper_titlebar_post_bg_end' => array(
				'type' => 'wrapper_end',
			),
			'titlebar_post_overlay_color' => array(
				'title' => __( 'Overlay Color', 'us' ),
				'type' => 'color',
				'show_if' => array(
					array( 'titlebar_post', '=', '1' ),
					'and',
					array( 'titlebar_post_defaults', '=', 0 ),
				),
			),

			// Titlebar for Archive, Search Results Pages
			'h_titlebar_4' => array(
				'title' => __( 'Archive, Search Results Pages', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'titlebar_archive' => array(
				'type' => 'switch',
				'text' => __( 'Show Title Bar', 'us' ),
				'std' => 1,
				'classes' => 'width_full',
			),
			'titlebar_archive_defaults' => array(
				'type' => 'switch',
				'text' => __( 'Use Defaults', 'us' ),
				'std' => 1,
				'classes' => 'width_full',
				'show_if' => array( 'titlebar_archive', '=', '1' ),
			),
			'titlebar_archive_size' => array(
				'title' => __( 'Title Bar Size', 'us' ),
				'type' => 'radio',
				'options' => array(
					'small' => __( 'Small', 'us' ),
					'medium' => __( 'Medium', 'us' ),
					'large' => __( 'Large', 'us' ),
					'huge' => __( 'Huge', 'us' ),
				),
				'std' => 'medium',
				'show_if' => array(
					array( 'titlebar_archive', '=', '1' ),
					'and',
					array( 'titlebar_archive_defaults', '=', 0 ),
				),
			),
			'titlebar_archive_color' => array(
				'title' => __( 'Title Bar Color Style', 'us' ),
				'type' => 'select',
				'options' => array(
					'default' => __( 'Content colors', 'us' ),
					'alternate' => __( 'Alternate Content colors', 'us' ),
					'primary' => __( 'Primary bg & White text', 'us' ),
					'secondary' => __( 'Secondary bg & White text', 'us' ),
					'footer-top' => __( 'Top Footer colors', 'us' ),
					'footer-bottom' => __( 'Bottom Footer colors', 'us' ),
				),
				'std' => 'alternate',
				'show_if' => array(
					array( 'titlebar_archive', '=', '1' ),
					'and',
					array( 'titlebar_archive_defaults', '=', 0 ),
				),
			),
			'titlebar_archive_breadcrumbs' => array(
				'title' => __( 'Breadcrumbs', 'us' ),
				'type' => 'switch',
				'text' => __( 'Show Breadcrumbs', 'us' ),
				'std' => 0,
				'show_if' => array(
					array( 'titlebar_archive', '=', '1' ),
					'and',
					array( 'titlebar_archive_defaults', '=', 0 ),
				),
			),
			'titlebar_archive_bg_image' => array(
				'title' => __( 'Background Image', 'us' ),
				'type' => 'upload',
				'show_if' => array(
					array( 'titlebar_archive', '=', '1' ),
					'and',
					array( 'titlebar_archive_defaults', '=', 0 ),
				),
			),
			'wrapper_titlebar_archive_bg_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'force_right',
				'show_if' => array(
					array( 'titlebar_archive', '=', '1' ),
					'and',
					array( 'titlebar_archive_defaults', '=', 0 ),
					'and',
					array( 'titlebar_archive_bg_image', '!=', '' ),
				),
			),
			'titlebar_archive_bg_size' => array(
				'title' => __( 'Background Image Size', 'us' ),
				'type' => 'radio',
				'options' => array(
					'cover' => __( 'Fill Area', 'us' ),
					'contain' => __( 'Fit to Area', 'us' ),
					'initial' => __( 'Initial', 'us' ),
				),
				'std' => 'cover',
				'classes' => 'width_full',
			),
			'titlebar_archive_bg_repeat' => array(
				'title' => __( 'Background Image Repeat', 'us' ),
				'type' => 'radio',
				'options' => array(
					'repeat' => __( 'Repeat', 'us' ),
					'repeat-x' => __( 'Horizontally', 'us' ),
					'repeat-y' => __( 'Vertically', 'us' ),
					'no-repeat' => us_translate( 'None' ),
				),
				'std' => 'repeat',
				'classes' => 'width_full',
			),
			'titlebar_archive_bg_position' => array(
				'title' => __( 'Background Image Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'top left' => '<span class="dashicons dashicons-arrow-left-alt"></span>',
					'top center' => '<span class="dashicons dashicons-arrow-up-alt"></span>',
					'top right' => '<span class="dashicons dashicons-arrow-right-alt"></span>',
					'center left' => '<span class="dashicons dashicons-arrow-left-alt"></span>',
					'center center' => '<span class="dashicons dashicons-marker"></span>',
					'center right' => '<span class="dashicons dashicons-arrow-right-alt"></span>',
					'bottom left' => '<span class="dashicons dashicons-arrow-left-alt"></span>',
					'bottom center' => '<span class="dashicons dashicons-arrow-down-alt"></span>',
					'bottom right' => '<span class="dashicons dashicons-arrow-right-alt"></span>',
				),
				'std' => 'center center',
				'classes' => 'bgpos width_full',
			),
			'titlebar_archive_bg_parallax' => array(
				'title' => __( 'Parallax Effect', 'us' ),
				'type' => 'select',
				'options' => array(
					'none' => us_translate( 'None' ),
					'vertical' => __( 'Vertical Parallax', 'us' ),
					'vertical_reversed' => __( 'Vertical Reversed Parallax', 'us' ),
					'horizontal' => __( 'Horizontal Parallax', 'us' ),
					'still' => __( 'Fixed', 'us' ),
				),
				'std' => 'none',
				'classes' => 'width_full',
			),
			'wrapper_titlebar_archive_bg_end' => array(
				'type' => 'wrapper_end',
			),
			'titlebar_archive_overlay_color' => array(
				'title' => __( 'Overlay Color', 'us' ),
				'type' => 'color',
				'show_if' => array(
					array( 'titlebar_archive', '=', '1' ),
					'and',
					array( 'titlebar_archive_defaults', '=', 0 ),
				),
			),

			// Titlebar for Shop and Product Pages
			'h_titlebar_5' => array(
				'title' => __( 'Shop and Product Pages', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
				'place_if' => class_exists( 'woocommerce' ),
			),
			'titlebar_shop' => array(
				'type' => 'switch',
				'text' => __( 'Show Title Bar', 'us' ),
				'std' => 0,
				'classes' => 'width_full',
				'place_if' => class_exists( 'woocommerce' ),
			),
			'titlebar_shop_defaults' => array(
				'type' => 'switch',
				'text' => __( 'Use Defaults', 'us' ),
				'std' => 1,
				'classes' => 'width_full',
				'show_if' => array( 'titlebar_shop', '=', '1' ),
				'place_if' => class_exists( 'woocommerce' ),
			),
			'titlebar_shop_size' => array(
				'title' => __( 'Title Bar Size', 'us' ),
				'type' => 'radio',
				'options' => array(
					'small' => __( 'Small', 'us' ),
					'medium' => __( 'Medium', 'us' ),
					'large' => __( 'Large', 'us' ),
					'huge' => __( 'Huge', 'us' ),
				),
				'std' => 'medium',
				'show_if' => array(
					array( 'titlebar_shop', '=', '1' ),
					'and',
					array( 'titlebar_shop_defaults', '=', 0 ),
				),
				'place_if' => class_exists( 'woocommerce' ),
			),
			'titlebar_shop_color' => array(
				'title' => __( 'Title Bar Color Style', 'us' ),
				'type' => 'select',
				'options' => array(
					'default' => __( 'Content colors', 'us' ),
					'alternate' => __( 'Alternate Content colors', 'us' ),
					'primary' => __( 'Primary bg & White text', 'us' ),
					'secondary' => __( 'Secondary bg & White text', 'us' ),
					'footer-top' => __( 'Top Footer colors', 'us' ),
					'footer-bottom' => __( 'Bottom Footer colors', 'us' ),
				),
				'std' => 'alternate',
				'show_if' => array(
					array( 'titlebar_shop', '=', '1' ),
					'and',
					array( 'titlebar_shop_defaults', '=', 0 ),
				),
				'place_if' => class_exists( 'woocommerce' ),
			),
			'titlebar_shop_breadcrumbs' => array(
				'title' => __( 'Breadcrumbs', 'us' ),
				'type' => 'switch',
				'text' => __( 'Show Breadcrumbs', 'us' ),
				'std' => 0,
				'show_if' => array(
					array( 'titlebar_shop', '=', '1' ),
					'and',
					array( 'titlebar_shop_defaults', '=', 0 ),
				),
				'place_if' => class_exists( 'woocommerce' ),
			),
			'titlebar_shop_bg_image' => array(
				'title' => __( 'Background Image', 'us' ),
				'type' => 'upload',
				'show_if' => array(
					array( 'titlebar_shop', '=', '1' ),
					'and',
					array( 'titlebar_shop_defaults', '=', 0 ),
				),
				'place_if' => class_exists( 'woocommerce' ),
			),
			'wrapper_titlebar_shop_bg_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'force_right',
				'show_if' => array(
					array( 'titlebar_shop', '=', '1' ),
					'and',
					array( 'titlebar_shop_defaults', '=', 0 ),
					'and',
					array( 'titlebar_shop_bg_image', '!=', '' ),
				),
				'place_if' => class_exists( 'woocommerce' ),
			),
			'titlebar_shop_bg_size' => array(
				'title' => __( 'Background Image Size', 'us' ),
				'type' => 'radio',
				'options' => array(
					'cover' => __( 'Fill Area', 'us' ),
					'contain' => __( 'Fit to Area', 'us' ),
					'initial' => __( 'Initial', 'us' ),
				),
				'std' => 'cover',
				'classes' => 'width_full',
				'place_if' => class_exists( 'woocommerce' ),
			),
			'titlebar_shop_bg_repeat' => array(
				'title' => __( 'Background Image Repeat', 'us' ),
				'type' => 'radio',
				'options' => array(
					'repeat' => __( 'Repeat', 'us' ),
					'repeat-x' => __( 'Horizontally', 'us' ),
					'repeat-y' => __( 'Vertically', 'us' ),
					'no-repeat' => us_translate( 'None' ),
				),
				'std' => 'repeat',
				'classes' => 'width_full',
				'place_if' => class_exists( 'woocommerce' ),
			),
			'titlebar_shop_bg_position' => array(
				'title' => __( 'Background Image Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'top left' => '<span class="dashicons dashicons-arrow-left-alt"></span>',
					'top center' => '<span class="dashicons dashicons-arrow-up-alt"></span>',
					'top right' => '<span class="dashicons dashicons-arrow-right-alt"></span>',
					'center left' => '<span class="dashicons dashicons-arrow-left-alt"></span>',
					'center center' => '<span class="dashicons dashicons-marker"></span>',
					'center right' => '<span class="dashicons dashicons-arrow-right-alt"></span>',
					'bottom left' => '<span class="dashicons dashicons-arrow-left-alt"></span>',
					'bottom center' => '<span class="dashicons dashicons-arrow-down-alt"></span>',
					'bottom right' => '<span class="dashicons dashicons-arrow-right-alt"></span>',
				),
				'std' => 'center center',
				'classes' => 'bgpos width_full',
				'place_if' => class_exists( 'woocommerce' ),
			),
			'titlebar_shop_bg_parallax' => array(
				'title' => __( 'Parallax Effect', 'us' ),
				'type' => 'select',
				'options' => array(
					'none' => us_translate( 'None' ),
					'vertical' => __( 'Vertical Parallax', 'us' ),
					'vertical_reversed' => __( 'Vertical Reversed Parallax', 'us' ),
					'horizontal' => __( 'Horizontal Parallax', 'us' ),
					'still' => __( 'Fixed', 'us' ),
				),
				'std' => 'none',
				'classes' => 'width_full',
				'place_if' => class_exists( 'woocommerce' ),
			),
			'wrapper_titlebar_shop_bg_end' => array(
				'type' => 'wrapper_end',
				'place_if' => class_exists( 'woocommerce' ),
			),
			'titlebar_shop_overlay_color' => array(
				'title' => __( 'Overlay Color', 'us' ),
				'type' => 'color',
				'show_if' => array(
					array( 'titlebar_shop', '=', '1' ),
					'and',
					array( 'titlebar_shop_defaults', '=', 0 ),
				),
				'place_if' => class_exists( 'woocommerce' ),
			),

		),
	),
	'sidebar' => array(
		'title' => __( 'Sidebars', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/sidebar',
		'fields' => array_merge(
			array(

				// Sidebar Defaults
				'h_sidebar_1' => array(
					'title' => __( 'Defaults', 'us' ),
					'type' => 'heading',
					'classes' => 'with_separator',
				),
				'sidebar' => array(
					'type' => 'switch',
					'text' => __( 'Show Sidebar', 'us' ),
					'std' => 0,
					'classes' => 'width_full',
				),
				'sidebar_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'description' => sprintf( __( 'You can edit selected sidebar or create a new one on the %s page', 'us' ), '<a href="' . admin_url( 'widgets.php' ) . '" target="_blank">' . us_translate( 'Widgets' ) . '</a>' ),
					'type' => 'select',
					'options' => $sidebars_options,
					'std' => 'default_sidebar',
					'show_if' => array( 'sidebar', '=', 1 ),
				),
				'sidebar_pos' => array(
					'title' => __( 'Sidebar Position', 'us' ),
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'show_if' => array( 'sidebar', '=', 1 ),
				),

				// Sidebar for Portfolio Pages
				'h_sidebar_2' => array(
					'title' => __( 'Portfolio Pages', 'us' ),
					'type' => 'heading',
					'classes' => 'with_separator',
					'place_if' => ( $usof_enable_portfolio == 1 ),
				),
				'portfolio_sidebar' => array(
					'type' => 'switch',
					'text' => __( 'Show Sidebar', 'us' ),
					'std' => 0,
					'classes' => 'width_full',
					'place_if' => ( $usof_enable_portfolio == 1 ),
				),
				'portfolio_sidebar_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'description' => sprintf( __( 'You can edit selected sidebar or create a new one on the %s page', 'us' ), '<a href="' . admin_url( 'widgets.php' ) . '" target="_blank">' . us_translate( 'Widgets' ) . '</a>' ),
					'type' => 'select',
					'options' => $sidebars_options,
					'std' => 'default_sidebar',
					'show_if' => array( 'portfolio_sidebar', '=', 1 ),
					'place_if' => ( $usof_enable_portfolio == 1 ),
				),
				'portfolio_sidebar_pos' => array(
					'title' => __( 'Sidebar Position', 'us' ),
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'show_if' => array( 'portfolio_sidebar', '=', 1 ),
					'place_if' => ( $usof_enable_portfolio == 1 ),
				),

				// Sidebar for Posts
				'h_sidebar_3' => array(
					'title' => us_translate_x( 'Posts', 'post type general name' ),
					'type' => 'heading',
					'classes' => 'with_separator',
				),
				'post_sidebar' => array(
					'type' => 'switch',
					'text' => __( 'Show Sidebar', 'us' ),
					'std' => 0,
					'classes' => 'width_full',
				),
				'post_sidebar_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'description' => sprintf( __( 'You can edit selected sidebar or create a new one on the %s page', 'us' ), '<a href="' . admin_url( 'widgets.php' ) . '" target="_blank">' . us_translate( 'Widgets' ) . '</a>' ),
					'type' => 'select',
					'options' => $sidebars_options,
					'std' => 'default_sidebar',
					'show_if' => array( 'post_sidebar', '=', 1 ),
				),
				'post_sidebar_pos' => array(
					'title' => __( 'Sidebar Position', 'us' ),
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'show_if' => array( 'post_sidebar', '=', 1 ),
				),

				// Sidebar for Blog Home page
				'h_sidebar_4' => array(
					'title' => __( 'Blog Home Page', 'us' ),
					'type' => 'heading',
					'classes' => 'with_separator',
				),
				'blog_sidebar' => array(
					'type' => 'switch',
					'text' => __( 'Show Sidebar', 'us' ),
					'std' => 0,
					'classes' => 'width_full',
				),
				'blog_sidebar_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'description' => sprintf( __( 'You can edit selected sidebar or create a new one on the %s page', 'us' ), '<a href="' . admin_url( 'widgets.php' ) . '" target="_blank">' . us_translate( 'Widgets' ) . '</a>' ),
					'type' => 'select',
					'options' => $sidebars_options,
					'std' => 'default_sidebar',
					'show_if' => array( 'blog_sidebar', '=', 1 ),
				),
				'blog_sidebar_pos' => array(
					'title' => __( 'Sidebar Position', 'us' ),
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'show_if' => array( 'blog_sidebar', '=', 1 ),
				),

				// Sidebar for Archive pages
				'h_sidebar_5' => array(
					'title' => __( 'Archive Pages', 'us' ),
					'type' => 'heading',
					'classes' => 'with_separator',
				),
				'archive_sidebar' => array(
					'type' => 'switch',
					'text' => __( 'Show Sidebar', 'us' ),
					'std' => 0,
					'classes' => 'width_full',
				),
				'archive_sidebar_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'description' => sprintf( __( 'You can edit selected sidebar or create a new one on the %s page', 'us' ), '<a href="' . admin_url( 'widgets.php' ) . '" target="_blank">' . us_translate( 'Widgets' ) . '</a>' ),
					'type' => 'select',
					'options' => $sidebars_options,
					'std' => 'default_sidebar',
					'show_if' => array( 'archive_sidebar', '=', 1 ),
				),
				'archive_sidebar_pos' => array(
					'title' => __( 'Sidebar Position', 'us' ),
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'show_if' => array( 'archive_sidebar', '=', 1 ),
				),

				// Sidebar for Search Results page
				'h_sidebar_6' => array(
					'title' => __( 'Search Results Page', 'us' ),
					'type' => 'heading',
					'classes' => 'with_separator',
				),
				'search_sidebar' => array(
					'type' => 'switch',
					'text' => __( 'Show Sidebar', 'us' ),
					'std' => 0,
					'classes' => 'width_full',
				),
				'search_sidebar_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'description' => sprintf( __( 'You can edit selected sidebar or create a new one on the %s page', 'us' ), '<a href="' . admin_url( 'widgets.php' ) . '" target="_blank">' . us_translate( 'Widgets' ) . '</a>' ),
					'type' => 'select',
					'options' => $sidebars_options,
					'std' => 'default_sidebar',
					'show_if' => array( 'search_sidebar', '=', 1 ),
				),
				'search_sidebar_pos' => array(
					'title' => __( 'Sidebar Position', 'us' ),
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'show_if' => array( 'search_sidebar', '=', 1 ),
				),

				// Sidebar for Shop pages
				'h_sidebar_7' => array(
					'title' => __( 'Shop Pages', 'us' ),
					'type' => 'heading',
					'classes' => 'with_separator',
					'place_if' => class_exists( 'woocommerce' ),
				),
				'shop_sidebar' => array(
					'type' => 'switch',
					'text' => __( 'Show Sidebar', 'us' ),
					'std' => 0,
					'classes' => 'width_full',
					'place_if' => class_exists( 'woocommerce' ),
				),
				'shop_sidebar_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'description' => sprintf( __( 'You can edit selected sidebar or create a new one on the %s page', 'us' ), '<a href="' . admin_url( 'widgets.php' ) . '" target="_blank">' . us_translate( 'Widgets' ) . '</a>' ),
					'type' => 'select',
					'options' => $sidebars_options,
					'std' => 'default_sidebar',
					'show_if' => array( 'shop_sidebar', '=', 1 ),
					'place_if' => class_exists( 'woocommerce' ),
				),
				'shop_sidebar_pos' => array(
					'title' => __( 'Sidebar Position', 'us' ),
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'show_if' => array( 'shop_sidebar', '=', 1 ),
					'place_if' => class_exists( 'woocommerce' ),
				),

				// Sidebar for Product pages
				'h_sidebar_8' => array(
					'title' => us_translate( 'Products', 'woocommerce' ),
					'type' => 'heading',
					'classes' => 'with_separator',
					'place_if' => class_exists( 'woocommerce' ),
				),
				'product_sidebar' => array(
					'type' => 'switch',
					'text' => __( 'Show Sidebar', 'us' ),
					'std' => 0,
					'classes' => 'width_full',
					'place_if' => class_exists( 'woocommerce' ),
				),
				'product_sidebar_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'description' => sprintf( __( 'You can edit selected sidebar or create a new one on the %s page', 'us' ), '<a href="' . admin_url( 'widgets.php' ) . '" target="_blank">' . us_translate( 'Widgets' ) . '</a>' ),
					'type' => 'select',
					'options' => $sidebars_options,
					'std' => 'default_sidebar',
					'show_if' => array( 'product_sidebar', '=', 1 ),
					'place_if' => class_exists( 'woocommerce' ),
				),
				'product_sidebar_pos' => array(
					'title' => __( 'Sidebar Position', 'us' ),
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'show_if' => array( 'product_sidebar', '=', 1 ),
					'place_if' => class_exists( 'woocommerce' ),
				),

				// Sidebar for Events pages
				'h_sidebar_9' => array(
					'title' => us_translate( 'Events', 'the-events-calendar' ),
					'type' => 'heading',
					'classes' => 'with_separator',
					'place_if' => class_exists( 'Tribe__Events__Main' ),
				),
				'event_sidebar' => array(
					'type' => 'switch',
					'text' => __( 'Show Sidebar', 'us' ),
					'std' => 0,
					'classes' => 'width_full',
					'place_if' => class_exists( 'Tribe__Events__Main' ),
				),
				'event_sidebar_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'description' => sprintf( __( 'You can edit selected sidebar or create a new one on the %s page', 'us' ), '<a href="' . admin_url( 'widgets.php' ) . '" target="_blank">' . us_translate( 'Widgets' ) . '</a>' ),
					'type' => 'select',
					'options' => $sidebars_options,
					'std' => 'default_sidebar',
					'show_if' => array( 'event_sidebar', '=', 1 ),
					'place_if' => class_exists( 'Tribe__Events__Main' ),
				),
				'event_sidebar_pos' => array(
					'title' => __( 'Sidebar Position', 'us' ),
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'show_if' => array( 'event_sidebar', '=', 1 ),
					'place_if' => class_exists( 'Tribe__Events__Main' ),
				),

				// Sidebar for Forum pages
				'h_sidebar_10' => array(
					'title' => us_translate( 'Forums', 'bbpress' ),
					'type' => 'heading',
					'classes' => 'with_separator',
					'place_if' => class_exists( 'bbPress' ),
				),
				'forum_sidebar' => array(
					'type' => 'switch',
					'text' => __( 'Show Sidebar', 'us' ),
					'std' => 0,
					'classes' => 'width_full',
					'place_if' => class_exists( 'bbPress' ),
				),
				'forum_sidebar_id' => array(
					'title' => __( 'Sidebar', 'us' ),
					'description' => sprintf( __( 'You can edit selected sidebar or create a new one on the %s page', 'us' ), '<a href="' . admin_url( 'widgets.php' ) . '" target="_blank">' . us_translate( 'Widgets' ) . '</a>' ),
					'type' => 'select',
					'options' => $sidebars_options,
					'std' => 'default_sidebar',
					'show_if' => array( 'forum_sidebar', '=', 1 ),
					'place_if' => class_exists( 'bbPress' ),
				),
				'forum_sidebar_pos' => array(
					'title' => __( 'Sidebar Position', 'us' ),
					'type' => 'radio',
					'options' => array(
						'left' => us_translate( 'Left' ),
						'right' => us_translate( 'Right' ),
					),
					'std' => 'right',
					'show_if' => array( 'forum_sidebar', '=', 1 ),
					'place_if' => class_exists( 'bbPress' ),
				),

			),
			$usof_cpt_sidebars_config
		),
	),
	'footer' => array(
		'title' => __( 'Footers', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/footer',
		'new' => TRUE,
		'fields' => array(

			// Footer Defaults
			'h_footer_1' => array(
				'title' => __( 'Defaults', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'footer_id' => array(
				'title' => __( 'Footer', 'us' ),
				'description' => sprintf( __( 'You can edit selected footer or create a new one on the %s page', 'us' ), '<a href="' . admin_url() . 'edit.php?post_type=us_footer" target="_blank">' . __( 'Footers', 'us' ) . '</a>' ),
				'type' => 'select',
				'options' => $usof_footers_list,
				'std' => 'default-footer',
				'classes' => 'width_full desc_4',
			),

			// Footer for Portfolio Pages
			'h_footer_2' => array(
				'title' => __( 'Portfolio Pages', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
				'place_if' => ( $usof_enable_portfolio == 1 ),
			),
			'footer_portfolio_defaults' => array(
				'type' => 'switch',
				'text' => __( 'Use Defaults', 'us' ),
				'std' => 1,
				'classes' => 'width_full',
				'place_if' => ( $usof_enable_portfolio == 1 ),
			),
			'footer_portfolio_id' => array(
				'title' => __( 'Footer', 'us' ),
				'description' => sprintf( __( 'You can edit selected footer or create a new one on the %s page', 'us' ), '<a href="' . admin_url() . 'edit.php?post_type=us_footer" target="_blank">' . __( 'Footers', 'us' ) . '</a>' ),
				'type' => 'select',
				'options' => $usof_footers_list,
				'std' => 'default-footer',
				'classes' => 'width_full desc_4',
				'show_if' => array( 'footer_portfolio_defaults', '=', 0 ),
				'place_if' => ( $usof_enable_portfolio == 1 ),
			),

			// Footer for Posts
			'h_footer_3' => array(
				'title' => us_translate_x( 'Posts', 'post type general name' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'footer_post_defaults' => array(
				'type' => 'switch',
				'text' => __( 'Use Defaults', 'us' ),
				'std' => 1,
				'classes' => 'width_full',
			),
			'footer_post_id' => array(
				'title' => __( 'Footer', 'us' ),
				'description' => sprintf( __( 'You can edit selected footer or create a new one on the %s page', 'us' ), '<a href="' . admin_url() . 'edit.php?post_type=us_footer" target="_blank">' . __( 'Footers', 'us' ) . '</a>' ),
				'type' => 'select',
				'options' => $usof_footers_list,
				'std' => 'default-footer',
				'classes' => 'width_full desc_4',
				'show_if' => array( 'footer_post_defaults', '=', 0 ),
			),

			// Footer for Archive, Search Results Pages
			'h_footer_4' => array(
				'title' => __( 'Archive, Search Results Pages', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'footer_archive_defaults' => array(
				'type' => 'switch',
				'text' => __( 'Use Defaults', 'us' ),
				'std' => 1,
				'classes' => 'width_full',
			),
			'footer_archive_id' => array(
				'title' => __( 'Footer', 'us' ),
				'description' => sprintf( __( 'You can edit selected footer or create a new one on the %s page', 'us' ), '<a href="' . admin_url() . 'edit.php?post_type=us_footer" target="_blank">' . __( 'Footers', 'us' ) . '</a>' ),
				'type' => 'select',
				'options' => $usof_footers_list,
				'std' => 'default-footer',
				'classes' => 'width_full desc_4',
				'show_if' => array( 'footer_archive_defaults', '=', 0 ),
			),

			// Footer for Shop Pages
			'h_footer_5' => array(
				'title' => __( 'Shop Pages', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
				'place_if' => class_exists( 'woocommerce' ),
			),
			'footer_shop_defaults' => array(
				'type' => 'switch',
				'text' => __( 'Use Defaults', 'us' ),
				'std' => 1,
				'classes' => 'width_full',
				'place_if' => class_exists( 'woocommerce' ),
			),
			'footer_shop_id' => array(
				'title' => __( 'Footer', 'us' ),
				'description' => sprintf( __( 'You can edit selected footer or create a new one on the %s page', 'us' ), '<a href="' . admin_url() . 'edit.php?post_type=us_footer" target="_blank">' . __( 'Footers', 'us' ) . '</a>' ),
				'type' => 'select',
				'options' => $usof_footers_list,
				'std' => 'default-footer',
				'classes' => 'width_full desc_4',
				'show_if' => array( 'footer_shop_defaults', '=', 0 ),
				'place_if' => class_exists( 'woocommerce' ),
			),

			// Footer for Products
			'h_footer_6' => array(
				'title' => us_translate( 'Products', 'woocommerce' ),
				'type' => 'heading',
				'classes' => 'with_separator',
				'place_if' => class_exists( 'woocommerce' ),
			),
			'footer_product_defaults' => array(
				'type' => 'switch',
				'text' => __( 'Use Defaults', 'us' ),
				'std' => 1,
				'classes' => 'width_full',
				'place_if' => class_exists( 'woocommerce' ),
			),
			'footer_product_id' => array(
				'title' => __( 'Footer', 'us' ),
				'description' => sprintf( __( 'You can edit selected footer or create a new one on the %s page', 'us' ), '<a href="' . admin_url() . 'edit.php?post_type=us_footer" target="_blank">' . __( 'Footers', 'us' ) . '</a>' ),
				'type' => 'select',
				'options' => $usof_footers_list,
				'std' => 'default-footer',
				'classes' => 'width_full desc_4',
				'show_if' => array( 'footer_product_defaults', '=', 0 ),
				'place_if' => class_exists( 'woocommerce' ),
			),

		),
	),
	'typography' => array(
		'title' => __( 'Typography', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/style',
		'fields' => array(
			'h_typography_1' => array(
				'title' => __( 'Regular Text', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'body_font_family' => array(
				'type' => 'font',
				'preview' => array(
					'text' => __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque vel egestas leo. Nunc vel mauris eleifend, scelerisque nibh a, viverra quam. Proin eu venenatis ipsum. Nullam accumsan, velit nec egestas dictum, magna magna ornare nulla, a egestas mauris urna in neque.', 'us' ),
					'size_field' => 'body_fontsize',
					'lineheight_field' => 'body_lineheight',
				),
				'std' => 'Open Sans|400,700',
			),
			'body_font_start' => array(
				'type' => 'wrapper_start',
			),
			'body_fontsize' => array(
				'description' => __( 'Font Size', 'us' ),
				'type' => 'slider',
				'min' => 10,
				'max' => 20,
				'std' => 15,
				'postfix' => 'px',
				'classes' => 'inline cols_2 compact',
			),
			'body_fontsize_mobile' => array(
				'description' => __( 'Font Size on Mobiles', 'us' ),
				'type' => 'slider',
				'min' => 10,
				'max' => 20,
				'std' => 15,
				'postfix' => 'px',
				'classes' => 'inline cols_2 compact',
			),
			'body_lineheight' => array(
				'description' => __( 'Line height', 'us' ),
				'type' => 'slider',
				'min' => 15,
				'max' => 35,
				'std' => 25,
				'postfix' => 'px',
				'classes' => 'inline cols_2 compact',
			),
			'body_lineheight_mobile' => array(
				'description' => __( 'Line height on Mobiles', 'us' ),
				'type' => 'slider',
				'min' => 15,
				'max' => 35,
				'std' => 25,
				'postfix' => 'px',
				'classes' => 'inline cols_2 compact',
			),
			'body_font_end' => array(
				'type' => 'wrapper_end',
			),
			
			'h_typography_2' => array(
				'title' => __( 'Headings', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'heading_font_family' => array(
				'type' => 'font',
				'preview' => array(
					'text' => __( 'Heading Font Preview', 'us' ),
					'size_field' => 'h1_fontsize',
					'weight_field' => 'h1_fontweight',
					'letterspacing_field' => 'h1_letterspacing',
					'transform_field' => 'h1_transform',
				),
				'std' => 'none',
			),
			'h1_start' => array(
				'title' => us_translate( 'Heading 1' ),
				'type' => 'wrapper_start',
			),
			'h1_fontsize' => array(
				'description' => __( 'Font Size', 'us' ),
				'type' => 'slider',
				'min' => 10,
				'max' => 60,
				'std' => 40,
				'postfix' => 'px',
				'classes' => 'inline compact',
			),
			'h1_fontsize_mobile' => array(
				'description' => __( 'Font Size on Mobiles', 'us' ),
				'type' => 'slider',
				'min' => 10,
				'max' => 60,
				'std' => 30,
				'postfix' => 'px',
				'classes' => 'inline compact',
			),
			'h1_fontweight' => array(
				'description' => __( 'Font Weight', 'us' ),
				'type' => 'slider',
				'min' => 100,
				'max' => 900,
				'step' => 100,
				'std' => 400,
				'classes' => 'inline compact',
			),
			'h1_letterspacing' => array(
				'description' => __( 'Letter Spacing', 'us' ),
				'type' => 'slider',
				'min' => -0.10,
				'max' => 0.20,
				'step' => 0.01,
				'std' => 0,
				'postfix' => 'em',
				'classes' => 'inline compact',
			),
			'h1_transform' => array(
				'type' => 'checkboxes',
				'options' => array(
					'uppercase' => __( 'Uppercase', 'us' ),
					'italic' => __( 'Italic', 'us' ),
				),
				'std' => array(),
				'classes' => 'inline',
			),
			'h1_end' => array(
				'type' => 'wrapper_end',
			),
			'h2_start' => array(
				'title' => us_translate( 'Heading 2' ),
				'type' => 'wrapper_start',
			),
			'h2_fontsize' => array(
				'description' => __( 'Font Size', 'us' ),
				'type' => 'slider',
				'min' => 10,
				'max' => 60,
				'std' => 34,
				'postfix' => 'px',
				'classes' => 'inline compact',
			),
			'h2_fontsize_mobile' => array(
				'description' => __( 'Font Size on Mobiles', 'us' ),
				'type' => 'slider',
				'min' => 10,
				'max' => 60,
				'std' => 26,
				'postfix' => 'px',
				'classes' => 'inline compact',
			),
			'h2_fontweight' => array(
				'description' => __( 'Font Weight', 'us' ),
				'type' => 'slider',
				'min' => 100,
				'max' => 900,
				'step' => 100,
				'std' => 400,
				'classes' => 'inline compact',
			),
			'h2_letterspacing' => array(
				'description' => __( 'Letter Spacing', 'us' ),
				'type' => 'slider',
				'min' => -0.10,
				'max' => 0.20,
				'step' => 0.01,
				'std' => 0,
				'postfix' => 'em',
				'classes' => 'inline compact',
			),
			'h2_transform' => array(
				'type' => 'checkboxes',
				'options' => array(
					'uppercase' => __( 'Uppercase', 'us' ),
					'italic' => __( 'Italic', 'us' ),
				),
				'std' => array(),
				'classes' => 'inline',
			),
			'h2_end' => array(
				'type' => 'wrapper_end',
			),
			'h3_start' => array(
				'title' => us_translate( 'Heading 3' ),
				'type' => 'wrapper_start',
			),
			'h3_fontsize' => array(
				'description' => __( 'Font Size', 'us' ),
				'type' => 'slider',
				'min' => 10,
				'max' => 60,
				'std' => 28,
				'postfix' => 'px',
				'classes' => 'inline compact',
			),
			'h3_fontsize_mobile' => array(
				'description' => __( 'Font Size on Mobiles', 'us' ),
				'type' => 'slider',
				'min' => 10,
				'max' => 60,
				'std' => 24,
				'postfix' => 'px',
				'classes' => 'inline compact',
			),
			'h3_fontweight' => array(
				'description' => __( 'Font Weight', 'us' ),
				'type' => 'slider',
				'min' => 100,
				'max' => 900,
				'step' => 100,
				'std' => 400,
				'classes' => 'inline compact',
			),
			'h3_letterspacing' => array(
				'description' => __( 'Letter Spacing', 'us' ),
				'type' => 'slider',
				'min' => -0.10,
				'max' => 0.20,
				'step' => 0.01,
				'std' => 0,
				'postfix' => 'em',
				'classes' => 'inline compact',
			),
			'h3_transform' => array(
				'type' => 'checkboxes',
				'options' => array(
					'uppercase' => __( 'Uppercase', 'us' ),
					'italic' => __( 'Italic', 'us' ),
				),
				'std' => array(),
				'classes' => 'inline',
			),
			'h3_end' => array(
				'type' => 'wrapper_end',
			),
			'h4_start' => array(
				'title' => us_translate( 'Heading 4' ),
				'type' => 'wrapper_start',
			),
			'h4_fontsize' => array(
				'description' => __( 'Font Size', 'us' ),
				'type' => 'slider',
				'min' => 10,
				'max' => 60,
				'std' => 24,
				'postfix' => 'px',
				'classes' => 'inline compact',
			),
			'h4_fontsize_mobile' => array(
				'description' => __( 'Font Size on Mobiles', 'us' ),
				'type' => 'slider',
				'min' => 10,
				'max' => 60,
				'std' => 22,
				'postfix' => 'px',
				'classes' => 'inline compact',
			),
			'h4_fontweight' => array(
				'description' => __( 'Font Weight', 'us' ),
				'type' => 'slider',
				'min' => 100,
				'max' => 900,
				'step' => 100,
				'std' => 400,
				'classes' => 'inline compact',
			),
			'h4_letterspacing' => array(
				'description' => __( 'Letter Spacing', 'us' ),
				'type' => 'slider',
				'min' => -0.10,
				'max' => 0.20,
				'step' => 0.01,
				'std' => 0,
				'postfix' => 'em',
				'classes' => 'inline compact',
			),
			'h4_transform' => array(
				'type' => 'checkboxes',
				'options' => array(
					'uppercase' => __( 'Uppercase', 'us' ),
					'italic' => __( 'Italic', 'us' ),
				),
				'std' => array(),
				'classes' => 'inline',
			),
			'h4_end' => array(
				'type' => 'wrapper_end',
			),
			'h5_start' => array(
				'title' => us_translate( 'Heading 5' ),
				'type' => 'wrapper_start',
			),
			'h5_fontsize' => array(
				'description' => __( 'Font Size', 'us' ),
				'type' => 'slider',
				'min' => 10,
				'max' => 60,
				'std' => 20,
				'postfix' => 'px',
				'classes' => 'inline compact',
			),
			'h5_fontsize_mobile' => array(
				'description' => __( 'Font Size on Mobiles', 'us' ),
				'type' => 'slider',
				'min' => 10,
				'max' => 60,
				'std' => 20,
				'postfix' => 'px',
				'classes' => 'inline compact',
			),
			'h5_fontweight' => array(
				'description' => __( 'Font Weight', 'us' ),
				'type' => 'slider',
				'min' => 100,
				'max' => 900,
				'step' => 100,
				'std' => 400,
				'classes' => 'inline compact',
			),
			'h5_letterspacing' => array(
				'description' => __( 'Letter Spacing', 'us' ),
				'type' => 'slider',
				'min' => -0.10,
				'max' => 0.20,
				'step' => 0.01,
				'std' => 0,
				'postfix' => 'em',
				'classes' => 'inline compact',
			),
			'h5_transform' => array(
				'type' => 'checkboxes',
				'options' => array(
					'uppercase' => __( 'Uppercase', 'us' ),
					'italic' => __( 'Italic', 'us' ),
				),
				'std' => array(),
				'classes' => 'inline',
			),
			'h5_end' => array(
				'type' => 'wrapper_end',
			),
			'h6_start' => array(
				'title' => us_translate( 'Heading 6' ),
				'type' => 'wrapper_start',
			),
			'h6_fontsize' => array(
				'description' => __( 'Font Size', 'us' ),
				'type' => 'slider',
				'min' => 10,
				'max' => 60,
				'std' => 18,
				'postfix' => 'px',
				'classes' => 'inline compact',
			),
			'h6_fontsize_mobile' => array(
				'description' => __( 'Font Size on Mobiles', 'us' ),
				'type' => 'slider',
				'min' => 10,
				'max' => 60,
				'std' => 18,
				'postfix' => 'px',
				'classes' => 'inline compact',
			),
			'h6_fontweight' => array(
				'description' => __( 'Font Weight', 'us' ),
				'type' => 'slider',
				'min' => 100,
				'max' => 900,
				'step' => 100,
				'std' => 400,
				'classes' => 'inline compact',
			),
			'h6_letterspacing' => array(
				'description' => __( 'Letter Spacing', 'us' ),
				'type' => 'slider',
				'min' => -0.10,
				'max' => 0.20,
				'step' => 0.01,
				'std' => 0,
				'postfix' => 'em',
				'classes' => 'inline compact',
			),
			'h6_transform' => array(
				'type' => 'checkboxes',
				'options' => array(
					'uppercase' => __( 'Uppercase', 'us' ),
					'italic' => __( 'Italic', 'us' ),
				),
				'std' => array(),
				'classes' => 'inline',
			),
			'h6_end' => array(
				'type' => 'wrapper_end',
			),
			
			'h_typography_3' => array(
				'title' => __( 'Header Menu', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'menu_font_family' => array(
				'type' => 'font',
				'preview' => array(
					'text' => __( 'Home About Services Portfolio Contacts', 'us' ),
				),
				'std' => 'none',
			),
			'h_typography_4' => array(
				'title' => __( 'Subset', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'font_subset' => array(
				'description' => sprintf( __( 'Check available subsets for needed fonts on %s website', 'us' ), '<a href="https://fonts.google.com/" target="_blank">Google Fonts</a>' ),
				'type' => 'select',
				'options' => array(
					'arabic' => 'arabic',
					'cyrillic' => 'cyrillic',
					'cyrillic-ext' => 'cyrillic-ext',
					'greek' => 'greek',
					'greek-ext' => 'greek-ext',
					'hebrew' => 'hebrew',
					'khmer' => 'khmer',
					'latin' => 'latin',
					'latin-ext' => 'latin-ext',
					'vietnamese' => 'vietnamese',
				),
				'std' => 'latin',
				'classes' => 'width_full desc_1',
			),
		),
	),
	'buttons' => array(
		'title' => __( 'Buttons', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/buttons',
		'fields' => array(
			'button_preview' => array(
				'type' => 'button_preview',
				'classes' => 'width_full sticky',
			),
			'button_text_style' => array(
				'title' => __( 'Text Styles', 'us' ),
				'type' => 'checkboxes',
				'options' => array(
					'uppercase' => __( 'Uppercase', 'us' ),
					'italic' => __( 'Italic', 'us' ),
				),
				'std' => array(),
			),
			'button_fontsize' => array(
				'title' => __( 'Default Font Size', 'us' ),
				'type' => 'slider',
				'min' => 10,
				'max' => 20,
				'std' => 15,
				'postfix' => 'px',
			),
			'button_fontweight' => array(
				'title' => __( 'Font Weight', 'us' ),
				'type' => 'slider',
				'min' => 100,
				'max' => 900,
				'step' => 100,
				'std' => 400,
			),
			'button_letterspacing' => array(
				'title' => __( 'Letter Spacing', 'us' ),
				'type' => 'slider',
				'min' => -0.10,
				'max' => 0.20,
				'step' => 0.01,
				'std' => 0,
				'postfix' => 'em',
			),
			'button_font' => array(
				'title' => __( 'Use Font from', 'us' ),
				'type' => 'radio',
				'options' => array(
					'body' => __( 'Regular Text', 'us' ),
					'heading' => __( 'Headings', 'us' ),
					'menu' => __( 'Header Menu', 'us' ),
				),
				'std' => 'body',
			),
			'button_height' => array(
				'title' => __( 'Relative Height', 'us' ),
				'type' => 'slider',
				'min' => 2.0,
				'max' => 5.0,
				'step' => 0.1,
				'std' => 2.8,
				'postfix' => 'em',
			),
			'button_width' => array(
				'title' => __( 'Relative Width', 'us' ),
				'type' => 'slider',
				'min' => 0.5,
				'max' => 5.0,
				'step' => 0.1,
				'std' => 1.8,
				'postfix' => 'em',
			),
			'button_border_radius' => array(
				'title' => __( 'Corners Radius', 'us' ),
				'type' => 'slider',
				'min' => 0.0,
				'max' => 2.5,
				'step' => 0.1,
				'std' => 0.3,
				'postfix' => 'em',
			),
			'button_shadow' => array(
				'title' => __( 'Shadow', 'us' ),
				'type' => 'slider',
				'min' => 0.0,
				'max' => 2.0,
				'step' => 0.1,
				'std' => 0,
				'postfix' => 'em',
			),
			'button_hover' => array(
				'title' => __( 'Hover Style', 'us' ),
				'type' => 'radio',
				'options' => array(
					'none' => us_translate( 'None' ),
					'fade' => __( 'Fade', 'us' ),
					'slide' => __( 'Slide', 'us' ),
					'reverse' => __( 'Reverse', 'us' ),
				),
				'std' => 'slide',
			),
			'button_shadow_hover' => array(
				'title' => __( 'Shadow on Hover', 'us' ),
				'type' => 'slider',
				'min' => 0.0,
				'max' => 2.0,
				'step' => 0.1,
				'std' => 0,
				'postfix' => 'em',
			),
		),
	),
	'portfolio' => array(
		'title' => __( 'Portfolio', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/images',
		'place_if' => ( $usof_enable_portfolio == 1 ),
		'fields' => array(
			'h_portfolio_1' => array(
				'title' => __( 'Portfolio Pages', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'portfolio_breadcrumbs_page' => array(
				'title' => __( 'Intermediate Breadcrumbs Page', 'us' ),
				'type' => 'select',
				'options' => array_merge( array( '' => '&ndash; ' . us_translate( 'None' ) . ' &ndash;' ), $usof_wp_pages ),
				'std' => '',
			),
			'portfolio_comments' => array(
				'title' => us_translate( 'Comments' ),
				'type' => 'switch',
				'text' => __( 'Enable comments for Portfolio Pages', 'us' ),
				'std' => 0,
			),
			'portfolio_nav' => array(
				'title' => __( 'Prev/Next Navigation', 'us' ),
				'type' => 'switch',
				'text' => __( 'Show previous/next portfolio pages on sides of the screen', 'us' ),
				'std' => 1,
			),
			'portfolio_nav_invert' => array(
				'type' => 'switch',
				'text' => __( 'Invert position of previous and next', 'us' ),
				'std' => 0,
				'classes' => 'for_above',
				'show_if' => array( 'portfolio_nav', '=', TRUE ),
			),
			'portfolio_nav_category' => array(
				'type' => 'switch',
				'text' => __( 'Navigate within a category', 'us' ),
				'std' => 0,
				'classes' => 'for_above',
				'show_if' => array( 'portfolio_nav', '=', TRUE ),
			),
			'h_portfolio_2' => array(
				'title' => __( 'More Options', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'portfolio_slug' => array(
				'title' => __( 'Portfolio Page Slug', 'us' ),
				'type' => 'text',
				'std' => 'portfolio',
			),
			'portfolio_category_slug' => array(
				'title' => __( 'Portfolio Category Slug', 'us' ),
				'type' => 'text',
				'std' => 'portfolio_category',
			),

			// Portfolio Responsive Breakpoint 1
			'portfolio_breakpoint_1_start' => array(
				'title' => __( 'Portfolio Responsive Behavior', 'us' ),
				'type' => 'wrapper_start',
				'classes' => 'title_left',
			),
			'portfolio_breakpoint_1_width' => array(
				'title' => __( 'Below screen width', 'us' ),
				'description' => __( 'show', 'us' ),
				'type' => 'slider',
				'min' => 300,
				'max' => 1400,
				'step' => 10,
				'std' => 1200,
				'postfix' => 'px',
				'classes' => 'inline compact',
			),
			'portfolio_breakpoint_1_cols' => array(
				'type' => 'select',
				'options' => array(
					'6' => sprintf( us_translate_n( '%s column', '%s columns', 6 ), 6 ),
					'5' => sprintf( us_translate_n( '%s column', '%s columns', 5 ), 5 ),
					'4' => sprintf( us_translate_n( '%s column', '%s columns', 4 ), 4 ),
					'3' => sprintf( us_translate_n( '%s column', '%s columns', 3 ), 3 ),
					'2' => sprintf( us_translate_n( '%s column', '%s columns', 2 ), 2 ),
					'1' => sprintf( us_translate_n( '%s column', '%s columns', 1 ), 1 ),
				),
				'std' => '3',
				'classes' => 'inline',
			),
			'portfolio_breakpoint_1_end' => array(
				'type' => 'wrapper_end',
			),

			// Portfolio Responsive Breakpoint 2
			'portfolio_breakpoint_2_start' => array(
				'title' => ' ',
				'type' => 'wrapper_start',
				'classes' => 'title_left',
			),
			'portfolio_breakpoint_2_width' => array(
				'title' => __( 'Below screen width', 'us' ),
				'description' => __( 'show', 'us' ),
				'type' => 'slider',
				'min' => 300,
				'max' => 1400,
				'step' => 10,
				'std' => 900,
				'postfix' => 'px',
				'classes' => 'inline compact',
			),
			'portfolio_breakpoint_2_cols' => array(
				'type' => 'select',
				'options' => array(
					'6' => sprintf( us_translate_n( '%s column', '%s columns', 6 ), 6 ),
					'5' => sprintf( us_translate_n( '%s column', '%s columns', 5 ), 5 ),
					'4' => sprintf( us_translate_n( '%s column', '%s columns', 4 ), 4 ),
					'3' => sprintf( us_translate_n( '%s column', '%s columns', 3 ), 3 ),
					'2' => sprintf( us_translate_n( '%s column', '%s columns', 2 ), 2 ),
					'1' => sprintf( us_translate_n( '%s column', '%s columns', 1 ), 1 ),
				),
				'std' => '2',
				'classes' => 'inline',
			),
			'portfolio_breakpoint_2_end' => array(
				'type' => 'wrapper_end',
			),

			// Portfolio Responsive Breakpoint 3
			'portfolio_breakpoint_3_start' => array(
				'title' => ' ',
				'type' => 'wrapper_start',
				'classes' => 'title_left',
			),
			'portfolio_breakpoint_3_width' => array(
				'title' => __( 'Below screen width', 'us' ),
				'description' => __( 'show', 'us' ),
				'type' => 'slider',
				'min' => 300,
				'max' => 1400,
				'step' => 10,
				'std' => 600,
				'postfix' => 'px',
				'classes' => 'inline compact',
			),
			'portfolio_breakpoint_3_cols' => array(
				'type' => 'select',
				'options' => array(
					'6' => sprintf( us_translate_n( '%s column', '%s columns', 6 ), 6 ),
					'5' => sprintf( us_translate_n( '%s column', '%s columns', 5 ), 5 ),
					'4' => sprintf( us_translate_n( '%s column', '%s columns', 4 ), 4 ),
					'3' => sprintf( us_translate_n( '%s column', '%s columns', 3 ), 3 ),
					'2' => sprintf( us_translate_n( '%s column', '%s columns', 2 ), 2 ),
					'1' => sprintf( us_translate_n( '%s column', '%s columns', 1 ), 1 ),
				),
				'std' => '1',
				'classes' => 'inline',
			),
			'portfolio_breakpoint_3_end' => array(
				'type' => 'wrapper_end',
			),
		),
	),
	'blog' => array(
		'title' => us_translate( 'Blog' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/blogs',
		'fields' => array(

			// Posts
			'h_blog_1' => array(
				'title' => us_translate_x( 'Posts', 'post type general name' ),
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			'post_preview_layout' => array(
				'title' => __( 'Featured Image Layout', 'us' ),
				'type' => 'radio',
				'options' => array(
					'basic' => __( 'Standard', 'us' ),
					'modern' => __( 'Modern', 'us' ),
					'trendy' => __( 'Trendy', 'us' ),
					'none' => __( 'No Preview', 'us' ),
				),
				'std' => 'basic',
			),
			'post_preview_img_size' => array(
				'title' => __( 'Featured Image Size', 'us' ),
				'type' => 'select',
				'options' => $usof_img_sizes,
				'std' => 'large',
				'show_if' => array( 'post_preview_layout', '!=', 'none' ),
			),
			'post_meta' => array(
				'title' => __( 'Post Elements', 'us' ),
				'type' => 'checkboxes',
				'options' => array(
					'date' => us_translate( 'Date' ),
					'author' => us_translate( 'Author' ),
					'categories' => us_translate( 'Categories' ),
					'comments' => us_translate( 'Comments' ),
					'tags' => us_translate( 'Tags' ),
				),
				'std' => array( 'date', 'author', 'categories', 'comments', 'tags' ),
			),
			'post_sharing' => array(
				'title' => __( 'Sharing Buttons', 'us' ),
				'type' => 'switch',
				'text' => __( 'Show block with sharing buttons', 'us' ),
				'std' => 0,
			),
			'wrapper_post_sharing_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'force_right',
				'show_if' => array( 'post_sharing', '=', TRUE ),
			),
			'post_sharing_providers' => array(
				'title' => '',
				'type' => 'checkboxes',
				'options' => array(
					'email' => 'Email',
					'facebook' => 'Facebook',
					'twitter' => 'Twitter',
					'gplus' => 'Google+',
					'linkedin' => 'LinkedIn',
					'pinterest' => 'Pinterest',
					'vk' => 'Vkontakte',
				),
				'std' => array( 'facebook', 'twitter', 'gplus' ),
				'classes' => 'width_full',
			),
			'post_sharing_type' => array(
				'title' => __( 'Buttons Style', 'us' ),
				'type' => 'radio',
				'options' => array(
					'simple' => __( 'Simple', 'us' ),
					'solid' => __( 'Solid', 'us' ),
					'outlined' => __( 'Outlined', 'us' ),
				),
				'std' => 'simple',
				'classes' => 'width_full',
			),
			'wrapper_post_sharing_end' => array(
				'type' => 'wrapper_end',
			),
			'post_author_box' => array(
				'title' => __( 'Author Info', 'us' ),
				'type' => 'switch',
				'text' => __( 'Show block with information about post author', 'us' ),
				'std' => 0,
			),
			'post_nav' => array(
				'title' => __( 'Prev/Next Navigation', 'us' ),
				'type' => 'switch',
				'text' => __( 'Show links to previous/next posts', 'us' ),
				'std' => 0,
			),
			'wrapper_post_nav_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'force_right',
				'show_if' => array( 'post_nav', '=', TRUE ),
			),
			'post_nav_layout' => array(
				'title' => __( 'Layout', 'us' ),
				'type' => 'radio',
				'options' => array(
					'default' => __( 'Below a post content', 'us' ),
					'sided' => __( 'On sides of the screen', 'us' ),
				),
				'std' => 'default',
				'classes' => 'width_full',
			),
			'post_nav_invert' => array(
				'type' => 'switch',
				'text' => __( 'Invert position of previous and next', 'us' ),
				'std' => 0,
				'classes' => 'width_full',
			),
			'post_nav_category' => array(
				'type' => 'switch',
				'text' => __( 'Navigate within a category', 'us' ),
				'std' => 0,
				'classes' => 'width_full',
			),
			'wrapper_post_nav_end' => array(
				'type' => 'wrapper_end',
			),
			'post_related' => array(
				'title' => __( 'Related Posts', 'us' ),
				'type' => 'switch',
				'text' => __( 'Show list of posts with the same tags', 'us' ),
				'std' => 0,
			),
			'wrapper_post_related_start' => array(
				'type' => 'wrapper_start',
				'classes' => 'force_right',
				'show_if' => array( 'post_related', '=', TRUE ),
			),
			'post_related_layout' => array(
				'title' => __( 'Layout', 'us' ),
				'type' => 'radio',
				'options' => array(
					'compact' => __( 'Compact (without preview)', 'us' ),
					'related' => __( 'Standard (3 columns with preview)', 'us' ),
				),
				'std' => 'compact',
				'classes' => 'width_full',
			),
			'post_related_img_size' => array(
				'title' => __( 'Images Size', 'us' ),
				'type' => 'select',
				'options' => $usof_img_sizes,
				'std' => 'tnail-1x1-small',
				'show_if' => array( 'post_related_layout', '=', 'related' ),
				'classes' => 'width_full',
			),
			'wrapper_post_related_end' => array(
				'type' => 'wrapper_end',
			),

			// Blog Home Page
			'h_blog_2' => array(
				'title' => __( 'Blog Home Page', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			'blog_type' => array(
				'title' => __( 'Display Posts as', 'us' ),
				'type' => 'radio',
				'options' => array(
					'grid' => __( 'Grid', 'us' ),
					'masonry' => __( 'Masonry', 'us' ),
				),
				'std' => 'grid',
			),
			'blog_layout' => array(
				'title' => __( 'Layout', 'us' ),
				'type' => 'select',
				'options' => array(
					'classic' => __( 'Classic', 'us' ),
					'flat' => __( 'Flat', 'us' ),
					'tiles' => __( 'Tiles', 'us' ),
					'cards' => __( 'Cards', 'us' ),
					'smallcircle' => __( 'Small Circle Image', 'us' ),
					'smallsquare' => __( 'Small Square Image', 'us' ),
					'latest' => __( 'Latest Posts', 'us' ),
					'compact' => __( 'Compact', 'us' ),
				),
				'std' => 'classic',
			),
			'blog_img_size' => array(
				'title' => __( 'Images Size', 'us' ),
				'type' => 'select',
				'options' => array_merge( array( 'default' => us_translate( 'Default' ) ), $usof_img_sizes ),
				'std' => 'default',
				'show_if' => array( 'blog_layout', 'not in', array( 'latest', 'compact' ) ),
			),
			'blog_cols' => array(
				'title' => __( 'Posts Columns', 'us' ),
				'std' => '1',
				'type' => 'radio',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
			),
			'blog_content_type' => array(
				'title' => __( 'Posts Content', 'us' ),
				'type' => 'radio',
				'options' => array(
					'excerpt' => us_translate( 'Excerpt' ),
					'content' => __( 'Full Content', 'us' ),
					'none' => us_translate( 'None' ),
				),
				'std' => 'excerpt',
			),
			'blog_meta' => array(
				'title' => __( 'Posts Elements', 'us' ),
				'type' => 'checkboxes',
				'options' => array(
					'date' => us_translate( 'Date' ),
					'author' => us_translate( 'Author' ),
					'categories' => us_translate( 'Categories' ),
					'comments' => us_translate( 'Comments' ),
					'tags' => us_translate( 'Tags' ),
					'read_more' => __( 'Read More button', 'us' ),
				),
				'std' => array( 'date', 'author', 'categories', 'comments', 'tags', 'read_more' ),
			),
			'blog_pagination' => array(
				'title' => us_translate( 'Pagination' ),
				'type' => 'radio',
				'options' => array(
					'regular' => __( 'Regular pagination', 'us' ),
					'ajax' => __( 'Load More Button', 'us' ),
					'infinite' => __( 'Infinite Scroll', 'us' ),
				),
				'std' => 'regular',
			),

			// Archive Pages
			'h_blog_3' => array(
				'title' => __( 'Archive Pages', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			'archive_type' => array(
				'title' => __( 'Display Posts as', 'us' ),
				'type' => 'radio',
				'options' => array(
					'grid' => __( 'Grid', 'us' ),
					'masonry' => __( 'Masonry', 'us' ),
				),
				'std' => 'grid',
			),
			'archive_layout' => array(
				'title' => __( 'Layout', 'us' ),
				'type' => 'select',
				'options' => array(
					'classic' => __( 'Classic', 'us' ),
					'flat' => __( 'Flat', 'us' ),
					'tiles' => __( 'Tiles', 'us' ),
					'cards' => __( 'Cards', 'us' ),
					'smallcircle' => __( 'Small Circle Image', 'us' ),
					'smallsquare' => __( 'Small Square Image', 'us' ),
					'latest' => __( 'Latest Posts', 'us' ),
					'compact' => __( 'Compact', 'us' ),
				),
				'std' => 'smallcircle',
			),
			'archive_img_size' => array(
				'title' => __( 'Images Size', 'us' ),
				'type' => 'select',
				'options' => array_merge( array( 'default' => us_translate( 'Default' ) ), $usof_img_sizes ),
				'std' => 'default',
				'show_if' => array( 'archive_layout', 'not in', array( 'latest', 'compact' ) ),
			),
			'archive_cols' => array(
				'title' => __( 'Posts Columns', 'us' ),
				'std' => '1',
				'type' => 'radio',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
			),
			'archive_content_type' => array(
				'title' => __( 'Posts Content', 'us' ),
				'type' => 'radio',
				'options' => array(
					'excerpt' => us_translate( 'Excerpt' ),
					'content' => __( 'Full Content', 'us' ),
					'none' => us_translate( 'None' ),
				),
				'std' => 'excerpt',
			),
			'archive_meta' => array(
				'title' => __( 'Posts Elements', 'us' ),
				'type' => 'checkboxes',
				'options' => array(
					'date' => us_translate( 'Date' ),
					'author' => us_translate( 'Author' ),
					'categories' => us_translate( 'Categories' ),
					'comments' => us_translate( 'Comments' ),
					'tags' => us_translate( 'Tags' ),
					'read_more' => __( 'Read More button', 'us' ),
				),
				'std' => array( 'date', 'author', 'comments', 'tags' ),
			),
			'archive_pagination' => array(
				'title' => us_translate( 'Pagination' ),
				'type' => 'radio',
				'options' => array(
					'regular' => __( 'Regular pagination', 'us' ),
					'ajax' => __( 'Load More Button', 'us' ),
					'infinite' => __( 'Infinite Scroll', 'us' ),
				),
				'std' => 'regular',
			),

			// Search Results Page
			'h_blog_4' => array(
				'title' => __( 'Search Results Page', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator sticky',
			),
			'search_type' => array(
				'title' => __( 'Display Posts as', 'us' ),
				'type' => 'radio',
				'options' => array(
					'grid' => __( 'Grid', 'us' ),
					'masonry' => __( 'Masonry', 'us' ),
				),
				'std' => 'grid',
			),
			'search_layout' => array(
				'title' => __( 'Layout', 'us' ),
				'type' => 'select',
				'options' => array(
					'classic' => __( 'Classic', 'us' ),
					'flat' => __( 'Flat', 'us' ),
					'tiles' => __( 'Tiles', 'us' ),
					'cards' => __( 'Cards', 'us' ),
					'smallcircle' => __( 'Small Circle Image', 'us' ),
					'smallsquare' => __( 'Small Square Image', 'us' ),
					'latest' => __( 'Latest Posts', 'us' ),
					'compact' => __( 'Compact', 'us' ),
				),
				'std' => 'compact',
			),
			'search_img_size' => array(
				'title' => __( 'Images Size', 'us' ),
				'type' => 'select',
				'options' => array_merge( array( 'default' => us_translate( 'Default' ) ), $usof_img_sizes ),
				'std' => 'default',
				'show_if' => array( 'search_layout', 'not in', array( 'latest', 'compact' ) ),
			),
			'search_cols' => array(
				'title' => __( 'Posts Columns', 'us' ),
				'std' => '1',
				'type' => 'radio',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
			),
			'search_content_type' => array(
				'title' => __( 'Posts Content', 'us' ),
				'type' => 'radio',
				'options' => array(
					'excerpt' => us_translate( 'Excerpt' ),
					'content' => __( 'Full Content', 'us' ),
					'none' => us_translate( 'None' ),
				),
				'std' => 'excerpt',
			),
			'search_meta' => array(
				'title' => __( 'Posts Elements', 'us' ),
				'type' => 'checkboxes',
				'options' => array(
					'date' => us_translate( 'Date' ),
					'author' => us_translate( 'Author' ),
					'categories' => us_translate( 'Categories' ),
					'comments' => us_translate( 'Comments' ),
					'tags' => us_translate( 'Tags' ),
					'read_more' => __( 'Read More button', 'us' ),
				),
				'std' => array( 'date' ),
			),
			'search_pagination' => array(
				'title' => us_translate( 'Pagination' ),
				'type' => 'radio',
				'options' => array(
					'regular' => __( 'Regular pagination', 'us' ),
					'ajax' => __( 'Load More Button', 'us' ),
					'infinite' => __( 'Infinite Scroll', 'us' ),
				),
				'std' => 'regular',
			),
			'h_blog_5' => array(
				'title' => __( 'More Options', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'excerpt_length' => array(
				'title' => __( 'Excerpt Length', 'us' ),
				'description' => __( 'This option sets amount of words in the Excerpt. To show all the words, leave this field blank.', 'us' ),
				'type' => 'text',
				'std' => '55',
			),
			'read_more_btn_style' => array(
				'title' => __( 'Read More Button Style', 'us' ),
				'type' => 'radio',
				'options' => array(
					'solid' => __( 'Solid', 'us' ),
					'outlined' => __( 'Outlined', 'us' ),
				),
				'std' => 'outlined',
			),
			'read_more_btn_color' => array(
				'title' => __( 'Read More Button Color', 'us' ),
				'type' => 'select',
				'options' => array(
					'primary' => __( 'Primary (theme color)', 'us' ),
					'secondary' => __( 'Secondary (theme color)', 'us' ),
					'light' => __( 'Border (theme color)', 'us' ),
					'contrast' => __( 'Text (theme color)', 'us' ),
					'black' => us_translate( 'Black' ),
					'white' => us_translate( 'White' ),
					'purple' => __( 'Purple', 'us' ),
					'pink' => __( 'Pink', 'us' ),
					'red' => __( 'Red', 'us' ),
					'yellow' => __( 'Yellow', 'us' ),
					'lime' => __( 'Lime', 'us' ),
					'green' => __( 'Green', 'us' ),
					'teal' => __( 'Teal', 'us' ),
					'blue' => __( 'Blue', 'us' ),
					'navy' => __( 'Navy', 'us' ),
					'midnight' => __( 'Midnight', 'us' ),
					'brown' => __( 'Brown', 'us' ),
					'cream' => __( 'Cream', 'us' ),
				),
				'std' => 'light',
			),
			'read_more_btn_size' => array(
				'title' => __( 'Read More Button Size', 'us' ),
				'description' => sprintf( __( 'Examples: %s', 'us' ), '26px, 1.3em, 200%' ),
				'type' => 'text',
				'std' => '',
			),

			// Blog Responsive Breakpoint 1
			'blog_breakpoint_1_start' => array(
				'title' => __( 'Blog Responsive Behavior', 'us' ),
				'type' => 'wrapper_start',
				'classes' => 'title_left',
			),
			'blog_breakpoint_1_width' => array(
				'title' => __( 'Below screen width', 'us' ),
				'description' => __( 'show', 'us' ),
				'type' => 'slider',
				'min' => 300,
				'max' => 1400,
				'step' => 10,
				'std' => 1200,
				'postfix' => 'px',
				'classes' => 'inline compact',
			),
			'blog_breakpoint_1_cols' => array(
				'type' => 'select',
				'options' => array(
					'6' => sprintf( us_translate_n( '%s column', '%s columns', 6 ), 6 ),
					'5' => sprintf( us_translate_n( '%s column', '%s columns', 5 ), 5 ),
					'4' => sprintf( us_translate_n( '%s column', '%s columns', 4 ), 4 ),
					'3' => sprintf( us_translate_n( '%s column', '%s columns', 3 ), 3 ),
					'2' => sprintf( us_translate_n( '%s column', '%s columns', 2 ), 2 ),
					'1' => sprintf( us_translate_n( '%s column', '%s columns', 1 ), 1 ),
				),
				'std' => '3',
				'classes' => 'inline',
			),
			'blog_breakpoint_1_end' => array(
				'type' => 'wrapper_end',
			),

			// Blog Responsive Breakpoint 2
			'blog_breakpoint_2_start' => array(
				'title' => ' ',
				'type' => 'wrapper_start',
				'classes' => 'title_left',
			),
			'blog_breakpoint_2_width' => array(
				'title' => __( 'Below screen width', 'us' ),
				'description' => __( 'show', 'us' ),
				'type' => 'slider',
				'min' => 300,
				'max' => 1400,
				'step' => 10,
				'std' => 900,
				'postfix' => 'px',
				'classes' => 'inline compact',
			),
			'blog_breakpoint_2_cols' => array(
				'type' => 'select',
				'options' => array(
					'6' => sprintf( us_translate_n( '%s column', '%s columns', 6 ), 6 ),
					'5' => sprintf( us_translate_n( '%s column', '%s columns', 5 ), 5 ),
					'4' => sprintf( us_translate_n( '%s column', '%s columns', 4 ), 4 ),
					'3' => sprintf( us_translate_n( '%s column', '%s columns', 3 ), 3 ),
					'2' => sprintf( us_translate_n( '%s column', '%s columns', 2 ), 2 ),
					'1' => sprintf( us_translate_n( '%s column', '%s columns', 1 ), 1 ),
				),
				'std' => '2',
				'classes' => 'inline',
			),
			'blog_breakpoint_2_end' => array(
				'type' => 'wrapper_end',
			),

			// Blog Responsive Breakpoint 3
			'blog_breakpoint_3_start' => array(
				'title' => ' ',
				'type' => 'wrapper_start',
				'classes' => 'title_left',
			),
			'blog_breakpoint_3_width' => array(
				'title' => __( 'Below screen width', 'us' ),
				'description' => __( 'show', 'us' ),
				'type' => 'slider',
				'min' => 300,
				'max' => 1400,
				'step' => 10,
				'std' => 600,
				'postfix' => 'px',
				'classes' => 'inline compact',
			),
			'blog_breakpoint_3_cols' => array(
				'type' => 'select',
				'options' => array(
					'6' => sprintf( us_translate_n( '%s column', '%s columns', 6 ), 6 ),
					'5' => sprintf( us_translate_n( '%s column', '%s columns', 5 ), 5 ),
					'4' => sprintf( us_translate_n( '%s column', '%s columns', 4 ), 4 ),
					'3' => sprintf( us_translate_n( '%s column', '%s columns', 3 ), 3 ),
					'2' => sprintf( us_translate_n( '%s column', '%s columns', 2 ), 2 ),
					'1' => sprintf( us_translate_n( '%s column', '%s columns', 1 ), 1 ),
				),
				'std' => '1',
				'classes' => 'inline',
			),
			'blog_breakpoint_3_end' => array(
				'type' => 'wrapper_end',
			),
		),
	),
	'woocommerce' => array(
		'title' => us_translate_x( 'Shop', 'Page title', 'woocommerce' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/cart',
		'place_if' => class_exists( 'woocommerce' ),
		'fields' => array(
			'shop_listing_style' => array(
				'title' => __( 'Products Grid Style', 'us' ),
				'std' => 'standard',
				'type' => 'radio',
				'options' => array(
					'standard' => __( 'Standard', 'us' ),
					'modern' => __( 'Modern', 'us' ),
					'trendy' => __( 'Trendy', 'us' ),
				),
			),
			'shop_columns' => array(
				'title' => __( 'Products Grid Columns', 'us' ),
				'std' => '3',
				'type' => 'radio',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
			),
			'product_related_qty' => array(
				'title' => __( 'Related Products Quantity', 'us' ),
				'description' => __( 'On Product pages and Cart page', 'us' ),
				'std' => '3',
				'type' => 'radio',
				'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
			),
			'shop_cart' => array(
				'title' => __( 'Cart Page Style', 'us' ),
				'std' => 'compact',
				'type' => 'radio',
				'options' => array(
					'standard' => __( 'Standard', 'us' ),
					'compact' => __( 'Compact', 'us' ),
				),
			),
			'shop_catalog' => array(
				'title' => __( 'Catalog Mode', 'us' ),
				'type' => 'switch',
				'text' => __( 'Disable ability to buy products via removing "Add to Cart" buttons', 'us' ),
				'std' => 0,
			),
		),
	),
	'advanced' => array(
		'title' => _x( 'Advanced', 'Advanced Settings', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/cog',
		'fields' => array(
			'h_advanced_1' => array(
				'title' => __( 'Theme Modules', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'enable_portfolio' => array(
				'type' => 'switch',
				'text' => sprintf( __( '%s module', 'us' ), __( 'Portfolio', 'us' ) ),
				'std' => 1,
				'classes' => 'width_full desc_2',
			),
			'enable_testimonials' => array(
				'type' => 'switch',
				'text' => sprintf( __( '%s module', 'us' ), __( 'Testimonials', 'us' ) ),
				'std' => 1,
				'classes' => 'width_full desc_2',
			),
			'og_enabled' => array(
				'type' => 'switch',
				'text' => __( 'Open Graph meta tags', 'us' ),
				'std' => 1,
				'classes' => 'width_full desc_2',
			),
			'schema_markup' => array(
				'type' => 'switch',
				'text' => __( 'Schema.org markup', 'us' ),
				'std' => 1,
				'classes' => 'width_full desc_2',
			),

			'h_advanced_2' => array(
				'title' => __( 'Website Performance', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'lazyload_fonts' => array(
				'type' => 'switch',
				'text' => __( 'Defer Google Fonts loading', 'us' ),
				'description' => __( 'When this option is ON, Google Fonts files will be loaded after page content.', 'us' ),
				'std' => 0,
				'classes' => 'width_full desc_2',
			),
			'jquery_footer' => array(
				'type' => 'switch',
				'text' => __( 'Move jQuery scripts to the footer', 'us' ),
				'description' => __( 'When this option is ON, jQuery library files will be loaded after page content.', 'us' ) . ' ' . __( 'This will improve pages loading speed.', 'us' ),
				'std' => 1,
				'classes' => 'width_full desc_2',
			),
			'disable_jquery_migrate' => array(
				'type' => 'switch',
				'text' => __( 'Disable jQuery migrate script', 'us' ),
				'description' => __( 'When this option is ON, "jquery-migrate.min.js" file won\'t be loaded in front-end.', 'us' ) . ' ' . __( 'This will improve pages loading speed.', 'us' ),
				'std' => 1,
				'classes' => 'width_full desc_2',
			),
			'ajax_load_js' => array(
				'type' => 'switch',
				'text' => __( 'Dynamically load theme JS components', 'us' ),
				'description' => __( 'When this option is ON, theme components JS files will be loaded dynamically without additional external requests.', 'us' ) . ' ' . __( 'This will improve pages loading speed.', 'us' ),
				'std' => 1,
				'classes' => 'width_full desc_2',
			),
			'disable_extra_vc' => array(
				'type' => 'switch',
				'text' => __( 'Disable extra features of WPBakery Page Builder', 'us' ),
				'description' => __( 'When this option is ON, original CSS and JS files of WPBakery Page Builder won\'t be loaded in front-end.', 'us' ) . ' ' . __( 'This will improve pages loading speed.', 'us' ),
				'std' => 1,
				'place_if' => class_exists( 'Vc_Manager' ),
				'classes' => 'width_full desc_2',
			),
			'optimize_assets' => array(
				'type' => 'switch',
				'text' => __( 'Optimize CSS size', 'us' ),
				'description' => __( 'When this option is ON, your site will load only one CSS file. You can disable unused components to reduce the file size.', 'us' ) . ' ' . __( 'This will improve pages loading speed.', 'us' ),
				'std' => 0,
				'classes' => 'width_full desc_2' . $optimize_assets_add_class,
			),
			'optimize_assets_alert' => array(
				'description' => __( 'Your uploads folder is not writable. Change your server permissions to use this option.', 'us' ),
				'type' => 'message',
				'classes' => 'width_full string' . $optimize_assets_alert_add_class,
			),
			'optimize_assets_start' => array(
				'type' => 'wrapper_start',
				'show_if' => array( 'optimize_assets', '=', TRUE ),
			),
			'assets' => array(
				'type' => 'check_table',
				'options' => $usof_assets,
				'std' => array_keys( $usof_assets ),
				'classes' => 'width_full',
			),
			'optimize_assets_end' => array(
				'type' => 'wrapper_end',
			),

			'h_advanced_3' => array(
				'title' => __( 'Custom Image Sizes', 'us' ),
				'type' => 'heading',
				'classes' => 'with_separator',
			),
			'img_size_info' => array(
				'description' => sprintf( __( 'Read %s how to use image sizes%s to improve pages loading speed.', 'us' ), '<a target="_blank" href="https://help.us-themes.com/impreza/general/images/">', '</a>' ),
				'type' => 'message',
				'classes' => 'width_full color_blue',
			),
			'img_size' => array(
				'type' => 'group',
				'classes' => 'compact',
				'params' => array(
					'width' => array(
						'title' => us_translate( 'Width' ),
						'type' => 'slider',
						'min' => 0,
						'max' => 1000,
						'std' => 600,
						'postfix' => 'px',
						'classes' => 'inline compact',
					),
					'height' => array(
						'title' => us_translate( 'Height' ),
						'type' => 'slider',
						'min' => 0,
						'max' => 1000,
						'std' => 400,
						'postfix' => 'px',
						'classes' => 'inline compact',
					),
					'crop' => array(
						'type' => 'checkboxes',
						'options' => array(
							'crop' => __( 'Crop to exact dimensions', 'us' ),
						),
						'std' => array(),
						'classes' => 'inline',
					),
				),
			),

		),
	),
	'code' => array(
		'title' => __( 'Custom Code', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/tag',
		'fields' => array(
			'custom_css' => array(
				'title' => __( 'Custom CSS', 'us' ),
				'description' => sprintf( __( 'CSS code from this field will overwrite theme styles. It will be located inside the %s tags just before the %s tag of every site page.', 'us' ), '<code>&lt;style&gt;&lt;/style&gt;</code>', '<code>&lt;/head&gt;</code>' ),
				'type' => 'css',
				'classes' => 'width_full desc_4',
			),
			'custom_html' => array(
				'title' => __( 'Custom HTML', 'us' ),
				'description' => sprintf( __( 'Use this field for Google Analytics code or other tracking code. If you paste custom JavaScript, use it inside the %s tags.<br><br>Content from this field will be located just before the %s tag of every site page.', 'us' ), '<code>&lt;script&gt;&lt;/script&gt;</code>', '<code>&lt;/body&gt;</code>' ),
				'type' => 'html',
				'classes' => 'width_full desc_4',
			),
		),
	),
	'manage' => array(
		'title' => __( 'Manage Options', 'us' ),
		'icon' => $us_template_directory_uri . '/framework/admin/img/usof/backups',
		'fields' => array(
			'of_reset' => array(
				'title' => __( 'Reset Theme Options', 'us' ),
				'type' => 'reset',
			),
			'of_backup' => array(
				'title' => __( 'Backup Theme Options', 'us' ),
				'type' => 'backup',
			),
			'of_transfer' => array(
				'title' => __( 'Transfer Theme Options', 'us' ),
				'type' => 'transfer',
				'description' => __( 'You can transfer the saved options data between different installations by copying the text inside the text box. To import data from another installation, replace the data in the text box with the one from another installation and click "Import Options".', 'us' ),
			),
		),
	),
);
