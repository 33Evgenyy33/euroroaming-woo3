<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Retrieves and returns the part of current post that can be used as the post's preview.
 *
 * (!) Should be called in WP_Query fetching loop only.
 *
 * @param string $the_content            Post content, retrieved with get_the_content() (without 'the_content' filters)
 * @param bool   $strip_from_the_content Should the found element be removed from post content not to be duplicated?
 *
 * @return string
 */

function us_get_post_preview( &$the_content, $strip_from_the_content = FALSE ) {
	// Retreiving post format
	$post_format = get_post_format() ? get_post_format() : 'standard';
	$preview_html = '';

	global $us_blog_img_ratio;
	if ( ! empty( $us_blog_img_ratio ) ) {
		$video_h_style = ' style="padding-bottom:' . $us_blog_img_ratio . '%;"';
	} else {
		$video_h_style = '';
	}

	// Retrieving post preview
	if ( $post_format == 'image' ) {
		if ( preg_match( "%<img.+?>%", $the_content, $matches ) ) {
			// Using first inner image
			$preview_html = $matches[0];
			if ( $strip_from_the_content ) {
				$the_content = str_replace( $matches[0], '', $the_content );
			}
		} elseif ( preg_match( '~(https?(?://([^/?#]*))?([^?#]*?\.(?:jpe?g|gif|png)))~', $the_content, $matches ) ) {
			// Using first image link
			$preview_html = '<img src="' . $matches[0] . '" alt="">';
			if ( $strip_from_the_content ) {
				$the_content = str_replace( $matches[0], '', $the_content );
			}
		}
	} elseif ( $post_format == 'gallery' ) {
		if ( preg_match( '~\[us_gallery.+?\]|\[us_image_slider.+?\]|\[gallery.+?\]~', $the_content, $matches ) ) {

			// Replacing with a simple image slider
			$gallery = preg_replace( '~(vc_gallery|us_gallery|gallery)~', 'us_image_slider', $matches[0] );

			global $blog_listing_slider_size;
			if ( ! empty( $blog_listing_slider_size ) ) {
				if ( preg_match( '~layout=\"[a-z]+\"~', $gallery ) ) {
					$gallery = preg_replace( '~img_size=\"[a-z]+\"~', 'img_size="' . $blog_listing_slider_size . '"', $gallery );
				} else {
					$gallery = str_replace( '[us_image_slider', '[us_image_slider img_size="' . $blog_listing_slider_size . '"', $gallery );
				}

			}
			$preview_html = do_shortcode( $gallery );

			if ( $strip_from_the_content ) {
				$the_content = str_replace( $matches[0], '', $the_content );
			}
		}
	} elseif ( $post_format == 'video' ) {
		$post_content = preg_replace( '~^\s*(https?://[^\s"]+)\s*$~im', "[embed]$1[/embed]", $the_content );

		if ( preg_match( '~\[embed.+?\]|\[vc_video.+?\]~', $post_content, $matches ) ) {

			global $wp_embed;
			$video = $matches[0];
			$preview_html = do_shortcode( $wp_embed->run_shortcode( $video ) );
			if ( strpos( $preview_html, 'w-video' ) === FALSE ) {
				$preview_html = '<div class="w-video"><div class="w-video-h"' . $video_h_style . '>' . $preview_html . '</div></div>';
			}
			$post_content = str_replace( $matches[0], "", $post_content );
		}


		if ( ! empty( $preview_html ) AND $strip_from_the_content ) {
			$the_content = $post_content;
		}
	} elseif ( $post_format == 'audio' ) {
		$post_content = preg_replace( '~^\s*(https?://[^\s"]+)\s*$~im', "[embed]$1[/embed]", $the_content );

		if ( preg_match( '~\[audio.+?\]\[\/audio\]~', $post_content, $matches ) ) {
			$audio = $matches[0];
			$preview_html = do_shortcode( $audio );

			$post_content = str_replace( $matches[0], "", $post_content );
		} elseif ( preg_match( '~\[embed.+?\]~', $post_content, $matches ) ) {

			global $wp_embed;
			$video = $matches[0];
			$preview_html = do_shortcode( $wp_embed->run_shortcode( $video ) );
			if ( strpos( $preview_html, 'w-video' ) === FALSE ) {
				$preview_html = '<div class="w-video"><div class="w-video-h"' . $video_h_style . '>' . $preview_html . '</div></div>';
			}
			$post_content = str_replace( $matches[0], "", $post_content );
		}

		if ( ! empty( $preview_html ) AND $strip_from_the_content ) {
			$the_content = $post_content;
		}
	}

	$preview_html = apply_filters( 'us_get_post_preview', $preview_html, get_the_ID() );

	return $preview_html;
}

/**
 * Get URL for link post format
 *
 * @param            $the_content
 * @param bool|FALSE $strip_from_the_content
 */
function us_get_post_format_link_url( $url, $post ) {

	if ( get_post_format( $post->ID ) != 'link' ) {
		return $url;
	}

	$post_content = $post->post_content;
	$link = '';

	if ( preg_match( '$(https?|ftp|file)://[-A-Z0-9+&@#/%?=~_|!:,.;]*[-A-Z0-9+&@#/%=~_|]$i', $post_content, $matches ) ) {
		$link = $matches[0];
	} else {

	}

	if ( $link != '' ) {
		//$post->post_content = str_replace( $link, "", $post->post_content );
		return $link;
	}

	return $url;
}

add_filter( 'post_link', 'us_get_post_format_link_url', 10, 3 );

/**
 * @var array The list of portfolio pages that are not supposed to have their own pages (external links or lightboxes)
 */
global $us_post_prevnext_exclude_ids;

/**
 * Get information about previous and next post or page (should be used in singular element context)
 *
 * @return array
 */
function us_get_post_prevnext() {

	// TODO Create for singular pages https://codex.wordpress.org/Next_and_Previous_Links#The_Next_and_Previous_Pages
	$result = array();
	if ( is_singular( 'us_portfolio' ) ) {
		global $us_post_prevnext_exclude_ids;
		if ( $us_post_prevnext_exclude_ids === NULL ) {
			// Getting the list of portfolio pages with custom links
			global $wpdb;
			$wpdb_query = 'SELECT `post_id` FROM `' . $wpdb->postmeta . '` ';
			$wpdb_query .= 'WHERE (`meta_key`=\'us_tile_link\' AND (`meta_value`!=\'\' AND `meta_value` NOT LIKE \'%"url":""%\'))';
			$us_post_prevnext_exclude_ids = apply_filters( 'us_get_post_prevnext_exclude_ids', $wpdb->get_col( $wpdb_query ) );
			if ( ! empty( $us_post_prevnext_exclude_ids ) ) {
				add_filter( 'get_next_post_where', 'us_exclude_hidden_portfolios_from_prevnext' );
				add_filter( 'get_previous_post_where', 'us_exclude_hidden_portfolios_from_prevnext' );
			}
		}
		$in_same_term = ! ! us_get_option( 'portfolio_nav_category' );
		$next_post = get_next_post( $in_same_term, '', 'us_portfolio_category' );
		$prev_post = get_previous_post( $in_same_term, '', 'us_portfolio_category' );
	} else {
		global $us_post_prevnext_exclude_ids;
		if ( $us_post_prevnext_exclude_ids === NULL ) {
			global $wpdb;
			$wpdb_query = 'SELECT `object_id` FROM `' . $wpdb->terms . '`, `' . $wpdb->term_relationships . '` ';
			$wpdb_query .= 'WHERE ((`slug`=\'post-format-quote\' OR `slug`=\'post-format-link\') AND `term_id`=`term_taxonomy_id`)';
			$us_post_prevnext_exclude_ids = apply_filters( 'us_get_post_prevnext_exclude_ids', $wpdb->get_col( $wpdb_query ) );
			if ( ! empty( $us_post_prevnext_exclude_ids ) ) {
				add_filter( 'get_next_post_where', 'us_exclude_hidden_portfolios_from_prevnext' );
				add_filter( 'get_previous_post_where', 'us_exclude_hidden_portfolios_from_prevnext' );
			}
		}
		$in_same_term = ! ! us_get_option( 'post_nav_category' );
		$next_post = get_next_post( $in_same_term, '', 'category' );
		$prev_post = get_previous_post( $in_same_term, '', 'category' );
	}

	if ( ! empty( $prev_post ) ) {
		$result['prev'] = array(
			'id' => $prev_post->ID,
			'link' => get_permalink( $prev_post->ID ),
			'title' => get_the_title( $prev_post->ID ),
			'meta' => us_translate( 'Previous Post' ),
		);
	}
	if ( ! empty( $next_post ) ) {
		$result['next'] = array(
			'id' => $next_post->ID,
			'link' => get_permalink( $next_post->ID ),
			'title' => get_the_title( $next_post->ID ),
			'meta' => us_translate( 'Next Post' ),
		);
	}

	return $result;
}

function us_exclude_hidden_portfolios_from_prevnext( $where ) {
	global $us_post_prevnext_exclude_ids;
	$where .= ' AND p.ID NOT IN (' . implode( ',', $us_post_prevnext_exclude_ids ) . ')';

	return $where;
}

// Display specific page when Maintenance Mode is enabled in Theme Options
add_action( 'init', 'us_maintenance_mode' );
function us_maintenance_mode() {
	if ( is_user_logged_in() ) {
		add_action( 'admin_bar_menu', 'us_maintenance_admin_bar_menu', 1000 );
		return FALSE;
	}
	if ( us_get_option( 'maintenance_mode' ) AND us_get_option( 'maintenance_page' ) ) {
		$maintenance_page = get_page_by_path( us_get_option( 'maintenance_page' ) );
		if ( $maintenance_page ) {
			if ( function_exists( 'bp_is_active' ) ) {
				add_action( 'template_redirect', 'us_display_maintenance_page', 9 );
			} else {
				add_action( 'template_redirect', 'us_display_maintenance_page' );
			}
		}
	}
}

function us_maintenance_admin_bar_menu( ) {
	global $wp_admin_bar;

	if ( us_get_option( 'maintenance_mode' ) AND us_get_option( 'maintenance_page' ) ) {
		$maintenance_page = get_page_by_path( us_get_option( 'maintenance_page' ) );
		if ( $maintenance_page ) {

			$wp_admin_bar->add_node(
				array(
					'id' => 'us-maintenance-notice',
					'href' => admin_url() . 'admin.php?page=us-theme-options',
					'title' => __( 'Maintenance Mode', 'us' ),
					'meta' => array(
						'class' => 'us-maintenance',
						'html' => '<style>.us-maintenance a{font-weight:600!important;color:#f90!important;}</style>',
					),
				)
			);
		}
	}
}
function us_display_maintenance_page( ) {
	$maintenance_page = get_page_by_path( us_get_option( 'maintenance_page' ) );
	if ( $maintenance_page ) {
		us_open_wp_query_context();
		global $wp_query;
		$wp_query = new WP_Query(
			array(
				'p' => $maintenance_page->ID,
				'post_type' => 'page',
			)
		);
		the_post();

		if ( us_get_option( 'maintenance_503', 1 ) == 1 ) {
			header( 'HTTP/1.1 503 Service Temporarily Unavailable' );
			header( 'Status: 503 Service Temporarily Unavailable' );
			header( 'Retry-After: 86400' ); // retry in a day
		}

		$us_layout = US_Layout::instance();
		$us_layout->sidebar_pos = 'none';
		$us_layout->header_show = 'never';

		get_header();
?>
<div class="l-main">
	<div class="l-main-h i-cf">
		<main class="l-content"<?php echo ( us_get_option( 'schema_markup' ) ) ? ' itemprop="mainContentOfPage"' : ''; ?>>
			<?php
			do_action( 'us_before_page' );

			$the_content = apply_filters( 'the_content', $maintenance_page->post_content );

			// The page may be paginated itself via <!--nextpage--> tags
			$pagination = us_wp_link_pages(
				array(
					'before' => '<div class="w-blog-pagination"><nav class="navigation pagination">',
					'after' => '</nav></div>',
					'next_or_number' => 'next_and_number',
					'nextpagelink' => '>',
					'previouspagelink' => '<',
					'link_before' => '<span>',
					'link_after' => '</span>',
					'echo' => 0,
				)
			);

			// If content has no sections, we'll create them manually
			$has_own_sections = ( strpos( $the_content, ' class="l-section' ) !== FALSE );
			if ( ! $has_own_sections ) {
				$the_content = '<section class="l-section"><div class="l-section-h i-cf">' . $the_content . $pagination . '</div></section>';
			} elseif ( ! empty( $pagination ) ) {
				$the_content .= '<section class="l-section"><div class="l-section-h i-cf">' . $pagination . '</div></section>';
			}

			echo $the_content;

			do_action( 'us_after_page' );
			?>
		</main>
	</div>
</div>
<?php
		global $us_hide_footer;
		$us_hide_footer = TRUE;

		get_footer();
		us_close_wp_query_context();
		exit();
	}
}
