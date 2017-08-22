<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output dropdown element
 *
 * @var $source            string Dropdown source: 'own' / 'wpml' / 'polylang' / 'qtranslate'
 * @var $wpml_switcher     string
 * @var $text_size         int
 * @var $link_title        string
 * @var $link_qty          int
 * @var $link_1_label      string
 * @var $link_1_url        string
 * @var $link_2_label      string
 * @var $link_2_url        string
 * @var $link_3_label      string
 * @var $link_3_url        string
 * @var $link_4_label      string
 * @var $link_4_url        string
 * @var $link_5_label      string
 * @var $link_5_url        string
 * @var $link_6_label      string
 * @var $link_6_url        string
 * @var $link_7_label      string
 * @var $link_7_url        string
 * @var $link_8_label      string
 * @var $link_8_url        string
 * @var $link_9_label      string
 * @var $link_9_url        string
 * @var $design_options    array
 * @var $id                string
 */

$classes = ' source_' . $source;
if ( isset( $design_options ) AND isset( $design_options['hide_for_sticky'] ) AND $design_options['hide_for_sticky'] ) {
	$classes .= ' hide-for-sticky';
}
if ( isset( $design_options ) AND isset( $design_options['hide_for_not-sticky'] ) AND $design_options['hide_for_not-sticky'] ) {
	$classes .= ' hide-for-not-sticky';
}
foreach ( array( 'default', 'tablets', 'mobiles' ) as $state ) {
	if ( ! us_is_header_elm_shown( $id, $state ) ) {
		$classes .= ' hidden_for_' . $state;
	}
}
if ( isset( $id ) AND ! empty( $id ) ) {
	$classes .= ' ush_' . str_replace( ':', '_', $id );
}

// Common data format
$data = array(
	'current' => array(),
	'list' => array(),
);
if ( $source == 'own' ) {
	$link_qty = intval( $link_qty );
	$data['current']['title'] = $link_title;
	for ( $i = 1; $i <= $link_qty; $i ++ ) {
		$label_var = 'link_' . $i . '_label';
		$url_var = 'link_' . $i . '_url';
		$link_atts = usof_get_link_atts( $$url_var );
		if ( ! isset( $link_atts['href'] ) ) {
			$link_atts['href'] = '';
		}
		$data['list'][] = array(
			'title' => $$label_var,
			'url' => ( substr( $link_atts['href'], 0, 4 ) == 'http' || substr( $link_atts['href'], 0, 1 ) == '/' || strpos( $link_atts['href'], '#' ) !== FALSE || strpos( $link_atts['href'], '?' ) !== FALSE ) ? $link_atts['href'] : ( '//' . $link_atts['href'] ),
			'target' => ( isset( $link_atts['target'] ) ) ? $link_atts['target'] : NULL,
		);
	}
} elseif ( $source == 'wpml' AND function_exists( 'icl_get_languages' ) ) {

	$languages = apply_filters( 'wpml_active_languages', NULL, array( 'skip_missing' => 0 ) );
	foreach ( $languages as $language ) {
		$data_language = array();
		$data_language['title'] = '';
		if ( in_array( 'native_lang', $wpml_switcher ) ) {
			$data_language['title'] = $language['native_name'];
			if ( in_array( 'display_lang', $wpml_switcher ) AND ( $language['native_name'] != $language['translated_name'] ) ) {
				$data_language['title'] .= ' (' . $language['translated_name'] . ')';
			}
		} elseif ( in_array( 'display_lang', $wpml_switcher ) ) {
			$data_language['title'] = $language['translated_name'];
		}
		if ( in_array( 'flag', $wpml_switcher ) ) {
			$data_language['flag'] = $language['country_flag_url'];
			$data_language['code'] = $language['language_code'];
		}
		if ( $language['active'] ) {
			$data['current'] = $data_language;
		} else {
			$data_language['url'] = $language['url'];
			$data['list'][] = $data_language;
		}
	}
} elseif ( $source == 'polylang' AND function_exists( 'pll_the_languages' ) ) {
	$pll_langs = pll_the_languages( array( 'raw' => 1 ) );
	foreach ( $pll_langs as $pll_lang ) {
		$data_language = array(
			'title' => $pll_lang['name'],
			'flag' => $pll_lang['flag'],
			'code' => $pll_lang['slug'],
		);
		if ( $pll_lang['current_lang'] ) {
			$data['current'] = $data_language;
		} else {
			$data_language['url'] = $pll_lang['url'];
			$data['list'][] = $data_language;
		}
	}
} elseif ( $source == 'qtranslate' AND function_exists( 'qtranxf_getSortedLanguages' ) ) {
	global $q_config;
	if ( ! isset( $q_config ) OR ! is_array( $q_config ) ) {
		return;
	}
	$q_url = is_404() ? get_option( 'home' ) : '';
	foreach ( qtranxf_getSortedLanguages() as $q_lang_code ) {
		$data_language = array(
			'title' => $q_config['language_name'][$q_lang_code],
			'title_class' => 'qtranxs_flag_' . $q_lang_code,
		);
		if ( $q_lang_code == $q_config['language'] ) {
			$data['current'] = $data_language;
		} else {
			$data_language['url'] = qtranxf_convertURL( $q_url, $q_lang_code, FALSE, TRUE );
			$data['list'][] = $data_language;
		}
	}
}

if ( count( $data['list'] ) == 0 ) {
	return;
}
$output = '<div class="w-dropdown' . $classes . '"><div class="w-dropdown-h">';
$output .= '<div class="w-dropdown-list">';
foreach ( $data['list'] as $lang ) {
	$output .= '<a class="w-dropdown-item" href="' . esc_attr( $lang['url'] ) . '"';
	$output .= ( ! empty( $lang['target'] ) ) ? ' target="' . esc_attr( $lang['target'] ) . '"' : '';
	$output .= '>';
	if ( isset( $lang['flag'] ) AND ! empty( $lang['flag'] ) ) {
		$output .= '<img src="' . $lang['flag'] . '" alt="' . $lang['code'] . '" />';
	}
	$output .= '<span class="w-dropdown-item-title';
	if ( isset( $lang['title_class'] ) AND ! empty( $lang['title_class'] ) ) {
		$output .= ' ' . esc_attr( $lang['title_class'] );
	}
	$output .= '">' . $lang['title'] . '</span>';
	$output .= '</a>';
}
$output .= '</div>';
if ( isset( $data['current'] ) AND ! empty( $data['current'] ) ) {
	$output .= '<div class="w-dropdown-current"><a class="w-dropdown-item" href="javascript:void(0)">';
	if ( isset( $data['current']['flag'] ) AND ! empty( $data['current']['flag'] ) ) {
		$output .= '<img src="' . $data['current']['flag'] . '" alt="' . $data['current']['code'] . '" />';
	}
	$output .= '<span class="w-dropdown-item-title';
	if ( isset( $data['current']['title_class'] ) AND ! empty( $data['current']['title_class'] ) ) {
		$output .= ' ' . esc_attr( $data['current']['title_class'] );
	}
	$output .= '">' . $data['current']['title'] . '</span>';
	$output .= '</a></div>';
}
$output .= '</div></div>';
echo $output;
