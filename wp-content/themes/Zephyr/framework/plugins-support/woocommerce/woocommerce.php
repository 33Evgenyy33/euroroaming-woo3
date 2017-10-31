<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * WooCommerce Theme Support
 *
 * @link http://www.woothemes.com/woocommerce/
 */

add_action( 'after_setup_theme', 'us_woocommerce_support' );
function us_woocommerce_support() {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}

if ( ! class_exists( 'woocommerce' ) ) {
	return FALSE;
}

global $woocommerce;
if ( version_compare( $woocommerce->version, '2.1', '<' ) ) {
	define( 'WOOCOMMERCE_USE_CSS', FALSE );
} else {
	add_filter( 'woocommerce_enqueue_styles', 'us_woocommerce_dequeue_styles' );
	function us_woocommerce_dequeue_styles( $styles ) {
		$styles = array();

		return $styles;
	}

	add_action( 'wp_enqueue_scripts', 'us_woocomerce_dequeue_checkout_styles', 100 );
	function us_woocomerce_dequeue_checkout_styles() {
		wp_dequeue_style( 'select2' );
		wp_deregister_style( 'select2' );
	}
}

if ( ! ( defined( 'US_DEV' ) AND US_DEV  ) AND us_get_option( 'optimize_assets', 0 ) == 0 ) {
	add_action( 'wp_enqueue_scripts', 'us_woocommerce_enqueue_styles', 14 );
}
function us_woocommerce_enqueue_styles( $styles ) {
	global $us_template_directory_uri;
	$min_ext = ( ! ( defined( 'US_DEV' ) AND US_DEV ) ) ? '.min' : '';
	wp_enqueue_style( 'us-woocommerce', $us_template_directory_uri . '/css/plugins/woocommerce' . $min_ext . '.css', array(), US_THEMEVERSION, 'all' );
}

// Adjust markup for all woocommerce pages

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
if ( ! function_exists( 'us_woocommerce_before_main_content' ) ) {
	add_action( 'woocommerce_before_main_content', 'us_woocommerce_before_main_content', 10 );
	function us_woocommerce_before_main_content() {
		echo '<div class="l-main"><div class="l-main-h i-cf"><main class="l-content">';
		if ( is_post_type_archive( 'product' ) && 0 === absint( get_query_var( 'paged' ) ) ) {
			$shop_page = get_post( wc_get_page_id( 'shop' ) );
			if ( $shop_page ) {

				$description = apply_filters( 'the_content', $shop_page->post_content );
				if ( $description ) {
					$has_own_sections = ( strpos( $description, ' class="l-section' ) !== FALSE );
					if ( ! $has_own_sections ) {
						$description = '<section class="l-section for_shop_description"><div class="l-section-h i-cf">' . $description . '</div></section>';
					}
					echo $description;

				}
			}
		}

		echo '<section id="shop" class="l-section for_shop"><div class="l-section-h i-cf">';
	}
}

remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
if ( ! function_exists( 'us_woocommerce_after_main_content' ) ) {
	add_action( 'woocommerce_after_main_content', 'us_woocommerce_after_main_content', 20 );
	function us_woocommerce_after_main_content() {
		$us_layout = US_Layout::instance();
		echo '</div></section></main>';
		if ( $us_layout->sidebar_pos == 'left' OR $us_layout->sidebar_pos == 'right' ) {

			if ( is_singular() ) {
				$sidebar_id = us_dynamic_sidebar_id( us_get_option( 'product_sidebar_id', 'default_sidebar' ) );
			} else {
				$sidebar_id = us_get_option( 'shop_sidebar_id', 'default_sidebar' );
				if ( ! is_search() AND ! is_tax() ) {
					if ( usof_meta( 'us_sidebar', array(), wc_get_page_id( 'shop' ) ) == 'custom' ) {
						$shop_page_sidebar_id = usof_meta( 'us_sidebar_id', array(), wc_get_page_id( 'shop' ) );
						if ( $shop_page_sidebar_id ) {
							$sidebar_id = $shop_page_sidebar_id;
						}
					}
				}
			}
			echo '<aside class="l-sidebar at_' . $us_layout->sidebar_pos . ' ' . $sidebar_id . '"';
			if ( us_get_option( 'schema_markup' ) ) {
				echo ' itemscope itemtype="https://schema.org/WPSideBar"';
			}
			echo '>';
			dynamic_sidebar( $sidebar_id );
			echo '</aside>';
		}
		echo '</div></div>';
	}
}

// Adjust markup for product in list
add_action( 'woocommerce_before_shop_loop_item', 'us_woocommerce_before_shop_loop_item', 20 );
function us_woocommerce_before_shop_loop_item() {
	echo '<div class="product-h">';
}

add_action( 'woocommerce_after_shop_loop_item', 'us_woocommerce_after_shop_loop_item', 20 );
function us_woocommerce_after_shop_loop_item() {
	echo '</div>';
}

add_action( 'woocommerce_before_shop_loop_item_title', 'us_woocommerce_before_shop_loop_item_title', 20 );
function us_woocommerce_before_shop_loop_item_title() {
	echo '<div class="product-meta">';
}

add_action( 'woocommerce_after_shop_loop_item_title', 'us_woocommerce_after_shop_loop_item_title', 20 );
function us_woocommerce_after_shop_loop_item_title() {
	echo '</div>';
}

// Change number of related products
function woo_related_products_limit() {
	global $product;

	$args['posts_per_page'] = us_get_option( 'product_related_qty', 4 );

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'us_related_products_args' );
function us_related_products_args( $args ) {
	$args['posts_per_page'] = us_get_option( 'product_related_qty', 4 );

	return $args;
}
add_filter( 'woocommerce_cross_sells_total', 'us_woocommerce_cross_sells_total' );
add_filter( 'woocommerce_cross_sells_columns', 'us_woocommerce_cross_sells_total' );
function us_woocommerce_cross_sells_total( $count ) {
	return 4;
}

// Remove WC sidebar
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

// Move cross sells bellow the shipping
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display', 10 );

// Add breadcrumbs before product title
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_breadcrumb', 3 );

// Alter Cart - add total number
add_filter( 'woocommerce_add_to_cart_fragments', 'us_add_to_cart_fragments' );
function us_add_to_cart_fragments( $fragments ) {
	global $woocommerce;

	$fragments['a.cart-contents'] = '<a class="cart-contents" href="' . esc_url( $woocommerce->cart->get_cart_url() ) . '">' . $woocommerce->cart->get_cart_total() . '</a>';

	return $fragments;
}

add_action( 'body_class', 'us_wc_body_class' );
function us_wc_body_class( $classes ) {
	$classes[] = 'us-woo-shop_' . us_get_option( 'shop_listing_style', 'standard' );
	$classes[] = 'us-woo-cart_' . us_get_option( 'shop_cart', 'standard' );
	if ( us_get_option( 'shop_catalog', 0 ) == 1 ) {
		$classes[] = 'us-woo-catalog';
	}
	if ( is_single() OR is_cart() ) {
		$classes[] = 'columns-' . us_get_option( 'product_related_qty', 4 );
	} else {
		$classes[] = 'columns-' . us_get_option( 'shop_columns', 4 );
	}

	return $classes;
}

// Pagination
if ( ! function_exists( 'woocommerce_pagination' ) ) {
	function woocommerce_pagination() {
		global $wp_query;
		if ( $wp_query->max_num_pages <= 1 ) {
			return;
		}
		echo '<div class="g-pagination">';
		the_posts_pagination(
			array(
				'prev_text' => '<',
				'next_text' => '>',
				'before_page_number' => '<span>',
				'after_page_number' => '</span>',
			)
		);
		echo '</div>';
	}
}

add_action( 'woocommerce_after_mini_cart', 'us_woocommerce_after_mini_cart' );
function us_woocommerce_after_mini_cart() {
	global $woocommerce;

	echo '<span class="us_mini_cart_amount" style="display: none;">' . $woocommerce->cart->cart_contents_count . '</span>';
}

function woocommerce_product_archive_description() {
	return '';
}

add_filter( 'us_image_sizes_select_values', 'us_woocommerce_image_sizes_select_values' );
function us_woocommerce_image_sizes_select_values( $image_sizes ) {
	$size_names = array( 'shop_single', 'shop_catalog', 'shop_thumbnail' );

	foreach ( $size_names as $size_name ) {
		// Detecting size
		$size = us_get_intermediate_image_size( $size_name );
		$size_title = ( ( $size['width'] == 0 ) ? __( 'any', 'us' ) : $size['width'] );
		$size_title .= ' x ';
		$size_title .= ( $size['height'] == 0 ) ? __( 'any', 'us' ) : $size['height'];
		if ( $size['crop'] ) {
			$size_title .= ' ' . __( 'cropped', 'us' );
		}
		if ( ! in_array( $size_title, $image_sizes ) ) {
			$image_sizes[$size_title] = $size_name;
		}
	}

	return $image_sizes;
}

add_filter( 'woocommerce_checkout_fields', 'us_woocommerce_disable_autofocus_billing_firstname' );
function us_woocommerce_disable_autofocus_billing_firstname( $fields ) {
	$fields['shipping']['shipping_first_name']['autofocus'] = FALSE;

	return $fields;
}

add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'us_woocommerce_dropdown_variation_attribute_options_html' );
function us_woocommerce_dropdown_variation_attribute_options_html( $html ) {
	$html = '<div class="woocommerce-select">' . $html . '</div>';

	return $html;
}
