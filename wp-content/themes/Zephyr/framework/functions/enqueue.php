<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Embed Google Fonts
 */
if ( us_get_option( 'lazyload_fonts', 0 ) == 1 ) {
	add_action( 'wp_footer', 'us_lazyload_fonts' );
} else {
	add_action( 'wp_enqueue_scripts', 'us_enqueue_fonts' );
}

function us_lazyload_fonts() {
	$prefixes = array( 'heading', 'body', 'menu' );
	$fonts = array();

	foreach ( $prefixes as $prefix ) {
		$font = explode( '|', us_get_option( $prefix . '_font_family', 'none' ), 2 );
		if ( ! isset( $font[1] ) OR empty( $font[1] ) ) {
			// Fault tolerance for missing font-variants
			$font[1] = '400,700';
		}
		$selected_font_variants = explode( ',', $font[1] );
		// Empty font or web safe combination selected
		if ( $font[0] == 'none' OR strpos( $font[0], ',' ) !== FALSE ) {
			continue;
		}

		$font[0] = str_replace( ' ', '+', $font[0] );
		if ( ! isset( $fonts[$font[0]] ) ) {
			$fonts[$font[0]] = array();
		}

		foreach ( $selected_font_variants as $font_variant ) {
			$fonts[$font[0]][] = $font_variant;
		}
	}

	if ( count( $fonts ) == 0 ) {
		return;
	}

	$subset = ':' . us_get_option( 'font_subset', 'latin' );
	$font_index = 1;
	$font_families = array();

	echo '<script src="https://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js"></script>';

	foreach ( $fonts as $font_name => $font_variants ) {
		if ( count( $font_variants ) == 0 ) {
			continue;
		}
		$font_variants = array_unique( $font_variants );

		// Google font url
		$font_family = $font_name . ':' . implode( ',', $font_variants ) . $subset;
		$font_families[] = $font_family;
		$font_index ++;
	}

	echo "<script>
  WebFont.load({
    google: {
      families: ['" . implode( "', '", $font_families ) . "']
    }
  });
</script>";
}

function us_enqueue_fonts() {
	$prefixes = array( 'heading', 'body', 'menu' );
	$fonts = array();

	foreach ( $prefixes as $prefix ) {
		$font = explode( '|', us_get_option( $prefix . '_font_family', 'none' ), 2 );
		if ( ! isset( $font[1] ) OR empty( $font[1] ) ) {
			// Fault tolerance for missing font-variants
			$font[1] = '400,700';
		}
		$selected_font_variants = explode( ',', $font[1] );
		// Empty font or web safe combination selected
		if ( $font[0] == 'none' OR strpos( $font[0], ',' ) !== FALSE ) {
			continue;
		}

		$font[0] = str_replace( ' ', '+', $font[0] );
		if ( ! isset( $fonts[$font[0]] ) ) {
			$fonts[$font[0]] = array();
		}

		foreach ( $selected_font_variants as $font_variant ) {
			$fonts[$font[0]][] = $font_variant;
		}
	}

	$subset = '&subset=' . us_get_option( 'font_subset', 'latin' );
	$font_family = '';

	foreach ( $fonts as $font_name => $font_variants ) {
		if ( count( $font_variants ) == 0 ) {
			continue;
		}
		$font_variants = array_unique( $font_variants );
		if ( $font_family != '' ) {
			$font_family .= urlencode( '|' );
		}
		$font_family .= $font_name . ':' . implode( ',', $font_variants );
	}
	if ( $font_family != '' ) {
		$font_url = 'https://fonts.googleapis.com/css?family=' . $font_family . $subset;
		wp_enqueue_style( 'us-fonts', $font_url );
	}
}

/**
 * Embed CSS files
 */
add_action( 'wp_enqueue_scripts', 'us_styles', 12 );
function us_styles() {
	global $us_template_directory_uri;
	$assets_config = us_config( 'assets', array() );

	if ( defined( 'US_DEV' ) AND US_DEV ) {
		foreach ( $assets_config as $component => $component_atts ) {
			wp_enqueue_style( 'us-' . $component, $us_template_directory_uri . $component_atts['css'], array(), US_THEMEVERSION, 'all' );
		}
	} elseif ( us_get_option( 'optimize_assets', 0 ) == 1 ) {
		$wp_upload_dir = wp_upload_dir();
		$styles_dir = $wp_upload_dir['basedir'] . '/us-assets';
		$styles_dir = str_replace( '\\', '/', $styles_dir );
		$site_url_parts = parse_url( site_url() );
		$styles_file_suffix = ( ! empty( $site_url_parts['host'] ) ) ? $site_url_parts['host'] : '';
		$styles_file_suffix .= ( ! empty( $site_url_parts['path'] ) ) ? str_replace( '/', '_', $site_url_parts['host'] ) : '';
		$styles_file_suffix = ( ! empty( $styles_file_suffix ) ) ? $styles_file_suffix : '';
		$styles_file = $styles_dir . '/' . $styles_file_suffix . '.css';
		// If the styles file does not exists
		if ( ! file_exists( $styles_file ) ) {
			// Try to create the styles file
			us_generate_optimized_css_file();
			// If create attempt failed
			if ( ! file_exists( $styles_file ) ) {
				// Switch the Optimize CSS size option off
				global $usof_options;
				usof_load_options_once();
				$updated_options = $usof_options;
				$updated_options['optimize_assets'] = 0;
				usof_save_options( $updated_options );
				// Load all styles to make sure site looks as it should
				foreach ( $assets_config as $component => $component_atts ) {
					wp_enqueue_style( 'us-' . $component, $us_template_directory_uri . $component_atts['css'], array(), US_THEMEVERSION, 'all' );
				}
			}
		}
		if ( file_exists( $styles_file ) ) {
			$styles_file_uri = $wp_upload_dir['baseurl'] . '/us-assets/' . $styles_file_suffix . '.css';
			// Removing protocols for better compatibility with caching plugins and services
			$styles_file_uri = str_replace( array( 'http:', 'https:' ), '', $styles_file_uri );
			wp_enqueue_style( 'us-theme', $styles_file_uri, array(), US_THEMEVERSION, 'all' );
		}
	} else {
		wp_register_style( 'us-style', $us_template_directory_uri . '/css/style.min.css', array(), US_THEMEVERSION, 'all' );
		wp_enqueue_style( 'us-style' );
	}
}

add_action( 'wp_enqueue_scripts', 'us_rtl_styles', 15 );
function us_rtl_styles() {
	global $us_template_directory_uri;

	$min_ext = ( ! ( defined( 'US_DEV' ) AND US_DEV ) ) ? '.min' : '';
	if ( is_rtl() ) {
		wp_register_style( 'us-rtl', $us_template_directory_uri . '/css/rtl' . $min_ext . '.css', array(), US_THEMEVERSION, 'all' );
		wp_enqueue_style( 'us-rtl' );
	}
}

if ( us_get_option( 'responsive_layout', TRUE ) AND ( ( defined( 'US_DEV' ) AND US_DEV ) OR us_get_option( 'optimize_assets', 0 ) == 0 ) ) {
	add_action( 'wp_enqueue_scripts', 'us_responsive_styles', 16 );
	function us_responsive_styles() {
		global $us_template_directory_uri;

		$min_ext = ( ! ( defined( 'US_DEV' ) AND US_DEV ) ) ? '.min' : '';
		wp_register_style( 'us-responsive', $us_template_directory_uri . '/css/responsive' . $min_ext . '.css', array(), US_THEMEVERSION, 'all' );
		wp_enqueue_style( 'us-responsive' );
	}
}

add_action( 'wp_enqueue_scripts', 'us_custom_styles', 18 );
function us_custom_styles() {
	if ( is_child_theme() ) {
		global $us_stylesheet_directory_uri;
		wp_enqueue_style( 'theme-style', $us_stylesheet_directory_uri . '/style.css', array(), US_THEMEVERSION, 'all' );
	}
}

/**
 * Embed JS files
 */
if ( us_get_option( 'disable_jquery_migrate', 1 ) == 1 ) {
	add_action( 'wp_default_scripts', 'us_dequeue_jquery_migrate' );
}

function us_dequeue_jquery_migrate( &$wp_scripts ) {
	if ( is_admin() ) {
		return;
	}
	$jquery_core_obj = $wp_scripts->registered['jquery-core'];
	$wp_scripts->remove( 'jquery' );
	$wp_scripts->add( 'jquery', FALSE, array( 'jquery-core' ), $jquery_core_obj->ver );
}

if ( us_get_option( 'jquery_footer', 1 ) == 1 ) {
	add_action( 'wp_default_scripts', 'us_move_jquery_to_footer' );
}

function us_move_jquery_to_footer( $wp_scripts ) {
	if ( is_admin() ) {
		return;
	}
	$wp_scripts->add_data( 'jquery', 'group', 1 );
	$wp_scripts->add_data( 'jquery-core', 'group', 1 );
	$wp_scripts->add_data( 'jquery-migrate', 'group', 1 );
}

add_action( 'wp_enqueue_scripts', 'us_jscripts' );
function us_jscripts() {
	global $us_template_directory_uri;

	wp_register_script( 'us-google-maps', '//maps.googleapis.com/maps/api/js', array(), '', FALSE );

	if ( us_get_option( 'ajax_load_js', 0 ) == 0 ) {
		wp_register_script( 'us-isotope', $us_template_directory_uri . '/framework/js/jquery.isotope.js', array( 'jquery' ), '2.2.2', TRUE );

		wp_register_script( 'us-royalslider', $us_template_directory_uri . '/framework/js/jquery.royalslider.min.js', array( 'jquery' ), '9.5.7', TRUE );

		wp_register_script( 'us-owl', $us_template_directory_uri . '/framework/js/owl.carousel.min.js', array( 'jquery' ), '2.0.0', TRUE );

		wp_register_script( 'us-magnific-popup', $us_template_directory_uri . '/framework/js/jquery.magnific-popup.js', array( 'jquery' ), '1.1.0', TRUE );
		wp_enqueue_script( 'us-magnific-popup' );

		wp_register_script( 'us-gmap', $us_template_directory_uri . '/framework/js/gmaps.min.js', array( 'jquery' ), '', TRUE );
	}

	if ( defined( 'US_DEV' ) AND US_DEV ) {
		wp_register_script( 'us-core', $us_template_directory_uri . '/framework/js/us.core.js', array( 'jquery' ), US_THEMEVERSION, TRUE );
	} else {
		wp_register_script( 'us-core', $us_template_directory_uri . '/framework/js/us.core.min.js', array( 'jquery' ), US_THEMEVERSION, TRUE );
	}
	wp_enqueue_script( 'us-core' );
}

add_action( 'wp_footer', 'us_theme_js', 98 );
function us_theme_js() {
	$buffer = us_get_template( 'config/theme-js' );
	echo $buffer;
}

add_action( 'wp_footer', 'us_custom_html_output', 99 );
function us_custom_html_output() {
	echo us_get_option( 'custom_html', '' );
}

/**
 * Generate and cache theme options css data
 *
 * @return string
 */
function us_get_theme_options_css() {
	if ( ( $styles_css = get_option( 'us_theme_options_css' ) ) === FALSE OR ( defined( 'US_DEV' ) AND US_DEV ) ) {
		$styles_css = us_minify_css( us_get_template( 'config/theme-options.css' ) );
		if ( ! defined( 'US_DEV' ) OR ! US_DEV ) {
			update_option( 'us_theme_options_css', $styles_css, TRUE );
		}
	}

	return $styles_css;
}