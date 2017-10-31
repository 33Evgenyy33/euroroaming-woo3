<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_blog
 *
 * Listing of blog posts.
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $atts           array Shortcode attributes
 *
 * @param $atts           ['layout'] string Blog layout: 'classic' / 'smallcircle' / 'smallsquare' / 'flat' / 'compact' / 'latest'
 * @param $atts           ['img_size'] string post thumbnails image size
 * @param $atts           ['columns'] int Columns quantity
 * @param $atts           ['type'] strubg layout type: 'grid' / 'masonry' / 'carousel'
 * @param $atts           ['content_type'] string Content type: 'excerpt' / 'content' / 'none'
 * @param $atts           ['pagination'] string Pagination type: 'none' / 'regular' / 'ajax' / 'infinite'
 * @param $atts           ['ignore_sticky'] bool Ignore sticky posts
 * @param $atts           ['categories'] string Comma-separated list of categories slugs to filter the posts
 * @param $atts           ['orderby'] string Posts order: 'date' / 'rand'
 * @param $atts           ['show_date'] bool
 * @param $atts           ['show_author'] bool
 * @param $atts           ['show_categories'] bool
 * @param $atts           ['show_tags'] bool
 * @param $atts           ['show_comments'] bool
 * @param $atts           ['show_read_more'] bool
 * @param $atts           ['items'] int Number of items per page
 * @param $atts           ['title_size'] string Posts Title Size
 * @param $atts           ['title_size_mobiles'] string Posts Title Size on Mobiles
 * @param $atts           ['el_class'] string Extra class name
 * @param $atts           ['carousel_arrows'] bool used in Carousel type
 * @param $atts           ['carousel_dots'] bool used in Carousel type
 * @param $atts           ['carousel_center'] bool used in Carousel type
 * @param $atts           ['carousel_autoplay'] bool used in Carousel type
 * @param $atts           ['carousel_interval'] int used in Carousel type
 * @param $atts           ['carousel_slideby'] int used in Carousel type
 * @param $atts           ['filter'] string Filter type: 'none' / 'category'
 * @param $atts           ['filter_style'] string Filter Bar style: 'style_1' / 'style_2' / ... / 'style_N'
 */

$atts = us_shortcode_atts( $atts, 'us_blog' );

$metas = array();
foreach ( array( 'date', 'author', 'categories', 'tags', 'comments' ) as $meta_key ) {
	if ( $atts['show_' . $meta_key] ) {
		$metas[] = $meta_key;
	}
}

// Preparing query
$query_args = array(
	'post_type' => 'post',
);

// Providing proper post statuses
$query_args['post_status'] = array( 'publish' => 'publish' );
$query_args['post_status'] += (array) get_post_stati( array( 'public' => TRUE ) );
// Add private states if user is capable to view them
if ( is_user_logged_in() AND current_user_can( 'read_private_posts' ) ) {
	$query_args['post_status'] += (array) get_post_stati( array( 'private' => TRUE ) );
}
$query_args['post_status'] = array_values( $query_args['post_status'] );

if ( ! empty( $atts['categories'] ) ) {
	$query_args['category_name'] = $atts['categories'];
}
if ( ! empty( $atts['ignore_sticky'] ) AND $atts['ignore_sticky'] ) {
	$query_args['ignore_sticky_posts'] = 1;
}

// Setting posts order
$orderby_translate = array(
	'date' => 'date',
	'date_asc' => 'date',
	'alpha' => 'title',
	'rand' => 'rand',
);
$order_translate = array(
	'date' => 'DESC',
	'date_asc' => 'ASC',
	'alpha' => 'ASC',
	'rand' => '',
);
$orderby = ( in_array( $atts['orderby'], array( 'date', 'date_asc', 'alpha', 'rand' ) ) ) ? $atts['orderby'] : 'date';
if ( $orderby == 'rand' ) {
	$query_args['orderby'] = 'rand';
} else {
	$query_args['orderby'] = array(
		$orderby_translate[$orderby] => $order_translate[$orderby],
	);
}


// Posts per page
$atts['items'] = max( 0, intval( $atts['items'] ) );
if ( $atts['items'] > 0 ) {
	$query_args['posts_per_page'] = $atts['items'];
}

// Current page
if ( $atts['pagination'] == 'regular' ) {
	$request_paged = is_front_page() ? 'page' : 'paged';
	if ( get_query_var( $request_paged ) ) {
		$query_args['paged'] = get_query_var( $request_paged );
	}
}


$template_vars = array(
	'query_args' => $query_args,
	'layout' => $atts['layout'],
	'img_size' => $atts['img_size'],
	'type' => $atts['type'],
	'columns' => $atts['columns'],
	'content_type' => $atts['content_type'],
	'metas' => $metas,
	'show_read_more' => ! ! $atts['show_read_more'],
	'pagination' => $atts['pagination'],
	'title_size' => $atts['title_size'],
	'el_class' => $atts['el_class'],
	'carousel_arrows' => $atts['carousel_arrows'],
	'carousel_dots' => $atts['carousel_dots'],
	'carousel_center' => $atts['carousel_center'],
	'carousel_autoplay' => $atts['carousel_autoplay'],
	'carousel_interval' => $atts['carousel_interval'],
	'carousel_slideby' => $atts['carousel_slideby'],
	'filter' => $atts['filter'],
	'filter_style' => $atts['filter_style'],
	'categories' => $atts['categories'],
);
us_load_template( 'templates/blog/listing', $template_vars );
