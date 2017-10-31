<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output a single us_portfolio listing. Universal template that is used by all the possible us_portfolio posts listings.
 *
 * @var $categories   string Comma-separated list of categories slugs to show
 * @var $type         string layout type: 'grid' / 'masonry' / 'carousel'
 * @var $style_name   string Items style: 'style_1' / 'style_2' / ... / 'style_N'
 * @var $columns      int Columns quantity
 * @var $ratio        string Items ratio: '3x2' / '4x3' / '1x1' / '2x3' / '3x4' / '16x9'
 * @var $metas        array Meta data that should be shown: array( 'title', 'date', 'categories' )
 * @var $align        string Content alignment: 'left' / 'center' / 'right'
 * @var $with_indents bool Items have indents?
 * @var $pagination   string Pagination type: 'regular' / 'none' / 'ajax' / 'infinite'
 * @var $orderby      string Order type: 'date' / 'rand'
 * @var $order        string Order direction 'desc' / 'asc'
 * @var $perpage      int Items per page (if 0, will output all the items)
 * @var $page         int If paginated, determines the current page number
 * @var $is_widget    bool if used in widget
 * @var $title_size   string Title Font Size
 * @var $meta_size    string Meta Font Size
 * @var $text_color   string
 * @var $bg_color     string
 * @var $img_size     string
 * @var $carousel_arrows       bool used in Carousel type
 * @var $carousel_dots         bool used in Carousel type
 * @var $carousel_center       bool used in Carousel type
 * @var $carousel_autoplay     bool used in Carousel type
 * @var $carousel_interval     bool used in Carousel type
 * @var $filter       string Filter type: 'none' / 'category'
 * @var $filter_style string Filter Bar style: 'style_1' / 'style_2' / ... / 'style_N'
 * @var $el_class     string Additional classes that will be appended to the main .w-portfolio container
 *
 * @action Before the template: 'us_before_template:templates/portfolio/listing'
 * @action After the template: 'us_after_template:templates/portfolio/listing'
 * @filter Template variables: 'us_template_vars:templates/portfolio/listing'
 */

// Global preloader type
$preloader_type = us_get_option( 'preloader' );
if ( ! in_array( $preloader_type, us_get_preloader_numeric_types() ) ) {
	$preloader_type = 1;
}
$data_atts = ' data-preloader_type="' . intval( $preloader_type ) . '"';

// portfolio grid additional variables
$classes = $list_classes = $inner_css = '';

$type = isset( $type ) ? $type : 'grid';
$classes .= ' type_' . $type;

$is_widget = ( isset( $is_widget ) ) ? $is_widget : FALSE;

if ( ! $is_widget ) {
	$style_name = isset( $style_name ) ? $style_name : 'style_1';
	$classes .= ' ' . $style_name;
}

$title_size = isset( $title_size ) ? $title_size : NULL;
$meta_size = isset( $meta_size ) ? $meta_size : NULL;
$text_color = isset( $text_color ) ? $text_color : NULL;
$bg_color = isset( $bg_color ) ? $bg_color : NULL;

$columns = isset( $columns ) ? intval( $columns ) : 3;
if ( $columns < 1 OR $columns > 6 ) {
	$columns = 3;
}
if ( $columns != 1 ) {
	$classes .= ' cols_' . $columns;
}

$align = isset( $align ) ? $align : 'left';
$classes .= ' align_' . $align;

$available_ratios = array( '3x2', '4x3', '1x1', '2x3', '3x4', '16x9' );
$ratio = ( isset( $ratio ) AND in_array( $ratio, $available_ratios ) ) ? $ratio : '3x2';
$classes .= ' ratio_' . str_replace( ':', '-', $ratio );

$available_metas = array( 'title', 'date', 'categories', 'desc' );
$metas = ( isset( $metas ) AND is_array( $metas ) ) ? array_intersect( $metas, $available_metas ) : array( 'title' );

$with_indents = ( isset( $with_indents ) AND $with_indents );
if ( $with_indents ) {
	$classes .= ' with_indents';
}

if ( ! empty( $items_action ) AND $items_action == 'lightbox_page' ) {
	$classes .= ' lightbox_page';
}

// Preparing query
$query_args = array(
	'post_type' => 'us_portfolio',
	'post_status' => 'publish',
);

// Exclude the current page from listing
if ( is_singular( 'us_portfolio' ) ) {
	$current_ID = get_the_ID();
	if ( ! empty( $current_ID ) ) {
		$query_args['post__not_in'] = array( $current_ID );
	}
}

// Show only items from the certain categories
$categories = ( isset( $categories ) AND ! empty( $categories ) ) ? array_filter( explode( ',', $categories ) ) : array();
if ( ! empty( $categories ) ) {
	$query_args['us_portfolio_category'] = implode( ',', $categories );
}

// Setting items order
$orderby_translate = array(
	'date' => 'date',
	'date_asc' => 'date',
	'alpha' => 'title',
	'rand' => 'rand',
);
$orderby_translate_sql = array(
	'date' => '`post_date`',
	'date_asc' => '`post_date`',
	'alpha' => '`post_title`',
	'rand' => 'RAND()',
);
$order_translate = array(
	'date' => 'DESC',
	'date_asc' => 'ASC',
	'alpha' => 'ASC',
	'rand' => '',
);
$orderby = ( isset( $orderby ) AND in_array(
		$orderby, array(
		'date',
		'date_asc',
		'alpha',
		'rand',
	)
	) ) ? $orderby : 'date';
$order = ( isset( $order ) AND $order == 'ASC' ) ? 'ASC' : 'DESC';
if ( $orderby == 'rand' ) {
	$query_args['orderby'] = 'rand';
} else/*if ( $atts['order_by'] == 'date' )*/ {
	$query_args['orderby'] = array(
		$orderby_translate[$orderby] => $order_translate[$orderby],
	);
}

// Posts per page
$pagination = isset( $pagination ) ? $pagination : 'none';
$has_pagination = ( $pagination != 'none' );
if ( $pagination == 'infinite' ) {
	$is_infinite = TRUE;
	$pagination = 'ajax';
}
$perpage = isset( $perpage ) ? intval( $perpage ) : 0;
$page = isset( $page ) ? max( 1, intval( $page ) ) : 1;
if ( $perpage < 1 ) {
	$query_args['nopaging'] = TRUE;
	$has_pagination = FALSE;
} else {
	$query_args['posts_per_page'] = $perpage;
	$query_args['paged'] = $page;
}

// Grabbing all the categories with a single request
global $wpdb;
$wpdb_query = 'SELECT `terms`.`slug` AS `category_slug`, `terms`.`name` AS `category_name`, `term_relationships`.`object_id` ';
$wpdb_query .= 'FROM `' . $wpdb->term_taxonomy . '` as `term_taxonomy`, `' . $wpdb->terms . '` as `terms`, `' . $wpdb->term_relationships . '` AS `term_relationships` ';
if ( class_exists( 'SitePress' ) AND defined( 'ICL_LANGUAGE_CODE' ) AND ICL_LANGUAGE_CODE ) {
	$wpdb_query .= ', `' . $wpdb->prefix . 'icl_translations` AS `translations` ';
}
$wpdb_query .= 'WHERE `term_taxonomy`.`taxonomy` = \'us_portfolio_category\' AND `terms`.`term_id` = `term_taxonomy`.`term_id`';
if ( class_exists( 'SitePress' ) AND defined( 'ICL_LANGUAGE_CODE' ) AND ICL_LANGUAGE_CODE ) {
	$wpdb_query .= ' AND `translations`.element_id = `term_relationships`.`object_id`  AND `translations`.`language_code` = \'' . ICL_LANGUAGE_CODE . '\'';
}
if ( ! empty( $categories ) ) {
	$wpdb_query .= ' AND `terms`.`slug` IN (\'' . implode( '\',\'', array_map( 'esc_sql', $categories ) ) . '\')';
}
$wpdb_query .= ' AND `term_relationships`.`term_taxonomy_id` = `term_taxonomy`.`term_taxonomy_id`';
// Categories slugs for all the portfolio pages that may be shown by the element
$items_categories = array();
// Category names for all the slugs
$categories_names = array();
foreach ( $wpdb->get_results( $wpdb_query ) as $row ) {
	if ( ! isset( $items_categories[$row->object_id] ) ) {
		$items_categories[$row->object_id] = array();
	}
	$items_categories[$row->object_id][] = $row->category_slug;
	if ( ! isset( $categories_names[$row->category_slug] ) ) {
		$categories_names[$row->category_slug] = $row->category_name;
	}
}
if ( empty( $items_categories ) ) {
	if ( ! empty( $categories ) ) {
		// Nothing is found in the needed categories
		return;
	} else {
		// Very unlikely, but still: portfolio posts may be not attached to categories, so fetching them the other way
		// TODO Rewrite the whole algorithm in a more lean way
		us_open_wp_query_context();
		foreach ( get_posts( array( 'post_type' => 'us_portfolio', 'numberposts' => - 1 ) ) as $post ) {
			$items_categories[$post->ID] = array();
		}
		us_close_wp_query_context();
	}
}

if ( $has_pagination AND count( $items_categories ) <= $perpage ) {
	$has_pagination = FALSE;
}

// Obtaining tiles sizes for proper
$tile_sizes = array();
if ( count( array_keys( $items_categories ) ) > 0 ) {
	$items_ids = implode( ',', array_keys( $items_categories ) );
	// Grabbing information about non-standard tile sizes to show them properly from the very beginning
	$wpdb_query = 'SELECT `post_id`, `meta_value` FROM `' . $wpdb->postmeta . '` ';
	$wpdb_query .= 'WHERE `post_id` IN (' . $items_ids . ') AND `meta_key`=\'us_tile_size\' AND `meta_value` NOT IN (\'\', \'1x1\')';
	foreach ( $wpdb->get_results( $wpdb_query ) as $result ) {
		$tile_sizes[$result->post_id] = $result->meta_value;
	}
}

if ( $has_pagination ) {
	// We count the element order for the various cases at the very beginning for complex ajax algorithms
	$wpdb_query = 'SELECT `ID` FROM `' . $wpdb->posts . '` ';
	$wpdb_query .= 'WHERE `ID` IN (' . $items_ids . ') AND `post_type`=\'us_portfolio\' AND `post_status`=\'publish\' ';
	$wpdb_query .= 'ORDER BY ' . $orderby_translate_sql[$orderby] . ' ' . $order_translate[$orderby];
	$tile_order = array(
		'*' => array_map( 'absint', $wpdb->get_col( $wpdb_query ) ),
	);
	if ( ! empty( $categories_names ) AND $filter == 'category' ) {
		$tile_order = array_merge( $tile_order, array_fill_keys( array_keys( $categories_names ), array() ) );
		foreach ( $tile_order['*'] as $elm_id ) {
			foreach ( $items_categories[$elm_id] AS $category_slug ) {
				$tile_order[$category_slug][] = $elm_id;
			}
		}
	}

	// Overloading the query by selecting the certain IDs
	$query_args['post__in'] = $tile_order['*'];
	$query_args['orderby'] = 'post__in';
}

us_open_wp_query_context();
global $wp_query;
$wp_query = new WP_Query( $query_args );
if ( ! have_posts() ) {
	echo us_translate( 'No pages found.' );
	return;
}

$available_filter_styles = array( 'style_1', 'style_2', 'style_3' );
$filter_style = ( isset( $filter_style ) AND in_array( $filter_style, $available_filter_styles ) ) ? $filter_style : 'style_1';

$filter_html = '';
$filter = isset( $filter ) ? $filter : 'none';
if ( $filter == 'category' AND $type != 'carousel' ) {
	// $categories_names already contains only the used categories
	if ( count( $categories_names ) > 1 ) {
		$classes .= ' with_filters';
		$filter_html .= '<div class="g-filters ' . $filter_style . '"><div class="g-filters-list">';
		$filter_html .= '<div class="g-filters-item active" data-category="*"><span>' . __( 'All', 'us' ) . '</span></div>';
		$all_categories = get_terms( array( 'taxonomy' => 'us_portfolio_category' ) );
		foreach ( $all_categories as $category ) {
			if ( isset( $categories_names[$category->slug] ) ) {
				$filter_html .= '<div class="g-filters-item" data-category="' . $category->slug . '"><span>' . $category->name . '</span></div>';
			}
		}
		$filter_html .= '</div></div>';
	}
}

if ( ( ! $is_widget ) AND ( ! empty( $filter_html ) OR $has_pagination OR $type == 'masonry' OR (  ! empty( $tile_sizes ) AND $columns > 1 ) ) ) {
	// We'll need the isotope script for any of the above cases
	if ( us_get_option( 'ajax_load_js', 0 ) == 0 ) {
		wp_enqueue_script( 'us-isotope' );
	}
	$classes .= ' with_isotope';
}

if ( $type == 'carousel' ) {
	// We need owl script for this
	if ( us_get_option( 'ajax_load_js', 0 ) == 0 ) {
		wp_enqueue_script( 'us-owl' );
	}
	$data_atts .= ' data-breakpoint_1_cols="' . us_get_option( 'portfolio_breakpoint_1_cols' ) . '"';
	$data_atts .= ' data-breakpoint_1_width="' . us_get_option( 'portfolio_breakpoint_1_width' ) . '"';
	$data_atts .= ' data-breakpoint_2_cols="' . us_get_option( 'portfolio_breakpoint_2_cols' ) . '"';
	$data_atts .= ' data-breakpoint_2_width="' . us_get_option( 'portfolio_breakpoint_2_width' ) . '"';
	$data_atts .= ' data-breakpoint_3_cols="' . us_get_option( 'portfolio_breakpoint_3_cols' ) . '"';
	$data_atts .= ' data-breakpoint_3_width="' . us_get_option( 'portfolio_breakpoint_3_width' ) . '"';

	$data_atts .= ' data-items="' . $columns . '"';
	$data_atts .= ' data-nav="' . intval( ! ! $carousel_arrows ) . '"';
	$data_atts .= ' data-dots="' . intval( ! ! $carousel_dots ) . '"';
	$data_atts .= ' data-center="' . intval( ! ! $carousel_center ) . '"';
	$data_atts .= ' data-autoplay="' . intval( ! ! $carousel_autoplay ) . '"';
	$data_atts .= ' data-timeout="' . intval( $carousel_interval * 1000 ) . '"';
	if ( $carousel_slideby ) {
		$data_atts .= ' data-slideby="page"';
	} else {
		$data_atts .= ' data-slideby="1"';
	}

	$list_classes = ' owl-carousel';
}

$el_class = isset( $el_class ) ? $el_class : '';
if ( ! empty( $el_class ) ) {
	$classes .= ' ' . $el_class;
}

$classes = apply_filters( 'us_portfolio_listing_classes', $classes );
?>
	<div class="w-portfolio<?php echo $classes ?>"><?php echo $filter_html; ?>
	<div class="w-portfolio-list<?php echo $list_classes; ?>"<?php echo $data_atts; ?>><?php

// Preparing template settings for loop post template
$template_vars = array(
	'type' => $type,
	'metas' => $metas,
	'ratio' => $ratio,
	'is_widget' => $is_widget,
	'columns' => $columns,
	'title_size' => $title_size,
	'meta_size' => $meta_size,
	'text_color' => $text_color,
	'bg_color' => $bg_color,
	'img_size' => $img_size,
	'items_action' => $items_action,
);
// Start the loop.
while ( have_posts() ) {
	the_post();

	us_load_template( 'templates/portfolio/listing-post', $template_vars );
}

?></div><?php

if ( $type == 'carousel' ) {
	?>
	<div class="g-preloader type_<?php echo $preloader_type; ?>"><div></div></div><?php
}

if ( $has_pagination AND $type != 'carousel' ) {
	$json_data = array(
		// Controller options
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		// Portfolio listing template variables that will be passed to this file in the next call
		'template_vars' => array(
			'ratio' => $ratio,
			'metas' => $metas,
			'is_widget' => FALSE,
			'title_size' => $title_size,
			'meta_size' => $meta_size,
			'text_color' => $text_color,
			'bg_color' => $bg_color,
			'img_size' => $img_size,
			'items_action' => $items_action,
		),
		'perpage' => $perpage,
		'page' => $page,
		'order' => $tile_order,
		'sizes' => $tile_sizes,
		'infinite_scroll' => ( ( isset( $is_infinite ) ) ? $is_infinite : 0 ),
	);
	if ( class_exists( 'SitePress' ) ) {
		global $sitepress;
		if ( $sitepress->get_default_language() != $sitepress->get_current_language() ) {
			$json_data['template_vars']['lang'] = $sitepress->get_current_language();
		}
	}
	?>
<div class="w-portfolio-json hidden"<?php echo us_pass_data_to_js( $json_data ) ?>></div><?php
	if ( $pagination == 'regular' ) {
		?>
		<div class="g-pagination"><?php
		the_posts_pagination(
			array(
				'prev_text' => '<',
				'next_text' => '>',
				'mid_size' => 3,
				'before_page_number' => '<span>',
				'after_page_number' => '</span>',
			)
		);
		?></div><?php
	} elseif ( $pagination == 'ajax' ) {
		// Passing g-loadmore options to JavaScript via onclick event
		?>
		<div class="g-loadmore">
			<div class="g-loadmore-btn">
				<span><?php _e( 'Load More', 'us' ) ?></span>
			</div>
			<div class="g-preloader type_<?php echo $preloader_type; ?>"><div></div></div>
		</div><?php
	}
}

if ( ! empty( $items_action ) AND $items_action == 'lightbox_page' ) {
	if ( ! $has_pagination ) {
		$json_data = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		);
		?>
<div class="w-portfolio-json hidden"<?php echo us_pass_data_to_js( $json_data ) ?>></div><?php
	}
?>
<div class="l-popup">
	<div class="l-popup-overlay"></div>
	<div class="l-popup-wrap">
		<div class="l-popup-box">
			<div class="l-popup-box-content"<?php if ( ! empty( $popup_width ) ) { echo ' style="max-width: ' . $popup_width . ';"'; } ?>>
				<div class="g-preloader type_<?php echo $preloader_type; ?>"><div></div></div>
				<iframe class="l-popup-box-content-frame" allowfullscreen></iframe>
			</div>
		</div>
		<?php if ( us_get_option( 'portfolio_nav', 0 ) ) {
			?>
			<div class="l-popup-arrow to_next" title="Next"></div>
			<div class="l-popup-arrow to_prev" title="Previous"></div>
			<?php
			}
		?>
		<div class="l-popup-closer"></div>
	</div>
</div>
<?php
}

?></div><?php

// Cleaning up
us_close_wp_query_context();
