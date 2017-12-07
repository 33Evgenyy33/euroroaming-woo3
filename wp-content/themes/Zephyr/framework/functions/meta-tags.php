<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

add_action( 'wp_head', 'us_output_meta_tags', 5 );
function us_output_meta_tags() {
	// Some of the tags might be defined previously
	global $us_meta_tags;
	$us_meta_tags = apply_filters( 'us_meta_tags', isset( $us_meta_tags ) ? $us_meta_tags : array() );

	// Some must-have general tags
	if ( ! isset( $us_meta_tags['viewport'] ) ) {
		$us_meta_tags['viewport'] = 'width=device-width';
		if ( us_get_option( 'responsive_layout' ) ) {
			$us_meta_tags['viewport'] .= ', initial-scale=1';
		}
		$us_meta_tags['viewport'] = apply_filters( 'us_meta_viewport', $us_meta_tags['viewport'] );
	}
	if ( ! isset( $us_meta_tags['SKYPE_TOOLBAR'] ) ) {
		$us_meta_tags['SKYPE_TOOLBAR'] = 'SKYPE_TOOLBAR_PARSER_COMPATIBLE';
	}

	// Open Graph meta tags when needed
	if ( us_get_option( 'og_enabled' ) AND is_singular() AND isset( $GLOBALS['post'] ) ) {
		if ( ! isset( $us_meta_tags['og:title'] ) ) {
			$us_meta_tags['og:title'] = get_the_title();
		}
		if ( ! isset( $us_meta_tags['og:type'] ) ) {
			$us_meta_tags['og:type'] = 'website';
		}
		if ( ! isset( $us_meta_tags['og:url'] ) ) {
			$us_meta_tags['og:url'] = site_url( $_SERVER['REQUEST_URI'] );
		}
		if ( ! isset( $us_meta_tags['og:image'] ) ) {
			if  ( $the_post_thumbnail_id = get_post_thumbnail_id() AND $the_post_thumbnail_src = wp_get_attachment_image_src( $the_post_thumbnail_id, 'large' ) ) {
				$us_meta_tags['og:image'] = $the_post_thumbnail_src[0];
			} elseif ( $meta_image = get_post_meta( get_the_ID(), 'us_og_image' ) AND ! empty( $meta_image[0] ) ) {
				$us_meta_tags['og:image'] = $meta_image[0];
			}
		}
		if ( ! isset( $us_meta_tags['og:description'] ) AND has_excerpt() AND ( $the_excerpt = get_the_excerpt() ) ) {
			$us_meta_tags['og:description'] = $the_excerpt;
		}
	}

	// Outputting the tags
	if ( isset( $us_meta_tags ) AND is_array( $us_meta_tags ) ) {
		foreach ( $us_meta_tags as $meta_name => $meta_content ) {
			echo '<meta name="' . esc_attr( $meta_name ) . '" content="' . esc_attr( $meta_content ) . '">' . "\n";
		}
	}
}

add_action( 'save_post', 'us_save_post_add_og_image' );
function us_save_post_add_og_image( $post_id ) {
	// If the post has thumbnail - clear og_image meta data, in other case try to find an image inside post content
	if  ( $the_post_thumbnail_id = get_post_thumbnail_id( $post_id ) AND $the_post_thumbnail_src = wp_get_attachment_image_src( $the_post_thumbnail_id, 'large' ) ) {
		update_post_meta( $post_id, 'us_og_image', '' );
	} else {
		$post = get_post( $post_id );
		$the_content = $post->post_content;
		$the_content = apply_filters( 'the_content', $the_content );
		if ( preg_match('/<img [^>]*src=["|\']([^"|\']+)/i', $the_content, $matches) ) {
			update_post_meta( $post_id, 'us_og_image', $matches[1] );
		} else {
			update_post_meta( $post_id, 'us_og_image', '' );
		}
	}
}
