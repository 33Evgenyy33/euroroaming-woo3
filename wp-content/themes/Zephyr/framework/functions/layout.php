<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

class US_Layout {

	/**
	 * @var US_Layout
	 */
	protected static $instance;

	/**
	 * Singleton pattern: US_Layout::instance()->do_something()
	 *
	 * @return US_Layout
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * @var string Columns type: right, left, none
	 */
	public $sidebar_pos;

	/**
	 * @var string Canvas type: wide / boxed
	 */
	public $canvas_type;

	/**
	 * @var string Default-state header orientation: 'hor' / 'ver'
	 */
	public $header_orientation;

	/**
	 * @var string Default-state header position: 'static' / 'fixed'
	 */
	public $header_pos;

	/**
	 * @var string Default-state header background: 'solid' / 'transparent'
	 */
	public $header_bg;

	/**
	 * @var string Default-state header show: 'always' / 'never'
	 */
	public $header_show;

	protected function __construct() {

		do_action( 'us_layout_before_init', $this );

		if ( WP_DEBUG AND ! ( isset( $GLOBALS['post'] ) OR is_404() OR is_search() OR is_archive() OR ( is_home() AND ! have_posts() ) ) ) {
			wp_die( 'US_Layout can be inited only after the current post is obtained' );
		}

		$postID = NULL;

		if ( is_singular() ) {
			$postID = get_the_ID();
		}
		if ( is_404() AND $page_404 = get_page_by_path( 'error-404' ) ) {
			$postID = $page_404->ID;
		}

		$supported_custom_post_types = us_get_option( 'custom_post_types_support', array() );
		if ( is_home() ) {
			// Default homepage blog listing
			if ( us_get_option( 'blog_sidebar', 0 ) == 1 ) {
				$this->sidebar_pos = us_get_option( 'blog_sidebar_pos', 'right' );
			} else {
				$this->sidebar_pos = 'none';
			}
		} elseif ( is_singular( array( 'post', 'attachment' ) ) ) {
			// Posts and attachments
			if ( us_get_option( 'post_sidebar', 0 ) == 1 ) {
				$this->sidebar_pos = us_get_option( 'post_sidebar_pos', 'right' );
			} else {
				$this->sidebar_pos = 'none';
			}
		} elseif ( is_singular( array( 'us_portfolio' ) ) ) {
			// Portfolio page
			if ( us_get_option( 'portfolio_sidebar', 0 ) == 1 ) {
				$this->sidebar_pos = us_get_option( 'portfolio_sidebar_pos', 'right' );
			} else {
				$this->sidebar_pos = 'none';
			}
		} elseif ( is_singular( array( 'tribe_events' ) ) OR is_tax( 'tribe_events_cat' ) OR is_post_type_archive( 'tribe_events' ) ) {
			// Events Calendar pages
			if ( us_get_option( 'event_sidebar', 0 ) == 1 ) {
				$this->sidebar_pos = us_get_option( 'event_sidebar_pos', 'right' );
			} else {
				$this->sidebar_pos = 'none';
			}
		} elseif (  is_array( $supported_custom_post_types ) AND count( $supported_custom_post_types ) > 0 AND is_singular( $supported_custom_post_types ) ) {
			// Supported custom post types
			$post_type = get_post_type();
			if ( us_get_option( 'sidebar_' . $post_type, 0 ) == 1 ) {
				$this->sidebar_pos = us_get_option( 'sidebar_' . $post_type . '_pos', 'right' );
			} else {
				$this->sidebar_pos = 'none';
			}
		} elseif ( is_page() OR ( is_404() AND $page_404 = get_page_by_path( 'error-404' ) ) ) {
			// Pages, 404 special page
			if ( us_get_option( 'sidebar', 0 ) == 1 ) {
				$this->sidebar_pos = us_get_option( 'sidebar_pos', 'right' );
			} else {
				$this->sidebar_pos = 'none';
			}
		} elseif ( is_search() ) {
			// Search
			if ( us_get_option( 'search_sidebar', 0 ) == 1 ) {
				$this->sidebar_pos = us_get_option( 'search_sidebar_pos', 'right' );
			} else {
				$this->sidebar_pos = 'none';
			}
		} elseif ( is_archive() ) {
			// Archive
			if ( us_get_option( 'archive_sidebar', 0 ) == 1 ) {
				$this->sidebar_pos = us_get_option( 'archive_sidebar_pos', 'right' );
			} else {
				$this->sidebar_pos = 'none';
			}
		} elseif ( is_404() ) {
			// 404 page
			$this->sidebar_pos = 'none';
		} else {
			$this->sidebar_pos = 'none';
		}

		$this->canvas_type = us_get_option( 'canvas_layout', 'wide' );
		$this->header_orientation = us_get_header_option( 'orientation', 'default', 'hor' );
		$this->header_pos = us_get_header_option( 'sticky', 'default', FALSE ) ? 'fixed' : 'static';
		$this->header_initial_pos = 'top';
		$this->header_bg = us_get_header_option( 'transparent', 'default', FALSE ) ? 'transparent' : 'solid';
		$this->header_shadow = us_get_header_option( 'shadow', 'default', 'thin' );
		$this->header_show = 'always';

		// Some of the options may be overloaded by post's meta settings
		if ( is_singular(
			array_merge(
				array(
					'post',
					'page',
					'us_portfolio',
					'product',
				), $supported_custom_post_types
			)
		) OR ( is_404() AND $postID != NULL ) ) {
			if ( usof_meta( 'us_sidebar', array(), $postID ) == 'hide' ) {
				$this->sidebar_pos = 'none';
			} elseif ( usof_meta( 'us_sidebar', array(), $postID ) == 'custom' ) {
				$this->sidebar_pos = usof_meta( 'us_sidebar_pos', array(), $postID );
			}

			global $us_iframe;
			if ( ( isset( $us_iframe ) AND $us_iframe ) OR usof_meta( 'us_header', array(), $postID ) == 'hide' ) {
				$this->header_show = 'never';
				$this->header_orientation = 'none';
			} elseif ( usof_meta( 'us_header', array(), $postID ) == 'custom' AND usof_meta( 'us_header_sticky_pos', array(), $postID ) != '' AND $this->header_orientation == 'hor' AND $this->sidebar_pos == 'none' ) {
				$this->header_initial_pos = usof_meta( 'us_header_sticky_pos', array(), $postID );
			}
		}
		
		// Some wrong value may came from various theme options, so filtering it
		if ( ! in_array( $this->sidebar_pos, array( 'right', 'left', 'none' ) ) ) {
			$this->sidebar_pos = 'none';
		}

		if ( $this->header_orientation == 'ver' ) {
			$this->header_pos = 'fixed';
			$this->header_bg = 'solid';
		}

		do_action( 'us_layout_after_init', $this );
	}

	/**
	 * Obtain theme-defined CSS classes for <html> element
	 *
	 * @return string
	 */
	public function html_classes() {
		$classes = '';

		if ( ! us_get_option( 'responsive_layout', TRUE ) ) {
			$classes .= 'no-responsive';
		}

		return $classes;
	}

	/**
	 * Obtain theme-defined CSS classes for <body> element
	 *
	 * @return string
	 */
	public function body_classes() {
		// TODO Dynamically prepare theme slug name
		$classes = US_THEMENAME . '_' . US_THEMEVERSION;
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( is_plugin_active( 'us-header-builder/us-header-builder.php' ) ) {
			$classes .= ' HB';
			if ( defined( 'US_HB_VERSION' ) ) {
				$classes .= '_' . US_HB_VERSION;
			}  else {
				$hb_data = @get_plugin_data( ABSPATH . 'wp-content/plugins/us-header-builder/us-header-builder.php' );
				$classes .= '_' . $hb_data['Version'];
			}
		}
		$classes .= ' header_' . $this->header_orientation;
		$classes .= ' header_inpos_' . $this->header_initial_pos;
		$classes .= ' btn_hov_' . us_get_option( 'button_hover' );
		if ( us_get_option( 'links_underline' ) == TRUE ) {
			$classes .= ' links_underline';
		}
		if ( us_get_option( 'rounded_corners' ) !== NULL AND us_get_option( 'rounded_corners' ) == FALSE ) {
			$classes .= ' rounded_none';
		}
		$classes .= ' state_default';

		global $us_iframe;
		if ( ( isset( $us_iframe ) AND $us_iframe ) ) {
			$classes .= ' us_iframe';
		}

		return $classes;
	}

	/**
	 * Obtain CSS classes for .l-canvas
	 *
	 * @return string
	 */
	public function canvas_classes() {

		$classes = 'sidebar_' . $this->sidebar_pos . ' type_' . $this->canvas_type;

		// Language modificator
		if ( defined( 'ICL_LANGUAGE_CODE' ) AND ICL_LANGUAGE_CODE ) {
			$classes .= ' wpml_lang_' . ICL_LANGUAGE_CODE;
		}

		return $classes;
	}

	/**
	 * Obtain CSS classes for .l-header
	 *
	 * @return string
	 */
	public function header_classes() {

		$classes = 'pos_' . $this->header_pos;
		$classes .= ' bg_' . $this->header_bg;
		$classes .= ' shadow_' . $this->header_shadow;
		if ( us_get_option( 'header_invert_logo_pos', FALSE ) ) {
			$classes .= ' logopos_right';
		}

		return $classes;
	}

}
