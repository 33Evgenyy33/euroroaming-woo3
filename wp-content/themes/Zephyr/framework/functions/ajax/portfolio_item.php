<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Ajax method for portfolio page ajax load.
 */
add_action( 'wp_ajax_nopriv_us_ajax_portfolio_item', 'us_ajax_portfolio_item' );
add_action( 'wp_ajax_us_ajax_portfolio_item', 'us_ajax_portfolio_item' );
function us_ajax_portfolio_item() {

	$item_id = sanitize_key( $_POST['item_id'] );

	$item = get_post( $item_id );

	if ( empty( $item ) ) {
		die( 'Wrong Portfolio Page ID' );
	}

	do_action( 'us_before_us_portfolio' );

	$the_content = apply_filters( 'the_content', $item->post_content );

	// If content has no sections, we'll create them manually
	$has_own_sections = ( strpos( $the_content, ' class="l-section' ) !== FALSE );
	if ( ! $has_own_sections ) {
		$the_content = '<section class="l-section"><div class="l-section-h i-cf">' . $the_content . '</div></section>';
	}

	echo $the_content;


	do_action( 'us_after_us_portfolio' );

	die();

}