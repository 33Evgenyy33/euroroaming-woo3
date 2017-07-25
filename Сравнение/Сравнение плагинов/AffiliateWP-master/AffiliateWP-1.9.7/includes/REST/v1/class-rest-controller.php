<?php
namespace AffWP\REST\v1;

/**
 * Base REST controller.
 *
 * @since 1.9
 * @abstract
 */
abstract class Controller {

	/**
	 * Object type.
	 *
	 * MUST be defined by extending classes.
	 *
	 * @since 1.9.5
	 * @access public
	 * @var string
	 */
	public $object_type = null;

	/**
	 * AffWP REST namespace.
	 *
	 * @since 1.9
	 * @access protected
	 * @var string
	 */
	protected $namespace = 'affwp/v1';

	/**
	 * The base of this controller's route.
	 *
	 * Should be defined and used by subclasses.
	 *
	 * @since 1.9
	 * @access protected
	 * @var string
	 */
	protected $rest_base;

	/**
	 * Registered REST fields.
	 *
	 * @since 1.9.5
	 * @access private
	 * @var array
	 */
	private $rest_fields = array();

	/**
	 * Constructor.
	 *
	 * Looks for a register_routes() method in the sub-class and hooks it up to 'rest_api_init'.
	 *
	 * @since 1.9
	 * @access public
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ), 15 );

		if ( null === $this->object_type ) {
			$message = sprintf( __( 'object_type must be defined by the extending class: %s', 'affiliate-wp' ), get_called_class() );
			_doing_it_wrong( 'object_type', $message, '1.9.5' );
		}
	}

	/**
	 * Converts an object or array of objects into a \WP_REST_Response object.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @param object|array $response Object or array of objects.
	 * @return \WP_REST_Response REST response.
	 */
	public function response( $response ) {
		return rest_ensure_response( $response );
	}

	/**
	 * Retrieves the query parameters for collections.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		return array(
			'context' => $this->get_context_param(),
			'number'  => array(
				'description'       => __( 'The number of items to query for. Use -1 for all.', 'affiliate-wp' ),
				'sanitize_callback' => 'absint',
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				},
			),
			'offset'  => array(
				'description'       => __( 'The number of items to offset in the query.', 'affiliate-wp' ),
				'sanitize_callback' => 'absint',
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				},
			),
			'order'   => array(
				'description'       => __( 'How to order results. Accepts ASC (ascending) or DESC (descending).', 'affiliate-wp' ),
				'validate_callback' => function( $param, $request, $key ) {
					return in_array( strtoupper( $param ), array( 'ASC', 'DESC' ) );
				},
			),
			'fields'  => array(
				'description'       => __( "Fields to limit the selection for. Accepts 'ids'. Default '*' for all.", 'affiliate-wp' ),
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => function( $param, $request, $key ) {
					return is_string( $param );
				},
			),
		);
	}

	/**
	 * Retrieves the magical context param.
	 *
	 * Ensures consistent description between endpoints, and populates enum from schema.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @see \WP_REST_Controller::get_context_param()
	 *
	 * @param array $args {
	 *     Optional. Parameter details. Default empty array.
	 *
	 *     @type string   $description       Parameter description.
	 *     @type string   $type              Parameter type. Accepts 'string', 'integer', 'array',
	 *                                       'object', etc. Default 'string'.
	 *     @type callable $sanitize_callback Parameter sanitization callback. Default 'sanitize_key'.
	 *     @type callable $validate_callback Parameter validation callback. Default empty.
	 * }
	 * @return array Context parameter details.
	 */
	public function get_context_param( $args = array() ) {
		$param_details = array(
			'description'       => __( 'Scope under which the request is made; determines fields present in response.', 'affiliate-wp' ),
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_key',
			'validate_callback' => '',
		);

		return array_merge( $param_details, $args );
	}

	/**
	 * Retrieves the object type for the current endpoints.
	 *
	 * @since 1.9.5
	 * @access public
	 *
	 * @return string Object type.
	 */
	public function get_object_type() {
		return $this->object_type;
	}

	/**
	 * Processes an object for output to a response.
	 *
	 * @since 1.9.5
	 * @access public
	 *
	 * @param \AffWP\Base_Object|mixed $object  Object for output to the response.
	 * @param \WP_REST_Request         $request Full details about the request.
	 * @return mixed (Maybe) modified object for a response.
	 */
	protected function process_for_output( $object, $request ) {
		$object_type = $this->get_object_type();
		$addl_fields = array();

		foreach ( $this->get_additional_fields( $object_type ) as $field_name => $field_options ) {

			if ( ! $field_options['get_callback'] ) {
				continue;
			}

			$addl_fields[ $field_name ] = call_user_func( $field_options['get_callback'], $object, $field_name, $request, $object_type );
		}

		$object::fill_vars( $object, $addl_fields );

		return $this->response( (object) $object );
	}

	/**
	 * Registers a new field on an existing AffiliateWP object type.
	 *
	 * @since 1.9.5
	 * @access public
	 *
	 * @param string $field_name The attribute name.
	 * @param array  $args {
	 *     Optional. An array of arguments used to handle the registered field.
	 *
	 *     @type string|array|null $get_callback    Optional. The callback function used to retrieve the field
	 *                                              value. Default is 'null', the field will not be returned in
	 *                                              the response.
	 *     @type string|array|null $schema          Optional. The callback function used to create the schema for
	 *                                              this field. Default is 'null', no schema entry will be returned.
	 * }
	 * @return null|void Null if the object type could not be determined, otherwise void.
	 */
	public function register_field( $field_name, $args = array() ) {
		$args = wp_parse_args( $args, array(
			'get_callback' => null,
			'schema'       => null,
		) );

		if ( ! $object_type = $this->get_object_type() ) {
			return;
		}

		if ( function_exists( 'register_rest_field' ) ) {
			register_rest_field( $object_type, $field_name, $args );
		}

		$this->rest_fields[ $object_type ][ $field_name ] = $args;
	}

	/**
	 * Retrieves all of the registered additional fields for a given object-type.
	 *
	 * @since 1.9.5
	 * @access protected
	 *
	 * @param string $object_type Optional. The object type.
	 * @return array Registered additional fields (if any), empty array if none or if the object type could
	 *               not be inferred.
	 */
	public function get_additional_fields( $object_type ) {
		$core_fields = array();

		if ( method_exists( '\WP_REST_Controller', 'get_additional_fields' ) ) {
			global $wp_rest_additional_fields;

			if ( $wp_rest_additional_fields[ $object_type ] ) {
				$core_fields = $wp_rest_additional_fields[ $object_type ];
			}
		}

		if ( isset( $this->rest_fields[ $object_type ] ) ) {
			$fields = $this->rest_fields[ $object_type ];
		} else {
			$fields = array();
		}

		return array_merge( $fields, $core_fields );
	}
}
