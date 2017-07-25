<?php

/**
 *  Prevents OptimizeMember from intefering with our ajax user search
 *
 *  @since 1.6.2
 *  @return void
 */
function affwp_optimize_member_user_query( $search_term = '' ) {

	remove_action( 'pre_user_query', 'c_ws_plugin__optimizemember_users_list::users_list_query', 10 );

}
add_action( 'affwp_pre_search_users', 'affwp_optimize_member_user_query' );

/**
 *  Prevents OptimizeMember from redirecting affiliates to the
 *  "Members Home Page/Login Welcome Page" when they log in
 *
 *  @since 1.7.16
 *  @return boolean
 */
function affwp_optimize_member_prevent_affiliate_redirect( $return, $vars ) {

	if ( doing_action( 'affwp_user_login' ) || doing_action( 'affwp_affiliate_register' ) ) {
		$return = false;
	}

	return $return;

}
add_filter( 'ws_plugin__optimizemember_login_redirect', 'affwp_optimize_member_prevent_affiliate_redirect', 10, 2 );

/**
 *  Fixes affiliate redirects when "Allow WishList Member To Handle Login Redirect"
 *  and "Allow WishList Member To Handle Logout Redirect" are enabled in WishList Member
 *
 *  @since 1.7.13
 *  @return boolean
 */
function affwp_wishlist_member_redirects( $return ) {

    $user    = wp_get_current_user();
    $user_id = $user->ID;

    if ( affwp_is_affiliate( $user_id ) ) {
        $return = true;
    }

    return $return;

}
add_filter( 'wishlistmember_login_redirect_override', 'affwp_wishlist_member_redirects' );
add_filter( 'wishlistmember_logout_redirect_override', 'affwp_wishlist_member_redirects' );

/**
 * Disables the mandrill_nl2br filter while sending AffiliateWP emails
 *
 * @since 1.7.17
 * @return void
 */
function affwp_disable_mandrill_nl2br() {
	add_filter( 'mandrill_nl2br', '__return_false' );
}
add_action( 'affwp_email_send_before', 'affwp_disable_mandrill_nl2br');

/**
 * Remove sptRemoveVariationsFromLoop() from pre_get_posts when query var is present.
 *
 * See https://github.com/AffiliateWP/AffiliateWP/issues/1586
 *
 * @since 1.9
 * @return void
 */
function affwp_simple_page_test_compat() {

	if( ! defined( 'SPT_PLUGIN_DIR' ) ) {
		return;
	}

	$tracking = affiliate_wp()->tracking;

	if( empty( $tracking ) ) {
		return;
	}

	if( $tracking->was_referred() ) {

		remove_action( 'pre_get_posts', 'sptRemoveVariationsFromLoop', 10 );

	}

}
add_action( 'pre_get_posts', 'affwp_simple_page_test_compat', -9999 );