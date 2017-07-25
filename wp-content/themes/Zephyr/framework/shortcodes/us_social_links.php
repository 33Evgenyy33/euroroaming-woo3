<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_social_links
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $atts           array Shortcode attributes
 *
 * @param $atts           ['email'] string Email
 * @param $atts           ['*'] string Social Link
 * @param $atts           ['custom_link'] string Custom link
 * @param $atts           ['custom_title'] string Custom link title
 * @param $atts           ['custom_icon'] string Custom icon
 * @param $atts           ['custom_color'] string Custom color
 * @param $atts           ['style'] string Icons style: 'default' / 'solid_square' / 'outlined_square' / 'solid_circle' / 'outlined_circle'
 * @param $atts           ['color'] string Icons color: 'default' / 'text' / 'primary' / 'secondary'
 * @param $atts           ['size'] string Icons size
 * @param $atts           ['align'] string Icons alignment: 'left' / 'center' / 'right'
 * @param $atts           ['el_class'] string Extra class name
 */

$atts = us_shortcode_atts( $atts, 'us_social_links' );

$socials = us_config( 'social_links' );

$socials = array_merge( array( 'email' => 'Email' ), $socials );

$classes = '';

global $us_socials_index;
// Social links indexes indexes start from 1
$us_socials_index = isset( $us_socials_index ) ? ( $us_socials_index + 1 ) : 1;

$classes .= ' align_' . $atts['align'];

$style_translate = array(
	'solid_square' => 'solid',
	'outlined_square' => 'outlined',
	'solid_circle' => 'solid circle',
	'outlined_circle' => 'outlined circle',
);

if ( array_key_exists( $atts['style'], $style_translate ) ) {
	$atts['style'] = $style_translate[$atts['style']];
}

$classes .= ' style_' . $atts['style'];
$classes .= ' color_' . $atts['color'];

$classes .= ' index_' . $us_socials_index;
if ( $atts['el_class'] != '' ) {
	$classes .= ' ' . $atts['el_class'];
}

$socials_inline_css = '';
if ( ! empty( $atts['size'] ) ) {
	$socials_inline_css = ' style="font-size:' . $atts['size'] . ';"';
}

$output = '<div class="w-socials' . $classes . '"' . $socials_inline_css . '><div class="w-socials-list">';

foreach ( $socials as $social_key => $social ) {
	if ( empty( $atts[$social_key] ) ) {
		continue;
	}
	$social_url = $atts[$social_key];
	if ( $social_key == 'email' ) {
		if ( filter_var( $social_url, FILTER_VALIDATE_EMAIL ) ) {
			$social_url = 'mailto:' . $social_url;
		}
	} elseif ( $social_key == 'skype' ) {
		// Skype link may be some http(s): or skype: link. If protocol is not set, adding "skype:"
		if ( strpos( $social_url, ':' ) === FALSE ) {
			$social_url = 'skype:' . esc_attr( $social_url );
		}
	} else {
		$social_url = esc_url( $social_url );
	}
	$output .= '<div class="w-socials-item ' . $social_key . '">
				<a class="w-socials-item-link" target="_blank" href="' . $social_url . '">
					<span class="w-socials-item-link-hover"></span>
					<span class="w-socials-item-link-title">' . $social . '</span>
				</a>
				<div class="w-socials-item-popup">
					<span>' . $social . '</span>
				</div>
			</div>';
}

// Custom icon
$custom_css = '';
$atts['custom_icon'] = trim( $atts['custom_icon'] );
if ( ! empty( $atts['custom_icon'] ) AND ! empty( $atts['custom_link'] ) ) {
	$output .= '<div class="w-socials-item custom">';
	$output .= '<a class="w-socials-item-link" target="_blank" href="' . esc_url( $atts['custom_link'] ) . '"';
	if ( $atts['color'] == 'brand' ) {
		$output .= ' style="color: ' . $atts['custom_color'] . '"';
	}
	$output .= '>';
	$output .= '<span class="w-socials-item-link-hover" style="background-color: ' . $atts['custom_color'] . '"></span>';
	$output .= '<span class="w-socials-item-link-title">' . $atts['custom_title'] . '</span>';
	$output .= us_prepare_icon_tag( $atts['custom_icon'] );
	$output .= '</a>';
	$output .= '<div class="w-socials-item-popup"><span>' . $atts['custom_title'] . '</span></div>';
	$output .= '</div>';
}

$output .= '</div></div>';

echo $output;
