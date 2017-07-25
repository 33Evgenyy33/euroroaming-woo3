<?php

// retrieves a list of users via live search
function affwp_search_users() {
	if ( empty( $_REQUEST['term'] ) ) {
		wp_die( -1 );
	}

	if ( ! current_user_can( 'manage_affiliates' ) ) {
		wp_die( -1 );
	}

	$search_query = htmlentities2( trim( $_REQUEST['term'] ) );

	do_action( 'affwp_pre_search_users', $search_query );

	$args = array(
		'search_columns' => array( 'user_login', 'display_name', 'user_email' )
	);

	if ( isset( $_REQUEST['status'] ) ) {
		$status = mb_strtolower( htmlentities2( trim( $_REQUEST['status'] ) ) );

		switch ( $status ) {
			case 'none':
				$affiliate_users = affiliate_wp()->affiliates->get_affiliates(
					array(
						'number' => -1,
						'fields' => 'user_id',
					)
				);
				$args = array( 'exclude' => $affiliate_users );
				break;
			case 'any':
				$affiliate_users = affiliate_wp()->affiliates->get_affiliates(
					array(
						'number' => -1,
						'fields' => 'user_id',
					)
				);
				$args = array( 'include' => $affiliate_users );
				break;
			default:
				$affiliate_users = affiliate_wp()->affiliates->get_affiliates(
					array(
						'number' => -1,
						'status' => $status,
						'fields' => 'user_id',
					)
				);
				$args = array( 'include' => $affiliate_users );
		}
	}

	// Add search string to args.
	$args['search'] = '*' . mb_strtolower( htmlentities2( trim( $_REQUEST['term'] ) ) ) . '*';

	// Get users matching search.
	$found_users = get_users( $args );

	$user_list = array();

	if ( $found_users ) {
		foreach( $found_users as $user ) {
			$label = empty( $user->user_email ) ? $user->user_login : "{$user->user_login} ({$user->user_email})";

			$user_list[] = array(
				'label'   => $label,
				'value'   => $user->user_login,
				'user_id' => $user->ID
			);
		}
	}

	wp_die( json_encode( $user_list ) );
}
add_action( 'wp_ajax_affwp_search_users', 'affwp_search_users' );
