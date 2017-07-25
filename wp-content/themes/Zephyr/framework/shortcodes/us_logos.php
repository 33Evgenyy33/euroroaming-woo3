<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_logos
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $atts           array Shortcode attributes
 *
 * @param $atts           ['items'] array of Logos
 * @param $atts           ['type'] string layout type: 'grid' / 'carousel'
 * @param $atts           ['columns'] int Columns quantity
 * @param $atts           ['with_indents'] bool Add indents between items?
 * @param $atts           ['style'] string Hover style: '1' / '2'
 * @param $atts           ['orderby'] string Items order: '' / 'rand'
 * @param $atts           ['el_class'] string Extra class name
 * @param $atts           ['carousel_arrows'] bool used in Carousel type
 * @param $atts           ['carousel_dots'] bool used in Carousel type
 * @param $atts           ['carousel_center'] bool used in Carousel type
 * @param $atts           ['carousel_autoplay'] bool used in Carousel type
 * @param $atts           ['carousel_interval'] int used in Carousel type
 * @param $atts           ['carousel_slideby'] bool used in Carousel type
 */

$atts = us_shortcode_atts( $atts, 'us_logos' );

$classes = $list_classes = '';

$atts['columns'] = intval( $atts['columns'] );
if ( $atts['columns'] < 1 OR $atts['columns'] > 8 ) {
	$atts['columns'] = 5;
}

$classes .= ' style_' . $atts['style'];

if ( $atts['with_indents'] ) {
	$classes .= ' with_indents';
}

if ( isset( $atts['type'] ) ) {
	$classes .= ' type_' . $atts['type'];
}
if ( $atts['type'] == 'carousel' ) {
	$list_classes .= ' owl-carousel';
}

if ( $atts['columns'] != 1 ) {
	$classes .= ' cols_' . $atts['columns'];
}

if ( $atts['el_class'] != '' ) {
	$classes .= ' ' . $atts['el_class'];
}

// We need owl script for this
if ( us_get_option( 'ajax_load_js', 0 ) == 0 ) {
	wp_enqueue_script( 'us-owl' );
}

$output = '<div class="w-logos' . $classes . '"><div class="w-logos-list' . $list_classes . '"';
if ( isset( $atts['type'] ) AND $atts['type'] == 'carousel' ) {
	$output .= ' data-items="' . $atts['columns'] . '"';
	$output .= ' data-nav="' . intval( ! ! $atts['carousel_arrows'] ) . '"';
	$output .= ' data-dots="' . intval( ! ! $atts['carousel_dots'] ) . '"';
	$output .= ' data-center="' . intval( ! ! $atts['carousel_center'] ) . '"';
	$output .= ' data-autoplay="' . intval( ! ! $atts['carousel_autoplay'] ) . '"';
	$output .= ' data-timeout="' . intval( $atts['carousel_interval'] * 1000 ) . '"';
	if ( $atts['carousel_slideby'] ) {
		$output .= ' data-slideby="page"';
	} else {
		$output .= ' data-slideby="1"';
	}
}
$output .= '>';

if ( empty( $atts['items'] ) ) {
	$atts['items'] = array();
} else {
	$atts['items'] = json_decode( urldecode( $atts['items'] ), TRUE );
	if ( ! is_array( $atts['items'] ) ) {
		$atts['items'] = array();
	}
}
if ( $atts['orderby'] == 'rand' ) {
	shuffle( $atts['items'] );
}

foreach ( $atts['items'] as $index => $item ) {
	$item['image'] = ( isset( $item['image'] ) ) ? $item['image'] : '';
	$item['link'] = ( isset( $item['link'] ) ) ? $item['link'] : '';

	$img_id = intval( $item['image'] );

	if ( $img_id AND ( $image_html = wp_get_attachment_image( $img_id, 'medium' ) ) ) {
		// We got image
	} else {
		// In case of any image issue using placeholder so admin could understand it quickly
		// TODO Move placeholder URL to some config
		global $us_template_directory_uri;
		$placeholder_url = $us_template_directory_uri . '/framework/img/us-placeholder-square.jpg';
		$image_html = '<img src="' . $placeholder_url . '" width="600" height="600" alt="">';
	}

	if ( $item['link'] != '' ) {
		$link = us_vc_build_link( $item['link'] );
		$link_target = ( $link['target'] == '_blank' ) ? ' target="_blank"' : '';
		$link_rel = ( $link['rel'] == 'nofollow' ) ? ' rel="nofollow"' : '';
		$link_title = empty( $link['title'] ) ? '' : ( ' title="' . esc_attr( $link['title'] ) . '"' );
		$output .= '<a class="w-logos-item" href="' . esc_url( $link['url'] ) . '"' . $link_target . $link_rel . $link_title . '>';
		$output .= $image_html . '</a>';
	} else {
		$output .= '<div class="w-logos-item">' . $image_html . '</div>';
	}
}
$output .= '</div>';

if ( $atts['type'] == 'carousel' ) {
	$preloader_type = us_get_option( 'preloader' );
	if ( ! in_array( $preloader_type, us_get_preloader_numeric_types() ) ) {
		$preloader_type = 1;
	}
	$output .= '<div class="g-preloader type_' . $preloader_type . '"><div></div></div>';
}

$output .= '</div>';

echo $output;
