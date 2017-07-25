<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output socials element
 *
 * @var $color          string
 * @var $hover          string
 * @var $socials        string
 * @var $custom_icon    string
 * @var $custom_url     string
 * @var $custom_title     string
 * @var $custom_color   string
 * @var $size           int
 * @var $size_tablets   int
 * @var $size_mobiles   int
 * @var $design_options array
 * @var $id             string
 */

$socials = us_config( 'social_links' );

$output_inner = '';

foreach ( $socials as $social_key => $social ) {
	$social_url = $$social_key;
	if ( ! $social_url ) {
		continue;
	}

	if ( $social_key == 'skype' ) {
		// Skype link may be some http(s): or skype: link. If protocol is not set, adding "skype:"
		if ( strpos( $social_url, ':' ) === FALSE ) {
			$social_url = 'skype:' . esc_attr( $social_url );
		}
	} else {
		$social_url = esc_url( $social_url );
	}

	$output_inner .= '<div class="w-socials-item ' . $social_key . '">
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
if ( ! empty( $custom_icon ) AND ! empty( $custom_url ) ) {
	$output_inner .= '<div class="w-socials-item custom">';
	$output_inner .= '<a class="w-socials-item-link" target="_blank" href="' . esc_url( $custom_url ) . '">';
	$output_inner .= '<span class="w-socials-item-link-hover"></span>';
	$output_inner .= '<span class="w-socials-item-link-title">' . $custom_title . '</span>';
	$output_inner .= us_prepare_icon_tag( $custom_icon );
	$output_inner .= '</a></div>';
}

if ( ! empty( $output_inner ) ) {
	$classes = ' color_' . $color . ' hover_' . $hover;
	if ( isset( $design_options ) AND isset( $design_options['hide_for_sticky'] ) AND $design_options['hide_for_sticky'] ) {
		$classes .= ' hide-for-sticky';
	}
	if ( isset( $design_options ) AND isset( $design_options['hide_for_not-sticky'] ) AND $design_options['hide_for_not-sticky'] ) {
		$classes .= ' hide-for-not-sticky';
	}
	if ( isset( $id ) AND ! empty( $id ) ) {
		$classes .= ' ush_' . str_replace( ':', '_', $id );
	}
	$output = '<div class="w-socials' . $classes . '"><div class="w-socials-list">' . $output_inner . '</div></div>';

	echo $output;
}
