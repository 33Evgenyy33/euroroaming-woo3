<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_single_image
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $atts           array Shortcode attributes
 *
 * @param $atts           ['image'] int WordPress media library image ID
 * @param $atts           ['size'] string Image size: 'large' / 'medium' / 'thumbnail' / 'full'
 * @param $atts           ['align'] string Image alignment: '' / 'left' / 'center' / 'right'
 * @param $atts           ['style'] string Image Style
 * @param $atts           ['meta'] bool Show items titles and description?
 * @param $atts           ['meta_style'] string Title and Description Style: 'simple' / 'modern'
 * @param $atts           ['onclick'] string On click action: '' / 'lightbox' / 'custom_link'
 * @param $atts           ['link'] string Image link in a serialized format: 'url:http%3A%2F%2Fwordpress.org|title:WP%20Website|target:_blank|rel:nofollow'
 * @param $atts           ['animate'] string Animation type: '' / 'fade' / 'afc' / 'afl' / 'afr' / 'afb' / 'aft' / 'hfc' / 'wfc'
 * @param $atts           ['animate_delay'] float Animation delay (in seconds)
 * @param $atts           ['el_class'] string Extra class name
 * @param $atts           ['css'] string Custom CSS
 */

$atts = us_shortcode_atts( $atts, 'us_single_image' );

$classes = $image_shadow = '';

// Link attributes' values
$link = array();

$img_id = intval( $atts['image'] );

if ( $img_id AND ( $image_html = wp_get_attachment_image( $img_id, $atts['size'] ) ) ) {
	// Check if the image is in SVG format and remove width and height for it in this case
	$img_src = wp_get_attachment_image_src( $img_id, $atts['size'] );
	$img_src_full = wp_get_attachment_image_src( $img_id, 'full' );
	if ( preg_match( '~\.svg$~', $img_src_full[0] ) ) {
		$image_html = preg_replace( '~(width|height)="\d+"~', '', $image_html );
	}

	// We got image
	if ( $atts['onclick'] == 'lightbox' ) {
		$link['url'] = wp_get_attachment_image_src( $img_id, 'full' );
		$link['url'] = ( $link['url'] ) ? $link['url'][0] : $image[0];
		$link['ref'] = 'magnificPopup';
	}

	if ( $atts['meta'] ) {
		$attachment = get_post( $img_id );
		$title = trim( strip_tags( get_post_meta( $img_id, '_wp_attachment_image_alt', TRUE ) ) );
		if ( empty( $title ) ) {
			// If not, Use the Caption
			$title = trim( strip_tags( $attachment->post_excerpt ) );
		}
		if ( empty( $title ) ) {
			// Finally, use the title
			$title = trim( strip_tags( $attachment->post_title ) );
		}
		$image_html .= '<div class="w-image-meta">';
		$image_html .= '<div class="w-image-title">' . $title . '</div>';
		$image_html .= ( ! empty( $attachment->post_content ) ) ? '<div class="w-image-description">' . $attachment->post_content . '</div>' : '';
		$image_html .= '</div>';
	}
	
	if ( $atts['style'] == 'shadow-2' ) {
		$image_shadow = '<div class="w-image-shadow" style="background-image:url(' . $img_src[0] . ');"></div>';
	}
} else {
	// In case of any image issue using placeholder so admin could understand it quickly
	// TODO Move placeholder URL to some config
	global $us_template_directory_uri;
	$placeholder_url = $us_template_directory_uri . '/framework/img/us-placeholder-square.jpg';
	$image_html = '<img src="' . $placeholder_url . '" width="600" height="600" alt="">';

	if ( $atts['meta'] ) {
		$image_html .= '<div class="w-image-meta">';
		$image_html .= '<div class="w-image-title">' . us_translate( 'Title' ) . '</div>';
		$image_html .= '<div class="w-image-description">' . us_translate( 'Description' ) . '</div>';
		$image_html .= '</div>';
	}
	
	if ( $atts['style'] == 'shadow-2' ) {
		$image_shadow = '<div class="w-image-shadow" style="background-image:url(' . $placeholder_url . ');"></div>';
	}
}

if ( $atts['onclick'] == 'custom_link' AND ! empty( $atts['link'] ) ) {
	// Passing params from vc_link field type
	$link = array_merge( $link, us_vc_build_link( $atts['link'] ) );
}

if ( ! empty( $link['url'] ) ) {
	$link_html = '<a href="' . esc_url( $link['url'] ) . '"';
	unset( $link['url'] );
	foreach ( $link as $key => $value ) {
		$link_html .= ' ' . $key . '="' . esc_attr( $value ) . '"';
	}
	if ( $atts['meta'] ) {
		$link_html .= ' title="' . esc_attr( $title ) . '"';
	}
	$link_html .= '>';
	$image_html = $link_html . $image_html . '</a>';
}

if ( ! empty( $atts['align'] ) ) {
	$classes .= ' align_' . $atts['align'];
}

if ( ! empty( $atts['style'] ) ) {
	$classes .= ' style_' . $atts['style'];
}

if ( ! empty( $atts['meta_style'] ) AND ( $atts['meta'] ) ) {
	$classes .= ' meta_' . $atts['meta_style'];
}

if ( ! empty( $atts['animate'] ) ) {
	$classes .= ' animate_' . $atts['animate'];
	if ( ! empty( $atts['animate_delay'] ) ) {
		$atts['animate_delay'] = floatval( $atts['animate_delay'] );
		$classes .= ' d' . intval( $atts['animate_delay'] * 5 );
	}
}

if ( ! empty( $atts['css'] ) AND function_exists( 'vc_shortcode_custom_css_class' ) ) {
	$classes .= ' ' . vc_shortcode_custom_css_class( $atts['css'] );
}

if ( $atts['el_class'] != '' ) {
	$classes .= ' ' . $atts['el_class'];
}

$output = '<div class="w-image' . $classes . '">';
$output .= '<div class="w-image-h">' . $image_shadow . $image_html . '</div>';
$output .= '</div>';

echo $output;
