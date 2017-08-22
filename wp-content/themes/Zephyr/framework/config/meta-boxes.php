<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Meta Boxes
 *
 * @filter us_config_meta-boxes
 */

$custom_post_types = us_get_option( 'custom_post_types_support' );

// Getting Sidebars
global $wp_registered_sidebars;
$sidebars_options = array();
if ( is_array( $wp_registered_sidebars ) && ! empty( $wp_registered_sidebars ) ) {
	foreach ( $wp_registered_sidebars as $sidebar ) {
		if ( $sidebar['id'] == 'default_sidebar' ) {
			// Add default sidebar to the beginning
			$sidebars_options = array_merge( array( $sidebar['id'] => $sidebar['name'] ), $sidebars_options );
		} else {
			$sidebars_options[$sidebar['id']] = $sidebar['name'];
		}
	}
}

// Getting Footers
us_open_wp_query_context();
$footer_templates_query = new WP_Query(
	array(
		'post_type' => 'us_footer',
		'posts_per_page' => '-1',
	)
);
$footer_templates = array();
while ( $footer_templates_query->have_posts() ) {
	$footer_templates_query->the_post();
	global $post;

	$footer_templates[$post->post_name] = get_the_title();
}
us_close_wp_query_context();

return array(
	// Blog Post settings
	array(
		'id' => 'us_post_settings',
		'title' => __( 'Featured Image Layout', 'us' ),
		'post_types' => array( 'post' ),
		'context' => 'side',
		'priority' => 'low',
		'fields' => array(
			'us_post_preview_layout' => array(
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'basic' => __( 'Standard', 'us' ),
					'modern' => __( 'Modern', 'us' ),
					'trendy' => __( 'Trendy', 'us' ),
					'none' => __( 'No Preview', 'us' ),
				),
				'std' => '',
			),
		),
	),
	// Page settings
	array(
		'id' => 'us_page_settings',
		'title' => __( 'Page Options', 'us' ),
		'post_types' => array_merge( array( 'post', 'page', 'us_portfolio', 'product' ), $custom_post_types ),
		'context' => 'side',
		'priority' => 'low',
		'fields' => array(
		
			// Header options
			'us_title_1' => array(
				'title' => _x( 'Header', 'site top area', 'us' ),
				'type' => 'heading',
			),
			'us_header' => array(
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'custom' => __( 'Custom header on this page', 'us' ),
					'hide' => __( 'Remove header on this page', 'us' ),
				),
				'std' => '',
			),
			'us_header_sticky_pos' => array(
				'title' => __( 'Sticky Header Initial Position', 'us' ),
				'type' => 'select',
				'options' => array(
					'' => __( 'At the Top of this page', 'us' ),
					'bottom' => __( 'At the Bottom of the first content row', 'us' ),
					'above' => __( 'Above the first content row', 'us' ),
					'below' => __( 'Below the first content row', 'us' ),
				),
				'std' => '',
				'show_if' => array( 'us_header', '=', 'custom' ),
			),

			// Titlebar options
			'us_title_2' => array(
				'title' => __( 'Title Bar', 'us' ),
				'type' => 'heading',
			),
			'us_titlebar' => array(
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'custom' => __( 'Custom Title Bar on this page', 'us' ),
					'hide' => __( 'Remove Title Bar on this page', 'us' ),
				),
				'std' => '',
			),
			'us_titlebar_subtitle' => array(
				'title' => us_translate( 'Description' ),
				'description' => __( 'Appears next to the page title', 'us' ),
				'type' => 'text',
				'std' => '',
				'show_if' => array( 'us_titlebar', '=', 'custom' ),
			),
			'us_titlebar_size' => array(
				'title' => __( 'Title Bar Size', 'us' ),
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'small' => __( 'Small', 'us' ),
					'medium' => __( 'Medium', 'us' ),
					'large' => __( 'Large', 'us' ),
					'huge' => __( 'Huge', 'us' ),
				),
				'std' => '',
				'show_if' => array( 'us_titlebar', '=', 'custom' ),
			),
			'us_titlebar_color' => array(
				'title' => __( 'Title Bar Color Style', 'us' ),
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'default' => __( 'Content colors', 'us' ),
					'alternate' => __( 'Alternate Content colors', 'us' ),
					'primary' => __( 'Primary bg & White text', 'us' ),
					'secondary' => __( 'Secondary bg & White text', 'us' ),
					'footer-top' => __( 'Top Footer colors', 'us' ),
					'footer-bottom' => __( 'Bottom Footer colors', 'us' ),
				),
				'std' => '',
				'show_if' => array( 'us_titlebar', '=', 'custom' ),
			),
			'us_titlebar_breadcrumbs' => array(
				'title' => __( 'Breadcrumbs', 'us' ),
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'show' => us_translate( 'Show' ),
					'hide' => us_translate( 'Hide' ),
				),
				'std' => '',
				'show_if' => array( 'us_titlebar', '=', 'custom' ),
			),
			'us_titlebar_image' => array(
				'title' => __( 'Background Image', 'us' ),
				'type' => 'upload',
				'extension' => 'png,jpg,jpeg,gif,svg',
				'show_if' => array( 'us_titlebar', '=', 'custom' ),
			),
			'us_titlebar_bg_size' => array(
				'title' => __( 'Background Image Size', 'us' ),
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'cover' => __( 'Fill Area', 'us' ),
					'contain' => __( 'Fit to Area', 'us' ),
					'initial' => __( 'Initial', 'us' ),
				),
				'std' => '',
				'show_if' => array(
					array( 'us_titlebar', '=', 'custom' ),
					'and',
					array( 'us_titlebar_image', '!=', '' ),
				),
			),
			'us_titlebar_bg_repeat' => array(
				'title' => __( 'Background Image Repeat', 'us' ),
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'repeat' => __( 'Repeat', 'us' ),
					'repeat-x' => __( 'Horizontally', 'us' ),
					'repeat-y' => __( 'Vertically', 'us' ),
					'no-repeat' => us_translate( 'None' ),
				),
				'std' => '',
				'show_if' => array(
					array( 'us_titlebar', '=', 'custom' ),
					'and',
					array( 'us_titlebar_image', '!=', '' ),
				),
			),
			'us_titlebar_bg_position' => array(
				'title' => __( 'Background Image Position', 'us' ),
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'top left' => us_translate( 'Top Left' ),
					'top center' => us_translate( 'Top Center' ),
					'top right' => us_translate( 'Top Right' ),
					'center left' => us_translate( 'Center Left' ),
					'center center' => us_translate( 'Center' ),
					'center right' => us_translate( 'Center Right' ),
					'bottom left' => us_translate( 'Bottom Left' ),
					'bottom center' => us_translate( 'Bottom Center' ),
					'bottom right' => us_translate( 'Bottom Right' ),
				),
				'std' => '',
				'show_if' => array(
					array( 'us_titlebar', '=', 'custom' ),
					'and',
					array( 'us_titlebar_image', '!=', '' ),
				),
			),
			'us_titlebar_bg_parallax' => array(
				'title' => __( 'Parallax Effect', 'us' ),
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'none' => us_translate( 'None' ),
					'vertical' => __( 'Vertical Parallax', 'us' ),
					'vertical_reversed' => __( 'Vertical Reversed Parallax', 'us' ),
					'horizontal' => __( 'Horizontal Parallax', 'us' ),
					'still' => __( 'Fixed', 'us' ),
				),
				'std' => '',
				'show_if' => array(
					array( 'us_titlebar', '=', 'custom' ),
					'and',
					array( 'us_titlebar_image', '!=', '' ),
				),
			),
			'us_titlebar_overlay_color' => array(
				'title' => __( 'Overlay Color', 'us' ),
				'type' => 'color',
				'show_if' => array(
					array( 'us_titlebar', '=', 'custom' ),
					'and',
					array( 'us_titlebar_image', '!=', '' ),
				),
			),

			// Sidebar options
			'us_title_3' => array(
				'title' => __( 'Sidebar', 'us' ),
				'type' => 'heading',
			),
			'us_sidebar' => array(
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'custom' => __( 'Custom sidebar on this page', 'us' ),
					'hide' => __( 'Remove sidebar on this page', 'us' ),
				),
				'std' => '',
			),
			'us_sidebar_id' => array(
				'description' => sprintf( __( 'You can edit selected sidebar or create a new one on the %s page', 'us' ), '<a href="' . admin_url( 'widgets.php' ) . '" target="_blank">' . us_translate( 'Widgets' ) . '</a>' ),
				'type' => 'select',
				'options' => $sidebars_options,
				'std' => 'default_sidebar',
				'show_if' => array( 'us_sidebar', '=', 'custom' ),
			),
			'us_sidebar_pos' => array(
				'title' => __( 'Sidebar Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'right',
				'classes' => 'width_full',
				'show_if' => array( 'us_sidebar', '=', 'custom' ),
			),

			// Footer options
			'us_title_4' => array(
				'title' => __( 'Footer', 'us' ),
				'type' => 'heading',
			),
			'us_footer' => array(
				'type' => 'select',
				'options' => array(
					'' => __( 'Default (from Theme Options)', 'us' ),
					'custom' => __( 'Custom footer on this page', 'us' ),
					'hide' => __( 'Remove footer on this page', 'us' ),
				),
				'std' => '',
			),
			'us_footer_id' => array(
				'description' => sprintf( __( 'You can edit selected footer or create a new one on the %s page', 'us' ), '<a href="' . admin_url() . 'edit.php?post_type=us_footer" target="_blank">' . __( 'Footers', 'us' ) . '</a>' ),
				'type' => 'select',
				'options' => $footer_templates,
				'std' => 'default-footer',
				'show_if' => array( 'us_footer', '=', 'custom' ),
			),
		),
	),
	// Portfolio Page settings
	array(
		'id' => 'us_portfolio_settings',
		'title' => __( 'Portfolio Tile Options', 'us' ),
		'post_types' => array( 'us_portfolio' ),
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(
			'us_tile_description' => array(
				'title' => us_translate( 'Description' ),
				'description' => __( 'This text will be shown in the relevant tile of Portfolio element', 'us' ),
				'type' => 'text',
				'std' => '',
			),
			'us_tile_bg_color' => array(
				'title' => __( 'Custom Background Color', 'us' ),
				'type' => 'color',
			),
			'us_tile_text_color' => array(
				'title' => __( 'Custom Text Color', 'us' ),
				'type' => 'color',
			),
			'us_tile_size' => array(
				'title' => __( 'Aspect Ratio', 'us' ),
				'type' => 'radio',
				'options' => array(
					'1x1' => '1x1',
					'2x1' => '2x1',
					'1x2' => '1x2',
					'2x2' => '2x2',
				),
				'std' => '1x1',
			),
			'us_tile_link' => array(
				'title' => __( 'Custom Link', 'us' ),
				'type' => 'link',
				'placeholder' => us_translate( 'Enter the URL' ),
				'std' => '',
			),
			'us_tile_additional_image' => array(
				'title' => __( 'Additional Tile Image on hover', 'us' ),
				'type' => 'upload',
				'extension' => 'png,jpg,jpeg,gif,svg',
			),
		),
	),
	// Testimonials settings
	array(
		'id' => 'us_testimonials_settings',
		'title' => __( 'More Options', 'us' ),
		'post_types' => array( 'us_testimonial' ),
		'context' => 'normal',
		'priority' => 'high',
		'fields' => array(
			'us_testimonial_author' => array(
				'title' => __( 'Author Name', 'us' ),
				'type' => 'text',
				'std' => 'John Doe',
			),
			'us_testimonial_role' => array(
				'title' => __( 'Author Role', 'us' ),
				'type' => 'text',
				'std' => '',
			),
			'us_testimonial_link' => array(
				'title' => __( 'Author Link', 'us' ),
				'type' => 'link',
				'placeholder' => us_translate( 'Enter the URL' ),
				'std' => '',
			),
		),
	),
);
