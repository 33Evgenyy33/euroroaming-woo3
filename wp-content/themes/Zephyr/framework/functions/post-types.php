<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

// Should be inited before the visual composer (that is 9)
$portfolio_slug = us_get_option( 'portfolio_slug', 'portfolio' );
add_action( 'init', 'us_create_post_types', 8 );
function us_create_post_types() {
	// Footer post type
	register_post_type(
		'us_footer', array(
			'labels' => array(
				'name' => __( 'Footers', 'us' ),
				'singular_name' => __( 'Footer', 'us' ),
				'add_new' => __( 'Add New Footer', 'us' ),
				'add_new_item' => __( 'Add New Footer', 'us' ),
				'edit_item' => __( 'Edit Footer', 'us' ),
			),
			'public' => TRUE,
			'show_in_menu' => 'us-theme-options',
			'exclude_from_search' => TRUE,
			'show_in_admin_bar' => FALSE,
			'publicly_queryable' => FALSE,
			'show_in_nav_menus' => FALSE,
			'capability_type' => array( 'us_footer', 'us_footers' ),
			'map_meta_cap' => TRUE,
		)
	);

	if ( us_get_option( 'enable_portfolio', 1 ) == 1 ) {
		// Portfolio post type
		global $portfolio_slug;
		if ( $portfolio_slug == '' ) {
			$portfolio_rewrite = array( 'slug' => FALSE, 'with_front' => FALSE );
		} else {
			$portfolio_rewrite = array( 'slug' => untrailingslashit( $portfolio_slug ) );
		}
		register_post_type(
			'us_portfolio', array(
				'labels' => array(
					'name' => __( 'Portfolio', 'us' ),
					'singular_name' => __( 'Portfolio Page', 'us' ),
					'all_items' => __( 'Portfolio Pages', 'us' ),
					'add_new' => __( 'Add Portfolio Page', 'us' ),
					'add_new_item' => __( 'Add Portfolio Page', 'us' ),
					'edit_item' => __( 'Edit Portfolio Page', 'us' ),
					'view_item' => us_translate( 'View Page' ),
					'not_found' => us_translate( 'No pages found.' ),
					'not_found_in_trash' => us_translate( 'No pages found in Trash.' ),
				),
				'public' => TRUE,
				'rewrite' => $portfolio_rewrite,
				'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'comments' ),
				'capability_type' => array( 'us_portfolio', 'us_portfolios' ),
				'map_meta_cap' => TRUE,
				'menu_icon' => 'dashicons-images-alt',
			)
		);

		// Portfolio categories
		register_taxonomy(
			'us_portfolio_category', array( 'us_portfolio' ), array(
				'labels' => array(
					'name' => __( 'Portfolio Categories', 'us' ),
					'menu_name' => us_translate( 'Categories' ),
				),
				'hierarchical' => TRUE,
				'rewrite' => array( 'slug' => us_get_option( 'portfolio_category_slug', 'portfolio_category' ) ),
			)
		);

		// Portfolio slug may have changed, so we need to keep WP's rewrite rules fresh
		if ( get_transient( 'us_flush_rules' ) ) {
			flush_rewrite_rules();
			delete_transient( 'us_flush_rules' );
		}
	}

	if ( us_get_option( 'enable_testimonials', 1 ) == 1 ) {
		// Testimonial post type
		register_post_type(
			'us_testimonial', array(
				'labels' => array(
					'name' => __( 'Testimonials', 'us' ),
					'singular_name' => __( 'Testimonial', 'us' ),
					'add_new' => __( 'Add Testimonial', 'us' ),
					'add_new_item' => __( 'Add Testimonial', 'us' ),
					'edit_item' => __( 'Edit Testimonial', 'us' ),
					'featured_image' => __( 'Author Photo', 'us' ),
				),
				'show_ui' => TRUE,
				'supports' => array( 'title', 'editor', 'thumbnail' ),
				'menu_icon' => 'dashicons-testimonial',
				'capability_type' => array( 'us_testimonial', 'us_testimonials' ),
				'map_meta_cap' => TRUE,
			)
		);

		// Testimonial categories
		register_taxonomy(
			'us_testimonial_category', array( 'us_testimonial' ), array(
				'labels' => array(
					'name' => __( 'Testimonial Categories', 'us' ),
					'menu_name' => us_translate( 'Categories' ),
				),
				'public' => FALSE,
				'show_ui' => TRUE,
				'hierarchical' => TRUE,
			)
		);
	}
}

add_filter( 'manage_us_portfolio_posts_columns', 'us_manage_portfolio_columns' );
function us_manage_portfolio_columns( $columns ) {
	$columns['us_portfolio_category'] = us_translate( 'Categories' );
	if ( isset( $columns['comments'] ) ) {
		$title = $columns['comments'];
		unset( $columns['comments'] );
		$columns['comments'] = $title;
	}
	if ( isset( $columns['date'] ) ) {
		$title = $columns['date'];
		unset( $columns['date'] );
		$columns['date'] = $title;
	}

	return $columns;
}

add_action( 'manage_us_portfolio_posts_custom_column', 'us_manage_portfolio_custom_column', 10, 2 );
function us_manage_portfolio_custom_column( $column_name, $post_id ) {
	if ( $column_name == 'us_portfolio_category' ) {
		if ( ! $terms = get_the_terms( $post_id, $column_name ) ) {
			echo '<span class="na">&ndash;</span>';
		} else {
			$termlist = array();
			foreach ( $terms as $term ) {
				$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column_name . '=' . $term->slug . '&post_type=us_portfolio' ) . ' ">' . $term->name . '</a>';
			}

			echo implode( ', ', $termlist );
		}
	}
}

add_filter( 'manage_us_testimonial_posts_columns', 'us_manage_testimonial_columns' );
function us_manage_testimonial_columns( $columns ) {
	$columns['us_testimonial_category'] = us_translate( 'Categories' );
	if ( isset( $columns['date'] ) ) {
		$title = $columns['date'];
		unset( $columns['date'] );
		$columns['date'] = $title;
	}

	return $columns;
}

add_action( 'manage_us_testimonial_posts_custom_column', 'us_manage_testimonial_custom_column', 10, 2 );
function us_manage_testimonial_custom_column( $column_name, $post_id ) {
	if ( $column_name == 'us_testimonial_category' ) {
		if ( ! $terms = get_the_terms( $post_id, $column_name ) ) {
			echo '<span class="na">&ndash;</span>';
		} else {
			$termlist = array();
			foreach ( $terms as $term ) {
				$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column_name . '=' . $term->slug . '&post_type=us_testimonial' ) . ' ">' . $term->name . '</a>';
			}

			echo implode( ', ', $termlist );
		}
	}
}

// TODO Move to a separate plugin for proper action order, and remove page refreshes
add_action( 'admin_init', 'us_add_theme_caps' );
function us_add_theme_caps() {
	global $wp_post_types;
	$role = get_role( 'administrator' );
	$force_refresh = FALSE;
	$custom_post_types = array( 'us_portfolio', 'us_testimonial', 'us_footer' );
	foreach ( $custom_post_types as $post_type ) {
		if ( ! isset( $wp_post_types[$post_type] ) ) {
			continue;
		}
		foreach ( $wp_post_types[$post_type]->cap as $cap ) {
			if ( ! $role->has_cap( $cap ) ) {
				$role->add_cap( $cap );
				$force_refresh = TRUE;
			}
		}
	}
	if ( $force_refresh AND current_user_can( 'manage_options' ) AND ! isset( $_COOKIE['us_cap_page_refreshed'] ) ) {
		// To prevent infinite refreshes when the DB is not writable
		setcookie( 'us_cap_page_refreshed' );
		header( 'Refresh: 0' );
	}
}

if ( strpos( $portfolio_slug, '%us_portfolio_category%' ) !== FALSE ) {
	function us_portfolio_link( $post_link, $id = 0 ) {
		$post = get_post( $id );
		if ( is_object( $post ) ) {
			$terms = wp_get_object_terms( $post->ID, 'us_portfolio_category' );
			if ( $terms ) {
				return str_replace( '%us_portfolio_category%', $terms[0]->slug, $post_link );
			}
		}

		return $post_link;
	}

	add_filter( 'post_type_link', 'us_portfolio_link', 1, 3 );
} elseif ( $portfolio_slug == '' ) {
	function us_portfolio_remove_slug( $post_link, $post, $leavename ) {
		if ( 'us_portfolio' != $post->post_type || 'publish' != $post->post_status ) {
			return $post_link;
		}
		$post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

		return $post_link;
	}

	add_filter( 'post_type_link', 'us_portfolio_remove_slug', 10, 3 );

	function us_portfolio_parse_request( $query ) {
		if ( ! $query->is_main_query() || 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
			return;
		}
		if ( ! empty( $query->query['name'] ) ) {
			$query->set( 'post_type', array( 'post', 'us_portfolio', 'page' ) );
		}
	}

	add_action( 'pre_get_posts', 'us_portfolio_parse_request' );
}

// Add Default Footer on theme activation
function us_add_default_footer() {
	$footer_id = us_get_option( 'footer_id', NULL );

	if ( ! empty( $footer_id ) ) {
		$args = array(
			'name' => $footer_id,
			'post_type' => 'us_footer',
			'post_status' => 'publish',
			'numberposts' => 1,
		);

		$footer_post = get_posts( $args );
		if ( $footer_post ) {
			return FALSE;
		}
	}

	$footer_content = '[vc_row color_scheme="footer-bottom"][vc_column][vc_column_text]<p style="text-align: center;">&copy; Copyright text goes here</p>[/vc_column_text][/vc_column][/vc_row]';
	$footer_post_array = array(
		'post_type' => 'us_footer',
		'post_date' => date( 'Y-m-d H:i', time() ),
		'post_name' => 'default-footer',
		'post_title' => __( 'Default Footer', 'us' ),
		'post_content' => $footer_content,
		'post_status' => 'publish',
	);

	$footer_post_id = wp_insert_post( $footer_post_array );

	global $usof_options;
	usof_load_options_once();
	$updated_options = $usof_options;

	$updated_options['footer_id'] = 'default-footer';

	$updated_options = array_merge( usof_defaults(), $updated_options );

	usof_save_options( $updated_options );
}

// Adding needed filters to footer content
add_filter( 'us_footer_the_content', 'wptexturize' );
add_filter( 'us_footer_the_content', 'wpautop' );
add_filter( 'us_footer_the_content', 'shortcode_unautop' );
add_filter( 'us_footer_the_content', 'wp_make_content_images_responsive' );
add_filter( 'us_footer_the_content', 'do_shortcode', 12 );
add_filter( 'us_footer_the_content', 'convert_smilies', 20 );
