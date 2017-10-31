<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme's Theme Options config
 *
 * @var $config array Framework-based theme options config
 *
 * @return array Changed config
 */

// Material design menu dropdown effect default
unset( $config['general']['fields']['rounded_corners'] );
unset( $config['general']['fields']['links_underline'] );

unset( $config['colors']['fields']['color_menu_active_bg'] );
unset( $config['colors']['fields']['change_alt_content_colors_start'] );
unset( $config['colors']['fields']['h_colors_4'] );
unset( $config['colors']['fields']['color_alt_content_bg'] );
unset( $config['colors']['fields']['color_alt_content_bg_alt'] );
unset( $config['colors']['fields']['color_alt_content_border'] );
unset( $config['colors']['fields']['color_alt_content_heading'] );
unset( $config['colors']['fields']['color_alt_content_text'] );
unset( $config['colors']['fields']['color_alt_content_link'] );
unset( $config['colors']['fields']['color_alt_content_link_hover'] );
unset( $config['colors']['fields']['color_alt_content_primary'] );
unset( $config['colors']['fields']['color_alt_content_secondary'] );
unset( $config['colors']['fields']['color_alt_content_faded'] );
unset( $config['colors']['fields']['change_alt_content_colors_end'] );

unset( $config['header']['fields']['header_socials_custom_color'] );
unset( $config['sidebar']['fields']['forum_sidebar'] );
unset( $config['sidebar']['fields']['forum_sidebar_id'] );
unset( $config['buttons'] );

unset( $config['blog']['fields']['post_nav_layout'] );
unset( $config['blog']['fields']['post_sharing_type']['options']['outlined'] );
unset( $config['blog']['fields']['blog_layout']['options']['cards'] );
unset( $config['blog']['fields']['blog_layout']['options']['flat'] );
unset( $config['blog']['fields']['archive_layout']['options']['cards'] );
unset( $config['blog']['fields']['archive_layout']['options']['flat'] );
unset( $config['blog']['fields']['search_layout']['options']['cards'] );
unset( $config['blog']['fields']['search_layout']['options']['flat'] );
unset( $config['blog']['fields']['read_more_btn_style'] );
unset( $config['blog']['fields']['read_more_btn_color'] );
unset( $config['blog']['fields']['read_more_btn_size'] );

$config['blog']['fields']['blog_layout']['options'] = us_array_merge_insert(
	$config['blog']['fields']['blog_layout']['options'], array(
	'flat' => 'Cards',
), 'after', 'classic'
);

$config['blog']['fields']['archive_layout']['options'] = us_array_merge_insert(
	$config['blog']['fields']['archive_layout']['options'], array(
	'flat' => 'Cards',
), 'after', 'classic'
);

$config['blog']['fields']['search_layout']['options'] = us_array_merge_insert(
	$config['blog']['fields']['search_layout']['options'], array(
	'flat' => 'Cards',
), 'after', 'classic'
);

unset( $config['woocommerce']['fields']['shop_listing_style']['options']['trendy'] );

unset( $config['advanced']['fields']['optimize_assets_start'] );
unset( $config['advanced']['fields']['assets'] );
unset( $config['advanced']['fields']['optimize_assets_end'] );

return $config;
