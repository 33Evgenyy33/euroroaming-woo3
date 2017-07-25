<?php
namespace AffWP\Affiliate\REST\v1;

use AffWP\REST\v1\Controller;

/**
 * Implements REST routes and endpoints for Affiliates.
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
	public $object_type = 'affwp_affiliate';

	/**
	 * Route base for affiliates.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $rest_base = 'affiliates';

	/**
	 * Registers Affiliate routes.
	 *
	 * @since 1.9
	 * @access public
	 */
	public function register_routes() {

		// /affiliates/
		register_rest_route( $this->namespace, '/' . $this->rest_base, array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_items' ),
			'args'     => $this->get_collection_params(),
			'permission_callback' => function( $request ) {
				return current_user_can( 'manage_affiliates' );
			}
		) );

		// /affiliates/ID
		register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>\d+)', array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => array( $this, 'get_item' ),
			'args'     => array(
				'id' => array(
					'required'          => true,
					'validate_callback' => function( $param, $request, $key ) {
						return is_numeric( $param );
					}
				),
				'user' => array(
					'validate_callback' => function( $param, $request, $key ) {
						return is_string( $param );
					}
				),
				'meta' => array(
					'validate_callback' => function( $param, $request, $key ) {
						return is_string( $param );
					}
				),
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
	 * Base endpoint to retrieve all affiliates.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Request $request Request arguments.
	 * @return \WP_REST_Response|\WP_Error Affiliates response object or \WP_Error object if not found.
	 */
	public function get_items( $request ) {

		$args = array();

		$args['number']       = isset( $request['number'] )       ? $request['number'] : 20;
		$args['offset']       = isset( $request['offset'] )       ? $request['offset'] : 0;
		$args['exclude']      = isset( $request['exclude'] )      ? $request['exclude'] : array();
		$args['affiliate_id'] = isset( $request['affiliate_id'] ) ? $request['affiliate_id'] : 0;
		$args['user_id']      = isset( $request['user_id'] )      ? $request['user_id'] : 0;
		$args['status']       = isset( $request['status'] )       ? $request['status'] : '';
		$args['order']        = isset( $request['order'] )        ? $request['order'] : 'ASC';
		$args['orderby']      = isset( $request['orderby'] )      ? $request['orderby'] : '';
		$args['fields']       = isset( $request['fields'] )       ? $request['fields'] : '*';

		if ( is_array( $request['filter'] ) ) {
			$args = array_merge( $args, $request['filter'] );
			unset( $request['filter'] );
		}

		$args['user'] = $user = ! empty( $request['user'] );
		$args['meta'] = $meta = ! empty( $request['meta'] );

		/**
		 * Filters the query arguments used to retrieve affiliates in a REST request.
		 *
		 * @since 1.9
		 *
		 * @param array            $args    Arguments.
		 * @param \WP_REST_Request $request Request.
		 */
		$args = apply_filters( 'affwp_rest_affiliates_query_args', $args, $request );

		$affiliates = affiliate_wp()->affiliates->get_affiliates( $args );

		if ( empty( $affiliates ) ) {
			$affiliates = new \WP_Error(
				'no_affiliates',
				'No affiliates were found.',
				array( 'status' => 404 )
			);
		} else {
			$inst = $this;
			array_map( function( $affiliate ) use ( $user, $meta, $inst, $request ) {
				$affiliate = $inst->process_for_output( $affiliate, $request, $user, $meta );
				return $affiliate;
			}, $affiliates );
		}

		return $this->response( $affiliates );
	}

	/**
	 * Endpoint to retrieve an affiliate by ID.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param \WP_REST_Request $request Request arguments.
	 * @return \WP_REST_Response|\WP_Error Affiliate object response or \WP_Error object if not found.
	 */
	public function get_item( $request ) {
		if ( ! $affiliate = \affwp_get_affiliate( $request['id'] ) ) {
			$affiliate = new \WP_Error(
				'invalid_affiliate_id',
				'Invalid affiliate ID',
				array( 'status' => 404 )
			);
		} else {
			$user = $request->get_param( 'user' );
			$meta = $request->get_param( 'meta' );

			// Populate extra fields and return.
			$affiliate = $this->process_for_output( $affiliate, $request, $user, $meta );
		}

		return $this->response( $affiliate );
	}

	/**
	 * Processes an Affiliate object for output.
	 *
	 * Populates non-public properties with derived values.
	 *
	 * @since 1.9
	 * @since 1.9.5 Added the `$meta` and `$request` parameters.
	 * @access protected
	 *
	 * @param \AffWP\Affiliate $affiliate Affiliate object.
	 * @param \WP_REST_Request $request   Full details about the request.
	 * @param bool             $user      Optional. Whether to lazy load the user object. Default false.
	 * @param bool             $meta      Optional. Whether to lazy load the affiliate meta. Default false.
	 * @return \AffWP\Affiliate Affiliate object.
	 */
	protected function process_for_output( $affiliate, $request, $user = false, $meta = false ) {

		if ( true == $user ) {
			$affiliate->user = $affiliate->get_user();
		}

		if ( true == $meta ) {
			$affiliate->meta = $affiliate->get_meta();
		}

		return parent::process_for_output( $affiliate, $request );
	}

	/**
	 * Retrieves the collection parameters for affiliates.
	 *
	 * @since 1.9
	 * @access public
	 * @return array
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params['context']['default'] = 'view';

		/*
		 * Pass top-level get_affiliates() args as query vars:
		 * /affiliates/?status=pending&order=desc
		 */
		$params['affiliate_id'] = array(
			'description'       => __( 'The affiliate ID or array of IDs to query for.', 'affiliate-wp' ),
			'sanitize_callback' => 'absint',
			'validate_callback' => function( $param, $request, $key ) {
				return is_numeric( $param );
			},
		);

		$params['user_id'] = array(
			'description'       => __( 'The user ID or array of IDs to query payouts for.', 'affiliate-wp' ),
			'sanitize_callback' => 'absint',
			'validate_callback' => function( $param, $request, $key ) {
				return is_numeric( $param );
			},
		);

		$params['exclude'] = array(
			'description'       => __( 'Affiliate ID or array of IDs to exclude from the query.', 'affiliate-wp' ),
			'sanitize_callback' => function( $param, $request, $key ) {
				return is_numeric( $param ) || is_array( $param );
			},
		);

		$params['search'] = array(
			'description'       => __( 'Terms to search for affiliates. Accepts an affiliate ID or a string.', 'affiliate-wp' ),
			'validate_callback' => function( $param, $request, $key ) {
				return is_numeric( $param ) || is_string( $param );
			},
		);

		$params['status'] = array(
			'description'       => __( "The affiliate status. Accepts 'active', 'inactive', 'pending', or 'rejected'.", 'affiliate-wp' ),
			'validate_callback' => function( $param, $request, $key ) {
				return in_array( $param, array( 'active', 'inactive', 'pending', 'rejected' ) );
			},
		);

		$params['orderby'] = array(
			'description'       => __( 'Affiliates table column to order by.', 'affiliate-wp' ),
			'validate_callback' => function( $param, $request, $key ) {
				return array_key_exists( $param, affiliate_wp()->affiliates->get_columns() );
			}
		);

		$params['date'] = array(
			'description'       => __( 'The date array or string to query affiliate registrations within.', 'affiliate-wp' ),
			'validate_callback' => function( $param, $request, $key ) {
				return is_string( $param ) || is_array( $param );
			}
		);

		/*
		 * Pass any valid get_creatives() args via filter:
		 * /affiliates/?filter[status]=pending&filter[order]=desc
		 */
		$params['filter'] = array(
			'description' => __( 'Use any get_affiliates() arguments to modify the response.', 'affiliate-wp' )
		);

		return $params;
	}
}
