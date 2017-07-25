<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Outputs page's titlebar
 *
 * (!) Should be called after the current $wp_query is already defined
 *
 * @var $show_titlebar    bool Show Title bar?
 * @var $show_breadcrumbs bool Show Breadcrumbs?
 * @var $title            string Current post title
 * @var $subtitle         string Description
 * @var $size             string Size
 * @var $color_style      string Color Style
 * @var $bg_image         string Background Image
 * @var $bg_size          string Background Size
 * @var $bg_repeat        string Background Repeat
 * @var $bg_position      string Background Position
 * @var $bg_parallax      string Background Image Parallax
 * @var $bg_overlay_color string Background Overlay Color
 *
 * @action Before the template: 'us_before_template:templates/titlebar'
 * @action After the template: 'us_after_template:templates/titlebar'
 * @filter Template variables: 'us_template_vars:templates/titlebar'
 */

$postID = $page_404 = NULL;
if ( is_singular() ) {
	$postID = get_the_ID();
}
if ( is_404() AND $page_404 = get_page_by_path( 'error-404' ) ) {
	$postID = $page_404->ID;
}
$supported_custom_post_types = us_get_option( 'custom_post_types_support', array() );

$show_titlebar_setting = ( us_get_option( 'titlebar', 1 ) == 1 );
$size_setting = us_get_option( 'titlebar_size', 'medium' );
$color_style_setting = us_get_option( 'titlebar_color', 'alternate' );
$show_breadcrumbs_setting = us_get_option( 'titlebar_breadcrumbs', 0 );
$bg_image_setting = us_get_option( 'titlebar_bg_image' );
$bg_size_setting = us_get_option( 'titlebar_bg_size', 'cover' );
$bg_repeat_setting = us_get_option( 'titlebar_bg_repeat', 'repeat' );
$bg_position_setting = us_get_option( 'titlebar_bg_position', 'top left' );
$bg_parallax_setting = us_get_option( 'titlebar_bg_parallax', 'none' );
$bg_overlay_color_setting = us_get_option( 'titlebar_overlay_color', '' );

if ( is_singular( array_merge( array( 'page' ), $supported_custom_post_types ) ) OR ( is_404() AND $postID != NULL ) ) {
	// Using defaults here
} elseif ( is_singular( 'us_portfolio' ) ) {
	$show_titlebar_setting = ( us_get_option( 'titlebar_portfolio', 1 ) == 1 );
	if ( $show_titlebar_setting AND ( us_get_option( 'titlebar_portfolio_defaults', 1 ) != 1 ) ) {
		$size_setting = us_get_option( 'titlebar_portfolio_size', 'medium' );
		$color_style_setting = us_get_option( 'titlebar_portfolio_color', 'alternate' );
		$show_breadcrumbs_setting = us_get_option( 'titlebar_portfolio_breadcrumbs', 0 );
		$bg_image_setting = us_get_option( 'titlebar_portfolio_bg_image' );
		$bg_size_setting = us_get_option( 'titlebar_portfolio_bg_size', 'cover' );
		$bg_repeat_setting = us_get_option( 'titlebar_portfolio_bg_repeat', 'repeat' );
		$bg_position_setting = us_get_option( 'titlebar_portfolio_bg_position', 'top left' );
		$bg_parallax_setting = us_get_option( 'titlebar_portfolio_bg_parallax', 'none' );
		$bg_overlay_color_setting = us_get_option( 'titlebar_portfolio_overlay_color', '' );
	}
} elseif ( is_singular( 'post' ) ) {
	$show_titlebar_setting = ( us_get_option( 'titlebar_post', 1 ) == 1 );
	if ( $show_titlebar_setting AND ( us_get_option( 'titlebar_post_defaults', 1 ) != 1 ) ) {
		$size_setting = us_get_option( 'titlebar_post_size', 'medium' );
		$color_style_setting = us_get_option( 'titlebar_post_color', 'alternate' );
		$show_breadcrumbs_setting = us_get_option( 'titlebar_post_breadcrumbs', 0 );
		$bg_image_setting = us_get_option( 'titlebar_post_bg_image' );
		$bg_size_setting = us_get_option( 'titlebar_post_bg_size', 'cover' );
		$bg_repeat_setting = us_get_option( 'titlebar_post_bg_repeat', 'repeat' );
		$bg_position_setting = us_get_option( 'titlebar_post_bg_position', 'top left' );
		$bg_parallax_setting = us_get_option( 'titlebar_post_bg_parallax', 'none' );
		$bg_overlay_color_setting = us_get_option( 'titlebar_post_overlay_color', '' );
	}
} elseif ( is_singular( 'tribe_events' ) OR is_tax( 'tribe_events_cat' ) OR is_post_type_archive( 'tribe_events' ) ) {
	$show_titlebar_setting = FALSE;
} else {
	$show_titlebar_setting = ( us_get_option( 'titlebar_archive', 1 ) == 1 );
	if ( $show_titlebar_setting AND ( us_get_option( 'titlebar_archive_defaults', 1 ) != 1 ) ) {
		$size_setting = us_get_option( 'titlebar_archive_size', 'medium' );
		$color_style_setting = us_get_option( 'titlebar_archive_color', 'alternate' );
		$show_breadcrumbs_setting = us_get_option( 'titlebar_archive_breadcrumbs', 0 );
		$bg_image_setting = us_get_option( 'titlebar_archive_bg_image' );
		$bg_size_setting = us_get_option( 'titlebar_archive_bg_size', 'cover' );
		$bg_repeat_setting = us_get_option( 'titlebar_archive_bg_repeat', 'repeat' );
		$bg_position_setting = us_get_option( 'titlebar_archive_bg_position', 'top left' );
		$bg_parallax_setting = us_get_option( 'titlebar_archive_bg_parallax', 'none' );
		$bg_overlay_color_setting = us_get_option( 'titlebar_archive_overlay_color', '' );
	}
}

if ( is_singular( array_merge( array( 'page', 'us_portfolio', 'post' ), $supported_custom_post_types ) ) OR ! empty( $page_404 ) ) {
	// Apply custom settings from metabox
	if ( usof_meta( 'us_titlebar', array(), $postID ) == 'custom' ) {
		$show_titlebar_setting = TRUE;
		$subtitle_setting = usof_meta( 'us_titlebar_subtitle', array(), $postID );
		if ( usof_meta( 'us_titlebar_size', array(), $postID ) != '' ) {
			$size_setting = usof_meta( 'us_titlebar_size', array(), $postID );
		}
		if ( usof_meta( 'us_titlebar_breadcrumbs', array(), $postID ) != '' ) {
			$show_breadcrumbs_setting = ( usof_meta( 'us_titlebar_breadcrumbs', array(), $postID ) == 'show' ) ? 1 : 0;
		}
		if ( usof_meta( 'us_titlebar_color', array(), $postID ) != '' ) {
			$color_style_setting = usof_meta( 'us_titlebar_color', array(), $postID );
		}
		if ( usof_meta( 'us_titlebar_image', array(), $postID ) != '' ) {
			$bg_image_setting = usof_meta( 'us_titlebar_image', array(), $postID );
			if ( usof_meta( 'us_titlebar_bg_size', array(), $postID ) != '' ) {
				$bg_size_setting = usof_meta( 'us_titlebar_bg_size', array(), $postID );
			}
			if ( usof_meta( 'us_titlebar_bg_repeat', array(), $postID ) != '' ) {
				$bg_repeat_setting = usof_meta( 'us_titlebar_bg_repeat', array(), $postID );
			}
			if ( usof_meta( 'us_titlebar_bg_position', array(), $postID ) != '' ) {
				$bg_position_setting = usof_meta( 'us_titlebar_bg_position', array(), $postID );
			}
			if ( usof_meta( 'us_titlebar_bg_parallax', array(), $postID ) != '' ) {
				$bg_parallax_setting = usof_meta( 'us_titlebar_bg_parallax', array(), $postID );
			}
			if ( usof_meta( 'us_titlebar_overlay_color', array(), $postID ) != '' ) {
				$bg_overlay_color_setting = usof_meta( 'us_titlebar_overlay_color', array(), $postID );
			}
		}
	} elseif ( usof_meta( 'us_titlebar', array(), $postID ) == 'hide' ) {
		$show_titlebar_setting = FALSE;
	}
}

$show_titlebar = isset( $show_titlebar ) ? $show_titlebar : $show_titlebar_setting;
if ( ! $show_titlebar ) {
	return;
}

$show_title = isset( $show_title ) ? $show_title : TRUE;
if ( $show_title ) {
	$title = isset( $title ) ? $title : get_the_title();
	if ( ! isset( $subtitle ) ) {
		$subtitle = $postID ? usof_meta( 'us_titlebar_subtitle', array(), $postID ) : '';
	}
}

$show_breadcrumbs = ( isset( $show_breadcrumbs ) ) ? $show_breadcrumbs : $show_breadcrumbs_setting;

// No need to do other actions: titlebar will be hidden
if ( ! $show_title AND ! $show_breadcrumbs ) {
	return;
}

$classes = $bg_img_atts = $bg_img_styles = '';

$size = isset( $size ) ? $size : $size_setting;
$color_style = isset( $color_style ) ? $color_style : $color_style_setting;
$bg_image = isset( $bg_image ) ? $bg_image : $bg_image_setting;
$bg_size = isset( $bg_size ) ? $bg_size : $bg_size_setting;
$bg_repeat = isset( $bg_repeat ) ? $bg_repeat : $bg_repeat_setting;
$bg_position = isset( $bg_position ) ? $bg_position : $bg_position_setting;
$bg_parallax = isset( $bg_parallax ) ? $bg_parallax : $bg_parallax_setting;
$bg_overlay_color = isset( $bg_overlay_color ) ? $bg_overlay_color : $bg_overlay_color_setting;

if ( ! empty( $bg_image ) ) {
	$bg_image_src = wp_get_attachment_image_src( (int) $bg_image, 'full' );
	if ( $bg_image_src ) {
		$bg_image = $bg_image_src[0];
		$bg_img_atts .= ' data-img-width="' . $bg_image_src[1] . '" data-img-height="' . $bg_image_src[2] . '"';
	}
	if ( $bg_size != 'initial' ) {
		$bg_img_styles .= ' background-size:' . $bg_size . ';';
	}
	if ( $bg_repeat != 'repeat' ) {
		$bg_img_styles .= ' background-repeat:' . $bg_repeat . ';';
	}
	if ( $bg_position != 'top left' ) {
		$bg_img_styles .= ' background-position:' . $bg_position . ';';
	}
}
if ( $bg_parallax == 'vertical' ) {
	$classes .= ' parallax_ver';
	if ( in_array( $bg_position, array( 'top right', 'center right', 'bottom right' ) ) ) {
		$classes .= ' parallax_xpos_right';
	} elseif ( in_array( $bg_position, array( 'top left', 'center left', 'bottom left' ) ) ) {
		$classes .= ' parallax_xpos_left';
	}
} elseif ( $bg_parallax == 'vertical_reversed' ) {
	$classes .= ' parallax_ver parallaxdir_reversed';
	if ( in_array( $bg_position, array( 'top right', 'center right', 'bottom right' ) ) ) {
		$classes .= ' parallax_xpos_right';
	} elseif ( in_array( $bg_position, array( 'top left', 'center left', 'bottom left' ) ) ) {
		$classes .= ' parallax_xpos_left';
	}
} elseif ( $bg_parallax == 'still' ) {
	$classes .= ' parallax_fixed';
} elseif ( $bg_parallax == 'horizontal' ) {
	$classes .= ' parallax_hor';
}

$classes .= ' size_' . $size . ' color_' . $color_style;

$output = '<div class="l-titlebar' . $classes . '">';
if ( ! empty( $bg_image ) ) {
	$output .= '<div class="l-titlebar-img" style="background-image: url(' . $bg_image . ');' . $bg_img_styles . '"' . $bg_img_atts . '></div>';
}
if ( ! empty( $bg_overlay_color ) ) {
	$output .= '<div class="l-titlebar-overlay" style="background-color:' . $bg_overlay_color . '"></div>';
}
$output .= '<div class="l-titlebar-h"><div class="l-titlebar-content">';
if ( $show_title ) {
	$output .= ( $title != '' ) ? '<h1 itemprop="headline">' . $title . '</h1>' : '';
	if ( ! empty( $subtitle ) ) {
		$output .= '<p>' . $subtitle . '</p>';
	}
}
$output .= '</div>';
if ( $show_breadcrumbs ) {
	// TODO Create the us_get_breadcrumbs function instead
	ob_start();
	us_breadcrumbs();
	$output .= ob_get_clean();
}
$output .= '</div></div>';

echo $output;
