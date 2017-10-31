<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_person
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $atts           array Shortcode attributes
 *
 * @param $atts           ['image'] int Photo (from WP Media Library)
 * @param $atts           ['image_hover'] int Photo on hover (from WP Media Library)
 * @param $atts           ['name'] string Name
 * @param $atts           ['role'] string Role
 * @param $atts           ['link'] string Link in a serialized format: 'url:http%3A%2F%2Fwordpress.org|title:WP%20Website|target:_blank|rel:nofollow'
 * @param $atts           ['layout'] string Layout: 'simple' / 'simple_circle' / 'circle' / 'square' / 'card' / 'modern' / 'trendy'
 * @param $atts           ['effect'] string Photo Effect: 'none' / 'sepia' / 'bw' / 'faded' / 'colored'
 * @param $atts           ['email'] string Email
 * @param $atts           ['facebook'] string Facebook link
 * @param $atts           ['twitter'] string Twitter link
 * @param $atts           ['google_plus'] string Google+ link
 * @param $atts           ['linkedin'] string LinkedIn link
 * @param $atts           ['skype'] string Skype link
 * @param $atts           ['custom_icon'] string Custom icon
 * @param $atts           ['custom_link'] string Custom link
 * @param $atts           ['el_class'] string Extra class name
 */

$atts = us_shortcode_atts( $atts, 'us_person' );

// Generate schema.org markup
$schema_base = $schema_image = $schema_name = $schema_job = $schema_desc = '';
if ( us_get_option( 'schema_markup' ) ) {
	$schema_base = ' itemscope itemtype="https://schema.org/Person"';
	$schema_image = ' itemprop="image"';
	$schema_name = ' itemprop="name"';
	$schema_job = ' itemprop="jobTitle"';
	$schema_desc = ' itemprop="description"';
}

$classes = ' layout_' . $atts['layout'];
if ( $atts['effect'] != 'none' ) {
	$classes .= ' effect_' . $atts['effect'];
}

$img_html = '';
if ( is_numeric( $atts['image'] ) ) {
	$img = wp_get_attachment_image_src( intval( $atts['image'] ), 'tnail-1x1-small' );
	if ( $img !== FALSE ) {
		if ( preg_match( '~\.svg$~', $img[0] ) ) {
			$img_html = '<img src="' . $img[0] . '" alt="' . esc_attr( $atts['name'] ) . '"' . $schema_image . '>';
		} else {
			$img_html = '<img src="' . $img[0] . '" width="' . $img[1] . '" height="' . $img[2] . '" alt="' . esc_attr( $atts['name'] ) . '"' . $schema_image . '>';
		}
	}
} elseif ( ! empty( $atts['image'] ) ) {
	// Direct link to image is set in the shortcode attribute
	$img_html = '<img src="' . $atts['image'] . '" alt="' . esc_attr( $atts['name'] ) . '">';
}

if ( is_numeric( $atts['image_hover'] ) ) {
	$img_hover = wp_get_attachment_image_src( intval( $atts['image_hover'] ), 'tnail-1x1-small' );
	if ( $img_hover !== FALSE ) {
		$img_html .= '<div class="img_hover" style="background-image:url(' . $img_hover[0] . ')"></div>';
	}
} elseif ( ! empty( $atts['image_hover'] ) ) {
	// Direct link to image is set in the shortcode attribute
	$img_html .= '<div class="img_hover" style="background-image:url(' . $atts['image_hover'] . ')"></div>';
}

$links_html = '';
if ( ! empty( $atts['email'] ) ) {
	$links_html .= '<a class="w-person-links-item" href="mailto:' . $atts['email'] . '"><i class="fa fa-envelope"></i></a>';
}
if ( ! empty( $atts['facebook'] ) ) {
	$links_html .= '<a class="w-person-links-item" href="' . esc_url( $atts['facebook'] ) . '" target="_blank"><i class="fa fa-facebook"></i></a>';
}
if ( ! empty( $atts['twitter'] ) ) {
	$links_html .= '<a class="w-person-links-item" href="' . esc_url( $atts['twitter'] ) . '" target="_blank"><i class="fa fa-twitter"></i></a>';
}
if ( ! empty( $atts['google_plus'] ) ) {
	$links_html .= '<a class="w-person-links-item" href="' . esc_url( $atts['google_plus'] ) . '" target="_blank"><i class="fa fa-google-plus"></i></a>';
}
if ( ! empty( $atts['linkedin'] ) ) {
	$links_html .= '<a class="w-person-links-item" href="' . esc_url( $atts['linkedin'] ) . '" target="_blank"><i class="fa fa-linkedin"></i></a>';
}
if ( ! empty( $atts['skype'] ) ) {
	// Skype link may be some http(s): or skype: link. If protocol is not set, adding "skype:"
	$skype_url = $atts['skype'];
	if ( strpos( $skype_url, ':' ) === FALSE ) {
		$skype_url = 'skype:' . esc_attr( $skype_url );
	}
	$links_html .= '<a class="w-person-links-item" href="' . $skype_url . '"><i class="fa fa-skype"></i></a>';
}
$atts['custom_icon'] = trim( $atts['custom_icon'] );
if ( ! empty( $atts['custom_icon'] ) AND ! empty( $atts['custom_link'] ) ) {
	$links_html .= '<a class="w-person-links-item" href="' . esc_url( $atts['custom_link'] ) . '" target="_blank">' . us_prepare_icon_tag( $atts['custom_icon'] ) . '</a>';
}
if ( ! empty( $links_html ) ) {
	$classes .= ' with_socials';
	$links_html = '<div class="w-person-links"><div class="w-person-links-list">' . $links_html . '</div></div>';
}
if ( ! empty( $content ) ) {
	$classes .= ' with_desc';
}

$link_start = $link_end = '';
$link = us_vc_build_link( $atts['link'] );

if ( ! empty( $link['url'] ) ) {
	$link_target = ( $link['target'] == '_blank' ) ? ' target="_blank"' : '';
	$link_rel = ( $link['rel'] == 'nofollow' ) ? ' rel="nofollow"' : '';
	$link_title = empty( $link['title'] ) ? '' : ( ' title="' . esc_attr( $link['title'] ) . '"' );
	$link_start = '<a class="w-person-link" href="' . esc_url( $link['url'] ) . '"' . $link_target . $link_rel . $link_title . '>';
	$link_end = '</a>';
}

if ( ! empty( $atts['el_class'] ) ) {
	$classes .= ' ' . $atts['el_class'];
}

$output = '<div class="w-person' . $classes . '"' . $schema_base . '>';
$output .= '<div class="w-person-image">';
$output .= $link_start . $img_html . $link_end;
if ( in_array( $atts['layout'], array( 'square', 'circle' ) ) ) {
	$output .= $links_html;
}
$output .= '</div>';
$output .= '<div class="w-person-content">';
if ( ! empty( $atts['name'] ) ) {
	$output .= $link_start . '<h4 class="w-person-name"' . $schema_name . '><span>' . $atts['name'] . '</span></h4>' . $link_end;
}
if ( ! empty( $atts['role'] ) ) {
	$output .= '<div class="w-person-role"' . $schema_job . '>' . $atts['role'] . '</div>';
}
if ( $atts['layout'] == 'trendy' AND ( ! empty( $content ) OR ! empty( $links_html ) ) ) {
	$output .= '</div><div class="w-person-content-alt">' . $link_start . $link_end;
}
if ( ! in_array( $atts['layout'], array( 'square', 'circle' ) ) ) {
	$output .= $links_html;
}
if ( ! empty( $content ) ) {
	$output .= '<div class="w-person-description"' . $schema_desc . '>' . do_shortcode( $content ) . '</div>';
}
$output .= '</div></div>';

echo $output;
