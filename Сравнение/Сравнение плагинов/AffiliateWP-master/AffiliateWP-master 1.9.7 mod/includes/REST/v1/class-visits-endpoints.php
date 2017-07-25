<?php
namespace AffWP\Visit\REST\v1;

use \AffWP\REST\v1\Controller;

/**
 * Implements REST routes and endpoints for Visits.
 *
 * @since 1.9
 *
 * @see AffWP\REST\Controller
 */
class Endpoints extends Controller {

	/**
	 * Object type.
	 *
	 * @since 1.9.5
	 * @access public
	 * @var string
	 */
	public $object_type = 'affwp_visit';

	/**
	 * Route base for visits.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $rest_base = 'visits';

	/**
	 * Registers Visit routes.
	 *
	 * @since 1.9
	 * @access public
	 */
	public function register_routes() {

		// /visits/
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_items' ),
			'args'     => $this->get_collection_params(),
			'permission_callback' => function( $request ) {
				return current_user_can( 'manage_affiliates' );
			}
		) );

		// /visits/ID
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>\d+)', array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_item' ),
			'args'     => array(
				'id' => array(
					'required'          => true,
					'validate_callback' => function( $param, $request, $key ) {
						return is_numeric( $param );
					}
				)
			),
			'permission_callback' => function( $request ) {
				return current_user_can( 'manage_affiliates' );
			}
		) );

		$this->register_field( 'id', array(
			'get_callback' => function( $object, $field_name, $request, $object_type ) {
				return $object->ID;
			}
		) );
	}

	/**
	 * Base endpoint to retrieve all visits.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Request $request Request arguments.
	 * @return \WP_REST_Response|\WP_Error Array of visits, otherwise WP_Error.
	 */
	public function get_items( $request ) {

		$args = array();

		$args['number']          = isset( $request['number'] )          ? $request['number'] : 20;
		$args['offset']          = isset( $request['offset'] )          ? $request['offset'] : 0;
		$args['visit_id']        = isset( $request['visit_id'] )        ? $request['visit_id'] : 0;
		$args['affiliate_id']    = isset( $request['affiliate_id'] )    ? $request['affiliate_id'] : 0;
		$args['referral_id']     = isset( $request['referral_id'] )     ? $request['referral_id'] : 0;
		$args['referral_status'] = isset( $request['referral_status'] ) ? $request['referral_status'] : '';
		$args['campaign']        = isset( $request['campaign'] )        ? $request['campaign'] : '';
		$args['date']            = isset( $request['date'] )            ? $request['date'] : '';
		$args['order']           = isset( $request['order'] )           ? $request['order'] : 'ASC';
		$args['orderby']         = isset( $request['orderby'] )         ? $request['orderby'] : '';
		$args['fields']          = isset( $request['fields'] )          ? $request['fields'] : '*';
		$args['search']          = isset( $request['search'] )          ? $request['search'] : '';

		if ( is_array( $request['filter'] ) ) {
			$args = array_merge( $args, $request['filter'] );
			unset( $request['filter'] );
		}

		/**
		 * Filters the query arguments used to retrieve visits in a REST request.
		 *
		 * @since 1.9
		 *
		 * @param array            $args    Arguments.
		 * @param \WP_REST_Request $request Request.
		 */
		$args = apply_filters( 'affwp_rest_visits_query_args', $args, $request );

		$visits = affiliate_wp()->visits->get_visits( $args );

		if ( empty( $visits ) ) {
			$visits = new \WP_Error(
				'no_visits',
				'No visits were found.',
				array( 'status' => 404 )
			);
		} else {
			$inst = $this;
			array_map( function( $visit ) use ( $inst, $request ) {
				$visit = $inst->process_for_output( $visit, $request );
				return $visit;
			}, $visits );
		}

		return $this->response( $visits );
	}

	/**
	 * Endpoint to retrieve a visit by ID.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Request $request Request arguments.
	 * @return \AffWP\Visit|\WP_Error Visit object or \WP_Error object if not found.
	 */
	public function get_item( $request ) {
		if ( ! $visit = \affwp_get_visit( $request['id'] ) ) {
			$visit = new \WP_Error(
				'invalid_visit_id',
				'Invalid visit ID',
				array( 'status' => 404 )
			);
		} else {
			// Populate extra fields.
			$visit = $this->process_for_output( $visit, $request );
		}

		return $this->response( $visit );
	}

	/**
	 * Retrieves the collection parameters for visits.
	 *
	 * @since 1.9
	 * @access public
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['context']['default'] = 'view';

		/*
		 * Pass top-level get_visits() args as query vars:
		 * /visits/?referral_status=pending&order=desc
		 */
		$params['visit_id'] = array(
			'description'       => __( 'The visit ID or array of IDs to query visits for.', 'affiliate-wp' ),
			'sanitize_callback' => 'absint',
			'validate_callback' => function( $param, $request, $key ) {
				return is_numeric( $param );
			},
		);

		$params['affiliate_id'] = array(
			'description'       => __( 'The affiliate ID or array of IDs to query visits for.', 'affiliate-wp' ),
			'sanitize_callback' => 'absint',
			'validate_callback' => function( $param, $request, $key ) {
				return is_numeric( $param );
			},
		);

		$params['referral_id'] = array(
			'description'       => __( 'The referral ID or array of IDs to query visits for.', 'affiliate-wp' ),
			'sanitize_callback' => 'absint',
			'validate_callback' => function( $param, $request, $key ) {
				return is_numeric( $param );
			},
		);

		$params['referral_status'] = array(
			'description'       => __( 'The referral status or array of statuses to retrieve visits for.', 'affiliate-wp' ),
			'validate_callback' => function( $param, $request, $key ) {
				return in_array( $param, array( 'paid', 'unpaid', 'pending', 'rejected' ) );
			},
		);

		$params['campaign'] = array(
			'description'       => __( 'The associated campaign.', 'affiliate-wp' ),
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => function( $param, $request, $key ) {
				return is_string( $param );
			},
		);

		$params['orderby'] = array(
			'description'       => __( 'Visits table column to order by.', 'affiliate-wp' ),
			'validate_callback' => function( $param, $request, $key ) {
				return array_key_exists( $param, affiliate_wp()->referrals->get_columns() );
			},
		);

		/*
		 * Pass any valid get_visits() args via filter:
		 * /visits/?filter[referral_status]=pending&filter[order]=desc
		 */
		$params['filter'] = array(
			'description' => __( 'Use any get_visits() arguments to modify the response.', 'affiliate-wp' )
		);

		return $params;
	}
}
