<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

// Apply Sidebar Options
$us_layout = US_Layout::instance();
if ( is_singular() ) {
	if ( usof_meta( 'us_sidebar' ) != '' ) {
		if ( usof_meta( 'us_sidebar' ) == 'hide' ) {
			$us_layout->sidebar_pos = 'none';
		} elseif ( usof_meta( 'us_sidebar' ) == 'custom' ) {
			$us_layout->sidebar_pos = usof_meta( 'us_sidebar_pos' );
		}
	} else {
		if ( us_get_option( 'product_sidebar', 0 ) == 1 ) {
			$us_layout->sidebar_pos = us_get_option( 'product_sidebar_pos', 'right' );
		} else {
			$us_layout->sidebar_pos = 'none';
		}
	}
} else {
	if ( us_get_option( 'shop_sidebar', 0 ) == 1 ) {
		$us_layout->sidebar_pos = us_get_option( 'shop_sidebar_pos', 'right' );
	} else {
		$us_layout->sidebar_pos = 'none';
	}
	if ( ! is_search() AND ! is_tax() ) {
		if ( usof_meta( 'us_sidebar', array(), wc_get_page_id( 'shop' ) ) == 'hide' ) {
			$us_layout->sidebar_pos = 'none';
		} elseif ( usof_meta( 'us_sidebar', array(), wc_get_page_id( 'shop' ) ) == 'custom' ) {
			$us_layout->sidebar_pos = usof_meta( 'us_sidebar_pos', array(), wc_get_page_id( 'shop' ) );
		}
	}
}

// Apply Title Bar Options from Theme Options
$show_titlebar = ( us_get_option( 'titlebar_shop', 1 ) == 1 );
if ( $show_titlebar AND ( us_get_option( 'titlebar_shop_defaults', 1 ) != 1 ) ) {
	$size_setting = us_get_option( 'titlebar_shop_size', 'medium' );
	$color_style_setting = us_get_option( 'titlebar_shop_color', 'alternate' );
	$show_breadcrumbs_setting = us_get_option( 'titlebar_shop_breadcrumbs', 0 );
	$bg_image_setting = us_get_option( 'titlebar_shop_bg_image' );
	$bg_size_setting = us_get_option( 'titlebar_shop_bg_size', 'cover' );
	$bg_repeat_setting = us_get_option( 'titlebar_shop_bg_repeat', 'repeat' );
	$bg_position_setting = us_get_option( 'titlebar_shop_bg_position', 'top left' );
	$bg_parallax_setting = us_get_option( 'titlebar_shop_bg_parallax', 'none' );
	$bg_overlay_color_setting = us_get_option( 'titlebar_shop_overlay_color', '' );
} else {
	$size_setting = us_get_option( 'titlebar_size', 'medium' );
	$color_style_setting = us_get_option( 'titlebar_color', 'alternate' );
	$show_breadcrumbs_setting = us_get_option( 'titlebar_breadcrumbs', 0 );
	$bg_image_setting = us_get_option( 'titlebar_bg_image' );
	$bg_size_setting = us_get_option( 'titlebar_bg_size', 'cover' );
	$bg_repeat_setting = us_get_option( 'titlebar_bg_repeat', 'repeat' );
	$bg_position_setting = us_get_option( 'titlebar_bg_position', 'top left' );
	$bg_parallax_setting = us_get_option( 'titlebar_bg_parallax', 'none' );
	$bg_overlay_color_setting = us_get_option( 'titlebar_overlay_color', '' );
}

// Apply Title Bar Options from Page Options
if ( is_singular() ) {
	if ( usof_meta( 'us_titlebar' ) == 'custom' ) {
		$show_titlebar = TRUE;
		$subtitle_setting = usof_meta( 'us_titlebar_subtitle' );
		if ( usof_meta( 'us_titlebar_size' ) != '' ) {
			$size_setting = usof_meta( 'us_titlebar_size' );
		}
		if ( usof_meta( 'us_titlebar_color' ) != '' ) {
			$color_style_setting = usof_meta( 'us_titlebar_color' );
		}
		if ( usof_meta( 'us_titlebar_breadcrumbs' ) != '' ) {
			$show_breadcrumbs_setting = ( usof_meta( 'us_titlebar_breadcrumbs' ) == 'show' ) ? 1 : 0;
		}
		if ( usof_meta( 'us_titlebar_image' ) != '' ) {
			$bg_image_setting = usof_meta( 'us_titlebar_image' );
			if ( usof_meta( 'us_titlebar_bg_size' ) != '' ) {
				$bg_size_setting = usof_meta( 'us_titlebar_bg_size' );
			}
			if ( usof_meta( 'us_titlebar_bg_repeat' ) != '' ) {
				$bg_repeat_setting = usof_meta( 'us_titlebar_bg_repeat' );
			}
			if ( usof_meta( 'us_titlebar_bg_position' ) != '' ) {
				$bg_position_setting = usof_meta( 'us_titlebar_bg_position' );
			}
			if ( usof_meta( 'us_titlebar_bg_parallax' ) != '' ) {
				$bg_parallax_setting = usof_meta( 'us_titlebar_bg_parallax' );
			}
			if ( usof_meta( 'us_titlebar_overlay_color' ) != '' ) {
				$bg_overlay_color_setting = usof_meta( 'us_titlebar_overlay_color' );
			}
		}
	} elseif ( usof_meta( 'us_titlebar' ) == 'hide' ) {
		$show_titlebar = FALSE;
	}
} elseif ( ! is_search() AND ! is_tax() ) {
	$pageID = wc_get_page_id( 'shop' );
	if ( usof_meta( 'us_titlebar', array(), $pageID ) == 'custom' ) {
		$show_titlebar = TRUE;
		$subtitle_setting = usof_meta( 'us_titlebar_subtitle', array(), $pageID );
		if ( usof_meta( 'us_titlebar_size', array(), $pageID ) != '' ) {
			$size_setting = usof_meta( 'us_titlebar_size', array(), $pageID );
		}
		if ( usof_meta( 'us_titlebar_color', array(), $pageID ) != '' ) {
			$color_style_setting = usof_meta( 'us_titlebar_color', array(), $pageID );
		}
		if ( usof_meta( 'us_titlebar_breadcrumbs', array(), $pageID ) != '' ) {
			$show_breadcrumbs_setting = ( usof_meta( 'us_titlebar_breadcrumbs', array(), $pageID ) == 'show' ) ? 1 : 0;
		}
		if ( usof_meta( 'us_titlebar_image', array(), $pageID ) != '' ) {
			$bg_image_setting = usof_meta( 'us_titlebar_image', array(), $pageID );
			if ( usof_meta( 'us_titlebar_bg_size', array(), $pageID ) != '' ) {
				$bg_size_setting = usof_meta( 'us_titlebar_bg_size', array(), $pageID );
			}
			if ( usof_meta( 'us_titlebar_bg_repeat', array(), $pageID ) != '' ) {
				$bg_repeat_setting = usof_meta( 'us_titlebar_bg_repeat', array(), $pageID );
			}
			if ( usof_meta( 'us_titlebar_bg_position', array(), $pageID ) != '' ) {
				$bg_position_setting = usof_meta( 'us_titlebar_bg_position', array(), $pageID );
			}
			if ( usof_meta( 'us_titlebar_bg_parallax', array(), $pageID ) != '' ) {
				$bg_parallax_setting = usof_meta( 'us_titlebar_bg_parallax', array(), $pageID );
			}
			if ( usof_meta( 'us_titlebar_overlay_color', array(), $pageID ) != '' ) {
				$bg_overlay_color_setting = usof_meta( 'us_titlebar_overlay_color', array(), $pageID );
			}
		}
	}  elseif ( usof_meta( 'us_titlebar', array(), wc_get_page_id( 'shop' ) ) == 'hide' ) {
		$show_titlebar = FALSE;
	}
}

get_header();

if ( $show_titlebar ) {

	// Hiding the default WooCommerce page title to avoid duplication
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
	add_filter( 'woocommerce_show_page_title', 'us_woocommerce_dont_show_page_title' );
	function us_woocommerce_dont_show_page_title() {
		return FALSE;
	}

	// Hiding the default WooCommerce breadcrumbs to avoid duplication
	if ( $show_breadcrumbs_setting ) {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_breadcrumb', 3 );
	}

	$template_vars = array(
		'show_titlebar' => TRUE,
		'show_title' => TRUE,
		'size' => $size_setting,
		'color_style' => $color_style_setting,
		'show_breadcrumbs' => $show_breadcrumbs_setting,
		'bg_image' => $bg_image_setting,
		'bg_size' => $bg_size_setting,
		'bg_repeat' => $bg_repeat_setting,
		'bg_position' => $bg_position_setting,
		'bg_parallax' => $bg_parallax_setting,
		'bg_overlay_color' => $bg_overlay_color_setting,
	);
	if ( is_singular() ) {
		$template_vars['title'] = get_the_title();
	} else {
		$template_vars['title'] = woocommerce_page_title( FALSE );
		if ( ! is_search() AND ! is_tax() ) {
			$template_vars['subtitle'] = usof_meta( 'us_titlebar_subtitle', array(), wc_get_page_id( 'shop' ) );
		}
	}
	us_load_template( 'templates/titlebar', $template_vars );
}
