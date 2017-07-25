<?php
namespace AffWP\Referral\REST\v1;

use \AffWP\REST\v1\Controller;

/**
 * Implements REST routes and endpoints for Referrals.
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
	public $object_type = 'affwp_referral';

	/**
	 * Route base for referrals.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $rest_base = 'referrals';

	/**
	 * Registers Referral routes.
	 *
	 * @since 1.9
	 * @access public
	 */
	public function register_routes() {
		// /referrals/
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_items' ),
			'args'     => $this->get_collection_params(),
			'permission_callback' => function( $request ) {
				return current_user_can( 'manage_affiliates' );
			}
		) );

		// /referrals/ID
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
	 * Base endpoint to retrieve all referrals.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Request $request Request arguments.
	 * @return \WP_REST_Response|\WP_Error Referrals query response, otherwise \WP_Error.
	 */
	public function get_items( $request ) {

		$args = array();

		$args['number']       = isset( $request['number'] )       ? $request['number'] : 20;
		$args['offset']       = isset( $request['offset'] )       ? $request['offset'] : 0;
		$args['referral_id']  = isset( $request['referral_id'] )  ? $request['referral_id'] : 0;
		$args['affiliate_id'] = isset( $request['affiliate_id'] ) ? $request['affiliate_id'] : 0;
		$args['reference']    = isset( $request['reference'] )    ? $request['reference'] : '';
		$args['context']      = isset( $request['ref_context'] )  ? $request['ref_context'] : '';
		$args['campaign']     = isset( $request['campaign'] )     ? $request['campaign'] : '';
		$args['status']       = isset( $request['status'] )       ? $request['status'] : '';
		$args['orderby']      = isset( $request['orderby'] )      ? $request['orderby'] : '';
		$args['order']        = isset( $request['order'] )        ? $request['order'] : 'ASC';
		$args['search']       = isset( $request['search'] )       ? $request['search'] : false;
		$args['date']         = isset( $request['date'] )         ? $request['date'] : '';

		if ( is_array( $request['filter'] ) ) {
			$args = array_merge( $args, $request['filter'] );
			unset( $request['filter'] );
		}

		/**
		 * Filters the query arguments used to retrieve referrals in a REST request.
		 *
		 * @since 1.9
		 *
		 * @param array            $args    Arguments.
		 * @param \WP_REST_Request $request Request.
		 */
		$args = apply_filters( 'affwp_rest_referrals_query_args', $args, $request );

		$referrals = affiliate_wp()->referrals->get_referrals( $args );

		if ( empty( $referrals ) ) {
			$referrals = new \WP_Error(
				'no_referrals',
				'No referrals were found.',
				array( 'status' => 404 )
			);
		} else {
			$inst = $this;
			array_map( function( $referral ) use ( $inst, $request ) {
				$referral = $inst->process_for_output( $referral, $request );
				return $referral;
			}, $referrals );
		}

		return $this->response( $referrals );
	}

	/**
	 * Endpoint to retrieve a referral by ID.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Request $request Request arguments.
	 * @return \WP_REST_Response|\WP_Error Response object or \WP_Error object if not found.
	 */
	public function get_item( $request ) {
		if ( ! $referral = \affwp_get_referral( $request['id'] ) ) {
			$referral = new \WP_Error(
				'invalid_referral_id',
				'Invalid referral ID',
				array( 'status' => 404 )
			);
		} else {
			// Populate extra fields.
			$referral = $this->process_for_output( $referral, $request );
		}

		return $this->response( $referral );
	}

	/**
	 * Retrieves the collection parameters for referrals.
	 *
	 * @since 1.9
	 * @access public
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['context']['default'] = 'view';

		/*
		 * Pass top-level get_referrals() args as query vars:
		 * /referrals/?status=pending&order=desc
		 */
		$params['referral_id'] = array(
			'description'       => __( 'The referral ID or array of IDs to query for.', 'affiliate-wp' ),
			'sanitize_callback' => 'absint',
			'validate_callback' => function( $param, $request, $key ) {
				return is_numeric( $param );
			},
		);

		$params['affiliate_id'] = array(
			'description'       => __( 'The affiliate ID or array of IDs to query for.', 'affiliate-wp' ),
			'sanitize_callback' => 'absint',
			'validate_callback' => function( $param, $request, $key ) {
				return is_numeric( $param );
			},
		);

		$params['reference'] = array(
			'description'       => __( 'Reference information (product ID) for the referral.', 'affiliate-wp' ),
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => function( $param, $request, $key ) {
				return is_string( $param );
			},
		);

		// 'ref_context' so as not to conflict with the global 'content' parameter.
		$params['ref_context'] = array(
			'description'       => __( 'The context under which the referral was created (integration).', 'affiliate-wp' ),
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => function( $param, $request, $key ) {
				return is_string( $param );
			},
		);

		$params['campaign'] = array(
			'description'       => __( 'The associated campaign.', 'affiliate-wp' ),
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => function( $param, $request, $key ) {
				return is_string( $param );
			},
		);

		$params['status'] = array(
			'description'       => __( 'The referral status or array of statuses.', 'affiliate-wp' ),
			'validate_callback' => function( $param, $request, $key ) {
				return in_array( $param, array( 'paid', 'unpaid', 'pending', 'rejected' ) );
			},
		);

		$params['orderby'] = array(
			'description'       => __( 'Referrals table column to order by.', 'affiliate-wp' ),
			'validate_callback' => function( $param, $request, $key ) {
				return array_key_exists( $param, affiliate_wp()->referrals->get_columns() );
			}
		);

		$params['search'] = array(
			'description'       => __( 'A referral ID or the search string to query for referrals with.', 'affiliate-wp' ),
			'sanitize_callback' => 'sanitize_text_field',
			'validate_callback' => function( $param, $request, $key ) {
				return is_numeric( $param ) || is_string( $param );
			},
		);

		$params['date'] = array(
			'description'       => __( 'The date array or string to query referrals within.', 'affiliate-wp' ),
			'validate_callback' => function( $param, $request, $key ) {
				return is_string( $param ) || is_array( $param );
			},
		);

		/*
		 * Pass any valid get_referrals() args via filter:
		 * /referrals/?filter[status]=pending&filter[order]=desc
		 */
		$params['filter'] = array(
			'description' => __( 'Use any get_referrals() arguments to modify the response.', 'affiliate-wp' )
		);

		return $params;
	}
}
