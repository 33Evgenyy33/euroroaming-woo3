<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options: USOF + UpSolution extendings
 *
 * Should be included in global context.
 */

// Generating single CSS file
add_action( 'usof_after_save', 'us_generate_optimized_css_file' );
add_action( 'usof_ajax_mega_menu_save_settings', 'us_generate_optimized_css_file' );
function us_generate_optimized_css_file() {
	global $usof_options, $us_template_directory;
	usof_load_options_once();

	if ( isset( $usof_options['optimize_assets'] ) AND $usof_options['optimize_assets'] ) {
		delete_option( 'us_theme_options_css' );
		$result_css = '';

		// Add styles set in Theme Options
		$assets_config = us_config( 'assets', array() );
		foreach ( $assets_config as $component => $component_atts ) {
			if ( isset( $component_atts['apply_if'] ) AND ! $component_atts['apply_if'] ) {
				continue;
			}
			if ( ( isset( $component_atts['hidden'] ) AND $component_atts['hidden'] ) OR ! isset( $usof_options['assets'] ) OR in_array( $component, $usof_options['assets'] ) ) {
				$result_css .= file_get_contents( $us_template_directory . $component_atts['css'] ) . "\n";
			}
		}

		// Add generated styles by Theme Options
		$result_css .= us_get_template( 'config/theme-options.css' ) . "\n";
		
		// Add responsive styles to the end, if Responsive Layout is enabled
		if ( $usof_options['responsive_layout'] ) {
			$result_css .= file_get_contents( $us_template_directory . '/css/responsive.css' ) . "\n";
		}

		if ( ( $us_custom_css = us_get_option( 'custom_css', '' ) ) != '' ) {
			$result_css .= $us_custom_css;
		}

		// TODO Use WP_Filesystem instead
		$wp_upload_dir = wp_upload_dir();
		$styles_dir = wp_normalize_path( $wp_upload_dir['basedir'] . '/us-assets' );
		$site_url_parts = parse_url( site_url() );
		$styles_file_suffix = ( ! empty( $site_url_parts['host'] ) ) ? $site_url_parts['host'] : '';
		$styles_file_suffix .= ( ! empty( $site_url_parts['path'] ) ) ? str_replace( '/', '_', $site_url_parts['host'] ) : '';
		$styles_file_suffix = ( ! empty( $styles_file_suffix ) ) ? $styles_file_suffix : '';
		$styles_file = $styles_dir . '/' . $styles_file_suffix . '.css';

		if ( ! is_dir( $styles_dir ) ) {
			wp_mkdir_p( trailingslashit( $styles_dir ) );
		}
		$handle = @fopen( $styles_file, 'w' );
		if ( $handle ) {
			if ( ! fwrite( $handle, us_minify_css( $result_css ) ) ) {
				return FALSE;
			}
			fclose( $handle );

			return TRUE;
		}

		return FALSE;

	} else {
		update_option( 'us_theme_options_css', us_minify_css( us_get_template( 'config/theme-options.css' ) ), TRUE );
	}

	return FALSE;
}

// Flushing WP rewrite rules on portfolio slug changes
add_action( 'usof_before_save', 'us_maybe_flush_rewrite_rules' );
add_action( 'usof_after_save', 'us_maybe_flush_rewrite_rules' );
function us_maybe_flush_rewrite_rules( $updated_options ) {
	// The function is called twice: before and after options change
	static $old_portfolio_slug = NULL;
	static $old_portfolio_category_slug = NULL;
	$flush_rules = FALSE;
	if ( ! isset( $updated_options['portfolio_slug'] ) ) {
		$updated_options['portfolio_slug'] = NULL;
	}
	if ( ! isset( $updated_options['portfolio_category_slug'] ) ) {
		$updated_options['portfolio_category_slug'] = NULL;
	}
	if ( $old_portfolio_slug === NULL ) {
		// At first call we're storing the previous portfolio slug
		$old_portfolio_slug = us_get_option( 'portfolio_slug', 'portfolio' );
	} elseif ( $old_portfolio_slug != $updated_options['portfolio_slug'] ) {
		// At second call we're triggering flush rewrite rules at the next app execution
		// We're using transients to reduce the number of excess auto-loaded options
		$flush_rules = TRUE;
	}
	if ( $old_portfolio_category_slug === NULL ) {
		// At first call we're storing the previous portfolio slug
		$old_portfolio_category_slug = us_get_option( 'portfolio_category_slug', 'portfolio_category' );
	} elseif ( $old_portfolio_slug != $updated_options['portfolio_category_slug'] ) {
		// At second call we're triggering flush rewrite rules at the next app execution
		// We're using transients to reduce the number of excess auto-loaded options
		$flush_rules = TRUE;
	}

	if ( $flush_rules ) {
		set_transient( 'us_flush_rules', TRUE, DAY_IN_SECONDS );
	}
}

add_action( 'usof_after_save', 'us_update_site_icon_from_options' );
function us_update_site_icon_from_options( $updated_options ) {
	$options_site_icon = $updated_options['site_icon'];
	$wp_site_icon = get_option( 'site_icon' );

	if ( $options_site_icon != $wp_site_icon ) {
		update_option( 'site_icon', $options_site_icon );
	}
}

add_filter( 'usof_load_options_once', 'us_get_site_icon_for_options' );
function us_get_site_icon_for_options( $usof_options ) {
	$wp_site_icon = get_option( 'site_icon' );

	$usof_options['site_icon'] = $wp_site_icon;
	return $usof_options;
}

// Using USOF for theme options
$usof_directory = $us_template_directory . '/framework/vendor/usof';
$usof_directory_uri = $us_template_directory_uri . '/framework/vendor/usof';
require $us_template_directory . '/framework/vendor/usof/usof.php';
