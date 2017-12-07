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
 * @param $atts           ['items'] array Social Link
 * @param $atts           ['style'] string Icons style: 'default' / 'solid_square' / 'outlined_square' / 'solid_circle' / 'outlined_circle'
 * @param $atts           ['color'] string Icons color: 'default' / 'text' / 'primary' / 'secondary'
 * @param $atts           ['size'] string Icons size
 * @param $atts           ['align'] string Icons alignment: 'left' / 'center' / 'right'
 * @param $atts           ['el_class'] string Extra class name
 */

$atts = us_shortcode_atts( $atts, 'us_social_links' );

$style_translate = array(
	'solid_square' => 'solid',
	'outlined_square' => 'outlined',
	'solid_circle' => 'solid circle',
	'outlined_circle' => 'outlined circle',
);
if ( array_key_exists( $atts['style'], $style_translate ) ) {
	$atts['style'] = $style_translate[$atts['style']];
}

$classes = ' align_' . $atts['align'];
$classes .= ' style_' . $atts['style'];
$classes .= ' color_' . $atts['color'];
if ( $atts['el_class'] != '' ) {
	$classes .= ' ' . $atts['el_class'];
}

$socials_inline_css = '';
if ( ! empty( $atts['size'] ) ) {
	$socials_inline_css = ' style="font-size:' . $atts['size'] . ';"';
}

$output = '<div class="w-socials' . $classes . '"' . $socials_inline_css . '><div class="w-socials-list">';

if ( empty( $atts['items'] ) ) {
	$atts['items'] = array();
} else {
	$atts['items'] = json_decode( urldecode( $atts['items'] ), TRUE );
	if ( ! is_array( $atts['items'] ) ) {
		$atts['items'] = array();
	}
}

$socials_config = us_config( 'social_links' );

foreach ( $atts['items'] as $index => $item ) {
	$social_title = ( isset( $socials_config[$item['type']] ) ) ? $socials_config[$item['type']] : $item['type'];
	$social_url = ( isset( $item['url'] ) ) ? $item['url'] : '';
	$social_target = $social_icon = $social_custom_bg = $social_custom_color = '';
	// Custom type
	if ( $item['type'] == 'custom' ) {
		$social_title = ( isset( $item['title'] ) ) ? $item['title'] : '';
		$social_url = esc_url( $social_url );
		$social_target = ' target="_blank"';
		if ( isset( $item['icon'] ) ) {
			$item['icon'] = trim( $item['icon'] );
			$social_icon = us_prepare_icon_tag( $item['icon'] );
		}
		if ( isset( $item['color'] ) ) {
			$social_custom_bg = ' style="background-color: ' . $item['color'] . '"';
			$social_custom_color = ' style="color: ' . $item['color'] . '"';
		}
	// Email type
	} elseif ( $item['type'] == 'email' ) {
		if ( filter_var( $social_url, FILTER_VALIDATE_EMAIL ) ) {
			$social_url = 'mailto:' . $social_url;
		}
	// Skype type
	} elseif ( $item['type'] == 'skype' ) {
		if ( strpos( $social_url, ':' ) === FALSE ) {
			$social_url = 'skype:' . esc_attr( $social_url );
		}
	// All others types
	} else {
		$social_url = esc_url( $social_url );
		$social_target = ' target="_blank"';
	}

	$output .= '<div class="w-socials-item ' . $item['type'] . '">';
	$output .= '<a class="w-socials-item-link"' . $social_target . ' href="' . $social_url . '" aria-label="' . $social_title . '" rel="nofollow"';
	if ( $atts['color'] == 'brand' ) {
		$output .= $social_custom_color;
	}
	$output .= '>';
	$output .= '<span class="w-socials-item-link-hover"' . $social_custom_bg . '></span>';
	$output .= $social_icon;
	$output .= '</a>';
	if ( $social_title != '' ) {
		$output .= '<div class="w-socials-item-popup"><span>' . $social_title . '</span></div>';
	}
	$output .= '</div>';
}

$output .= '</div></div>';

echo $output;
