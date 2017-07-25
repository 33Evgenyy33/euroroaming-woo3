<?php
/**
 * WooCommerce Local Pickup Plus
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @package     WC-Shipping-Local-Pickup-Plus
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2017, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Local Pickup Locations handler class.
 *
 * This class handles general pickup locations related functionality.
 *
 * @since 2.0.0
 */
class WC_Local_Pickup_Plus_Pickup_Locations {


	/** @var array memoized pickup locations */
	private $pickup_locations = array();

	/** @var array memoized pickup locations area codes */
	private $pickup_locations_country_state_codes = array();


	/**
	 * Pickup locations handler constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		$this->load_classes();

		// create a new row in geodata table when a new post is created
		add_action( 'wp_insert_post', array( $this, 'init_geodata' ), 10, 3 );
		// delete geodata row when a pickup location post is deleted
		add_action( 'delete_post',    array( $this, 'drop_geodata' ) );
	}


	/**
	 * Loads the pickup locations objects.
	 *
	 * @since 2.0.0
	 */
	private function load_classes() {

		$plugin_path = wc_local_pickup_plus()->get_plugin_path();
		$classes     = array(
			// pickup location properties helper classes to define a pickup location's properties:
			'/includes/pickup-locations/class-wc-local-pickup-plus-address.php',
			'/includes/pickup-locations/class-wc-local-pickup-plus-price-adjustment.php',
			'/includes/pickup-locations/class-wc-local-pickup-plus-business-hours.php',
			'/includes/pickup-locations/class-wc-local-pickup-plus-public-holidays.php',
			'/includes/pickup-locations/class-wc-local-pickup-plus-schedule-adjustment.php',
			// the main pickup location object that uses all of the above:
			'/includes/class-wc-local-pickup-plus-pickup-location.php',
		);

		// load helper objects
		foreach ( $classes as $class ) {
			require_once( $plugin_path . $class );
		}
	}


	/**
	 * Get area codes for registered pickup locations.
	 *
	 * @since 2.0.0
	 *
	 * @return string[] array of country:state codes
	 */
	public function get_available_pickup_location_country_state_codes() {

		if ( ! isset( $this->pickup_locations_country_state_codes['all'] ) ) {
			global $wpdb;

			$this->pickup_locations_country_state_codes['all'] = array();

			$codes = array();
			$table = $wpdb->prefix . 'woocommerce_pickup_locations_geodata';
			$query = $wpdb->get_results( "
				SELECT country, state
				FROM {$table}
				ORDER BY country
			", ARRAY_A );

			if ( ! empty( $query ) ) {

				foreach ( $query as $pickup_location ) {
					$codes[] = "{$pickup_location['country']}:{$pickup_location['state']}";
				}

				$this->pickup_locations_country_state_codes['all'] = array_unique( $codes );
			}
		}

		return $this->pickup_locations_country_state_codes['all'];
	}


	/**
	 * Get available countries where pickup locations exist.
	 *
	 * @since 2.0.0
	 *
	 * @return array associative array of country codes and labels
	 */
	public function get_available_pickup_location_countries() {

		$results         = array();
		$all_countries   = WC()->countries->get_countries();
		$available_areas = $this->get_available_pickup_location_country_state_codes();

		foreach ( $available_areas as $codes ) {

			$codes = explode( ':', $codes );

			if ( array_key_exists( $codes[0], $all_countries ) ) {
				$results[ $codes[0] ] = $all_countries[ $codes[0] ];
			}
		}

		asort( $results );

		return $results;
	}


	/**
	 * Get available states where pickup locations exits.
	 *
	 * @since 2.0.0
	 *
	 * @param string $country_code optional, narrow pickup locations to a country's states
	 * @return array associative array of country, state codes and labels
	 */
	public function get_available_pickup_location_states( $country_code = null ) {

		$results         = array();
		$states          = WC()->countries->get_states();
		$available_areas = $this->get_available_pickup_location_country_state_codes();

		foreach ( $available_areas as $codes ) {

			$codes   = explode( ':', $codes );
			$country = $codes[0];
			$state   = isset( $codes[1] ) ? $codes[1] : '';

			if ( ! empty( $state ) && isset( $states[ $country ][ $state ] ) ) {
				$results[ $country ][ $state ] = $states[ $country ][ $state ];
			}
		}

		asort( $results );

		if ( is_string( $country_code ) ) {
			$key     = strtoupper( $country_code );
			$results = isset( $results[ $key ] ) ? $results[ $key ] : array();
		}

		return $results;
	}


	/**
	 * Get nearby pickup locations.
	 *
	 * @since 2.0.0
	 *
	 * @param array|\WC_Local_Pickup_Plus_Address $origin either coordinates (array) or address (object)
	 * @param array $args optional: additional as in get_posts() to limit, filter and sort results, if any
	 * @return \WC_Local_Pickup_Plus_Pickup_Location[] array of locations
	 */
	public function get_pickup_locations_nearby( $origin, $args = array() ) {

		$locations = array();

		if ( is_array( $origin ) && isset( $origin['lat'], $origin['lon'] ) ) {
			$locations = $this->get_pickup_locations_by_distance( $origin, $args );
		} elseif ( $origin instanceof WC_Local_Pickup_Plus_Address ) {
			$locations = $this->get_pickup_locations_by_address( $origin, $args );
		}

		return $locations;
	}


	/**
	 * Normalize a distance string into a number, according to a unit length.
	 *
	 * TODO if there's need to open this method to public, consider instead making an utility function {FN 2017-05-05}
	 *
	 * @since 2.0.0
	 *
	 * @param string $distance distance to parse - expecting a string such as "30km" or "50 mi": if a unit is not passed, it is assumed to be the one specified in $format
	 * @param string $unit_length either 'km' for kilometers (default) or 'mi' for miles: if $distance carries a different format, the amount will be converted to $format
	 * @return int|float the amount in the requested format
	 */
	private function parse_distance( $distance, $unit_length = 'km' ) {

		$unit_length   = strtolower( $unit_length );
		$unit_length   = in_array( $unit_length, array( 'km', 'mi' ), true ) ? $unit_length : 'km';
		$radius_unit   = strtolower( trim( preg_replace( '/[^a-zA-Z]+/', '', $distance ) ) );
		$radius_unit   = in_array( $radius_unit, array( 'km', 'mi' ), true ) ? $radius_unit : $unit_length;
		$radius_amount = abs( preg_replace( '/\D/', '', $distance ) );

		if ( 'mi' === $radius_unit && 'km' === $unit_length ) {
			$radius_amount *= 0.621371;
		} elseif ( 'km' === $radius_unit && 'mi' === $unit_length ) {
			$radius_amount *= 1.60934;
		}

		return $radius_amount;
	}


	/**
	 * Get pickup locations by distance.
	 *
	 * @link https://en.wikipedia.org/wiki/Haversine_formula
	 *
	 * @since 2.0.0
	 *
	 * @param array $coordinates associative array with coordinates
	 * @param array $args optional, array of args similar to get_posts() arguments
	 * @param null|string $radius radius to search within, uses default from shipping method settings
	 * @return \WC_Local_Pickup_Plus_Pickup_Location[] array of locations
	 */
	public function get_pickup_locations_by_distance( array $coordinates, $args = array(), $radius = null ) {
		global $wpdb;

		$location_ids = array();
		$table        = $wpdb->prefix . 'woocommerce_pickup_locations_geodata';
		$latitude     = isset( $coordinates['lat'] ) && is_numeric( $coordinates['lat'] ) ? $coordinates['lat'] : 0;
		$longitude    = isset( $coordinates['lon'] ) && is_numeric( $coordinates['lon'] ) ? $coordinates['lon'] : 0;
		$radius       = ! is_string( $radius ) ? '50km' : $radius ;
		$radius_km    = max( 0, $this->parse_distance( $radius, 'km' ) );

		// we need a limiter to prevent a radius expansion infinite loop (use Earth circumference)
		if ( $radius_km > 0 && $radius_km <= 40075 ) {

			// MySQL adaptation of Haversine formula to calculate great-circle distance between two points.
			// Note: this version uses kilometers!
			$query = "
				SELECT post_id, ( 6371 * acos( cos( radians({$latitude}) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians({$longitude}) ) + sin( radians({$latitude}) ) * sin( radians(lat) ) ) ) AS distance
				FROM {$table}
				HAVING distance < {$radius_km}
				ORDER BY distance
			";

			if ( isset( $args['posts_per_page'] ) && $args['posts_per_page'] > -1 ) {
				$query .= "
					LIMIT {$args['posts_per_page']}
				";
			}

			$results = $wpdb->get_results( "{$query}", ARRAY_A );

			if ( ! empty( $results ) ) {
				foreach ( $results as $pickup_location ) {
					if ( isset( $pickup_location['post_id'] ) ) {
						$location_ids[] = (int) $pickup_location['post_id'];
					}
				}
			} else {
				// if no results, progressively expand the search by expanding the queried radius
				$this->get_pickup_locations_by_distance( $coordinates, $args, ( $radius_km * 2 ) . 'km' );
			}
		}

		return $this->gather_pickup_locations( array_unique( $location_ids ), $args );
	}


	/**
	 * Get pickup locations by address.
	 *
	 * @since 2.0.0
	 *
	 * @param \WC_Local_Pickup_Plus_Address $address an address object
	 * @param array $args optional, array of args similar to get_posts() arguments
	 * @return \WC_Local_Pickup_Plus_Pickup_Location[] array of locations
	 */
	private function get_pickup_locations_by_address( WC_Local_Pickup_Plus_Address $address, $args = array() ) {
		global $wpdb;

		$location_ids = array();
		$country      = $address->get_country();
		$state        = $address->get_state();
		$city         = $address->get_city();
		$postcode     = $address->get_postcode();
		$table        = $wpdb->prefix . 'woocommerce_pickup_locations_geodata';

		if ( '' === $country ) {
			$query = "
				SELECT post_id
				FROM {$table}
				WHERE city LIKE %s 
				OR postcode LIKE %s
				ORDER BY postcode
			";
		} else {
			$query = "
				SELECT post_id
				FROM {$table}
				WHERE country = %s
				AND state = %s
				AND ( city LIKE %s OR postcode LIKE %s )
				ORDER BY postcode
			";
		}

		if ( isset( $args['posts_per_page'] ) && $args['posts_per_page'] > -1 ) {
			$query .= "
				LIMIT {$args['posts_per_page']}
			";
		}

		if ( '' === $country ) {
			$results = $wpdb->get_results( $wpdb->prepare( "{$query}", "%{$city}%", "%{$postcode}%" ), ARRAY_A );
		} else {
			$results = $wpdb->get_results( $wpdb->prepare( "{$query}", $country, $state, "%{$city}%", "%{$postcode}%" ), ARRAY_A );
		}

		if ( ! empty( $results ) ) {
			foreach ( $results as $pickup_location ) {
				if ( isset( $pickup_location['post_id'] ) )  {
					$location_ids[] = (int) $pickup_location['post_id'];
				}
			}
		}

		return $this->gather_pickup_locations( array_unique( $location_ids ), $args );
	}


	/**
	 * Fetch and sort pickup locations by given args and sort settings.
	 *
	 * @since 2.0.0
	 *
	 * @param int[] $location_ids array of pickup location ids to fetch and sort
	 * @param array $args optional array of arguments to pass to `get_posts()`
	 * @return \WC_Local_Pickup_Plus_Pickup_Location[]
	 */
	private function gather_pickup_locations( array $location_ids, array $args ) {

		$found_locations = array();

		if ( ! empty( $location_ids ) && ( $shipping_method = wc_local_pickup_plus_shipping_method() ) ) {

			// limit to product availability when passed from arguments
			$post__in = ! empty( $args['post__in'] ) && is_array( $args['post__in'] ) ? array_intersect( $args['post__in'], $location_ids ) : $location_ids;

			if ( ! empty( $post__in ) ) {
				$args['post__in'] = $post__in;
			}

			// set results sorting order
			$orderby = ! empty( $args['orderby'] ) ? $args['orderby'] : $shipping_method->pickup_locations_sort_order();

			switch ( $orderby ) {

				case 'location_alphabetical' :
					$args['orderby'] = 'title';
					$args['order']   = 'ASC';
					$found_locations = $this->get_pickup_locations( $args );
				break;

				case 'location_date_added' :
					$args['orderby'] = 'date';
					$args['order']   = 'ASC';
					$found_locations = $this->get_pickup_locations( $args );
				break;

				case 'distance_customer' :
					$args['custom_order'] = $location_ids;
					$found_locations      = $this->get_pickup_locations( $args );
				break;

				default :
					$found_locations = $this->get_pickup_locations( $args );
				break;
			}
		}

		return $found_locations;
	}


	/**
	 * Get a pickup location.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WP_Post|\WC_Local_Pickup_Plus_Pickup_Location $location_id a location identifier
	 * @return null|\WC_Local_Pickup_Plus_Pickup_Location the pickup location object or false if none found
	 */
	public function get_pickup_location( $location_id = null ) {

		$location_post = $location_id;

		if ( 0 !== $location_post && empty( $location_post ) && isset( $GLOBALS['post'] ) ) {
			$location_post = $GLOBALS['post'];
		} elseif ( is_numeric( $location_id ) ) {
			$location_post = get_post( (int) $location_id );
		} elseif ( $location_id instanceof WC_Local_Pickup_Plus_Pickup_Location ) {
			$location_post = get_post( $location_id->get_id() );
		} elseif ( ! ( $location_id instanceof WP_Post ) ) {
			$location_post = null;
		}

		// if no acceptable post is found, bail out
		if ( ! $location_post || 'wc_pickup_location' !== get_post_type( $location_post ) ) {
			return null;
		}

		// set a pickup location object
		$pickup_location = new WC_Local_Pickup_Plus_Pickup_Location( $location_post );

		/**
		 * Get a pickup location.
		 *
		 * @since 2.0.0
		 *
		 * @param \WC_Local_Pickup_Plus_Pickup_Location $pickup_location the pickup location object
		 * @param \WP_Post $location_post the pickup $pickup_location post object
		 * @param int|string|\WP_Post|\WC_Local_Pickup_Plus_Pickup_Location $location_id the requested location id
		 */
		$pickup_location = apply_filters( 'wc_local_pickup_plus_get_pickup_location', $pickup_location, $location_post, $location_id );

		return $pickup_location instanceof WC_Local_Pickup_Plus_Pickup_Location ? $pickup_location : null;
	}


	/**
	 * Get pickup locations.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args optional array of arguments, passed to `get_posts()`
	 * @return int|int[]\WC_Local_Pickup_Plus_Pickup_Location[] $plans array of pickup location objects, IDs or count
	 */
	public function get_pickup_locations( $args = array() ) {

		$args = wp_parse_args( $args, array(
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		) );

		$args['post_type'] = 'wc_pickup_Location';

		// unique key for caching the results of the given query from passed args
		$cache_key = http_build_query( $args );

		if ( ! isset( $this->pickup_locations[ $cache_key ] ) ) {

			$pickup_locations_posts = get_posts( $args );

			$this->pickup_locations[ $cache_key ] = array();

			if ( ! empty( $pickup_locations_posts ) ) {

				if ( ! empty( $args['count'] ) ) {

					$found_locations = count( $pickup_locations_posts );

				} elseif ( isset( $args['fields'] ) && 'ids' === $args['fields'] ) {

					$found_locations = $pickup_locations_posts;

					if ( ! empty( $args['custom_order'] ) && is_array( $args['custom_order'] ) ) {

						$sorted_locations = array();

						foreach ( $args['custom_order'] as $pickup_location_id ) {

							if ( in_array( $pickup_location_id, $found_locations, false ) ) {
								$sorted_locations[] = $pickup_location_id;
							}
						}

						$found_locations = $sorted_locations;
					}

				} else {

					$found_locations = array();

					foreach ( $pickup_locations_posts as $post ) {

						if ( $pickup_location = $this->get_pickup_location( $post ) ) {
							$found_locations[ $pickup_location->get_id() ] = $pickup_location;
						}
					}

					if ( ! empty( $args['custom_order'] ) && is_array( $args['custom_order'] ) ) {

						$sorted_locations = array();

						foreach ( $args['custom_order'] as $pickup_location_id ) {

							if ( array_key_exists( $pickup_location_id, $found_locations ) ) {
								$sorted_locations[ $pickup_location_id ] = $found_locations[ $pickup_location_id ];
							}
						}

						$found_locations = $sorted_locations;
					}
				}

				$this->pickup_locations[ $cache_key ] = $found_locations;
			}
		}

		return $this->pickup_locations[ $cache_key ];
	}


	/**
	 * Count existing pickup locations.
	 *
	 * @since 2.0.0
	 *
	 * @param array $args optional additional args passed to get_posts() later
	 * @return int
	 */
	public function get_pickup_locations_count( $args = array() ) {

		$args['count']  = true;
		$args['fields'] = 'ids';

		return $this->get_pickup_locations( $args );
	}


	/**
	 * Init a new row in geodata custom table for a newly created pickup location.
	 *
	 * Should not be called directly. Intended as a callback when a new post is created.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id the pickup location post ID
	 * @param \WP_Post $post the pickup location post object
	 * @param bool $update whether this is a post being updated (true) or being created (false)
	 */
	public function init_geodata( $post_id, $post, $update ) {

		if ( ! $update && 'wc_pickup_location' === get_post_type( $post ) ) {
			global $wpdb;

			wc_local_pickup_plus()->check_tables();

			$geodata_table = $wpdb->prefix . 'woocommerce_pickup_locations_geodata';
			$record_exists = $wpdb->get_row( " SELECT * from {$geodata_table} WHERE post_id = {$post_id} " );

			if ( empty( $record_exists ) ) {

				$wpdb->insert(
					$geodata_table,
					array(
						'post_id'      => (int) $post_id,
						'title'        => $post->post_title,
						'last_updated' => date( 'Y-m-d H:i:s', current_time( 'timestamp', true ) ),
					),
					array( '%d', '%s', '%s' )
				);
			}
		}
	}


	/**
	 * Delete a pickup location related geodata from custom table when a pickup location post is deleted.
	 *
	 * Should not be called directly. Intended as a callback when a post is deleted.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id the pickup location post ID
	 */
	public function drop_geodata( $post_id ) {

		if ( 'wc_pickup_location' === get_post_type( $post_id ) ) {
			global $wpdb;

			wc_local_pickup_plus()->check_tables();

			$geodata_table = $wpdb->prefix . 'woocommerce_pickup_locations_geodata';

			$wpdb->delete(
				$geodata_table,
				array( 'post_id' => (int) $post_id, ),
				array( '%d' )
			);
		}
	}


}
