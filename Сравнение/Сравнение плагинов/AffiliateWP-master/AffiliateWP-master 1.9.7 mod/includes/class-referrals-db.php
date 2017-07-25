<?php
/**
 * Class Affiliate_WP_Referrals_DB
 *
 * @see Affiliate_WP_DB
 *
 * @property-read \AffWP\Referral\REST\v1\Endpoints $REST Referral REST endpoints.
 */
class Affiliate_WP_Referrals_DB extends Affiliate_WP_DB  {

	/**
	 * Cache group for queries.
	 *
	 * @internal DO NOT change. This is used externally both as a cache group and shortcut
	 *           for accessing db class instances via affiliate_wp()->{$cache_group}->*.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $cache_group = 'referrals';

	/**
	 * Object type to query for.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $query_object_type = 'AffWP\Referral';

	/**
	 * Get things started
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function __construct() {
		global $wpdb, $wp_version;

		if( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) {
			// Allows a single referrals table for the whole network
			$this->table_name  = 'affiliate_wp_referrals';
		} else {
			$this->table_name  = $wpdb->prefix . 'affiliate_wp_referrals';
		}
		$this->primary_key = 'referral_id';
		$this->version     = '1.1';

		// REST endpoints.
		if ( version_compare( $wp_version, '4.4', '>=' ) ) {
			$this->REST = new \AffWP\Referral\REST\v1\Endpoints;
		}
	}

	/**
	 * Retrieves a referral object.
	 *
	 * @since 1.9
	 * @access public
	 *
	 * @see Affiliate_WP_DB::get_core_object()
	 *
	 * @param int|object|AffWP\Referral $referral Referral ID or object.
	 * @return AffWP\Referral|null Referral object, null otherwise.
	 */
	public function get_object( $referral ) {
		return $this->get_core_object( $referral, $this->query_object_type );
	}

	/**
	 * Get columns and formats
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function get_columns() {
		return array(
			'referral_id' => '%d',
			'affiliate_id'=> '%d',
			'visit_id'    => '%d',
			'description' => '%s',
			'status'      => '%s',
			'amount'      => '%s',
			'currency'    => '%s',
			'custom'      => '%s',
			'context'     => '%s',
			'campaign'    => '%s',
			'reference'   => '%s',
			'products'    => '%s',
			'payout_id'   => '%d',
			'date'        => '%s',
		);
	}

	/**
	 * Get default column values
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function get_column_defaults() {
		return array(
			'affiliate_id' => 0,
			'date'         => date( 'Y-m-d H:i:s' ),
			'currency'     => affwp_get_currency()
		);
	}

	/**
	 * Adds a referral.
	 *
	 * @access  public
	 * @since   1.0
	 *
	 * @param array $data {
	 *     Optional. Referral data. Default empty array.
	 *
	 *     @type string $status Referral status. Default 'pending'.
	 *     @type int    $amount Referral amount. Defualt 0.
	 * }
	 * @return int|false Referral ID if successfully added, false otherwise.
	*/
	public function add( $data = array() ) {

		$defaults = array(
			'status' => 'pending',
			'amount' => 0
		);

		$args = wp_parse_args( $data, $defaults );

		if( empty( $args['affiliate_id'] ) ) {
			return false;
		}

		if( ! affiliate_wp()->affiliates->affiliate_exists( $args['affiliate_id'] ) ) {
			return false;
		}

		$args['amount'] = affwp_sanitize_amount( $args['amount'] );

		if( ! empty( $args['products'] ) ) {
			$args['products'] = maybe_serialize( $args['products'] );
		}

		$add  = $this->insert( $args, 'referral' );

		if ( $add ) {

			/**
			 * Fires once a new referral has successfully been inserted into the database.
			 *
			 * @since 1.6
			 *
			 * @param int $add Referral ID.
			 */
			do_action( 'affwp_insert_referral', $add );

			return $add;
		}

		return false;

	}

	/**
	 * Update a referral.
	 *
	 * @access  public
	 * @since   1.5
	 *
	 * @param int|AffWP\Referral $referral Referral ID or object.
	*/
	public function update_referral( $referral = 0, $data = array() ) {

		if ( ! $referral = affwp_get_referral( $referral ) ) {
			return false;
		}

		if( isset( $data['amount'] ) ) {
			$data['amount'] = affwp_sanitize_amount( $data['amount'] );
		}

		if( ! empty( $data['products'] ) ) {
			$data['products'] = maybe_serialize( $data['products'] );
		}

		if ( ! empty( $data['date'] ) ) {
			$data['date'] = date_i18n( 'Y-m-d H:i:s', strtotime( $data['date'] ) );
		}

		$update = $this->update( $referral->ID, $data, '', 'referral' );

		if( $update ) {

			if( ! empty( $data['status'] ) && $referral->status !== $data['status'] ) {

				affwp_set_referral_status( $referral->ID, $data['status'] );

			} elseif( 'paid' === $referral->status ) {

				if( $referral->amount > $data['amount'] ) {

					$change = $referral->amount - $data['amount'];
					affwp_decrease_affiliate_earnings( $referral->affiliate_id, $change );

				} elseif( $referral->amount < $data['amount'] ) {

					$change = $data['amount'] - $referral->amount;
					affwp_increase_affiliate_earnings( $referral->affiliate_id, $change );

				}

			}

			return $update;
		}

		return false;

	}

	/**
	 * Retrieves a referral by a specific field.
	 *
	 * @access  public
	 * @since   1.0
	 *
	 * @param string $column  Column name. See get_columns().
	 * @param string $context Optional. Context for which to retrieve a referral. Default empty.
	 * @return object|null Database query result object or null on failure.
	*/
	public function get_by( $column, $row_id, $context = '' ) {
		global $wpdb;

		$and = '';
		if( ! empty( $context ) ) {
			$and = " AND context = '" . esc_sql( $context ) . "'";
		}

		return $wpdb->get_row( $wpdb->prepare(  "SELECT * FROM $this->table_name WHERE $column = '%s'$and LIMIT 1;", $row_id ) );
	}

	/**
	 * Retrieves referrals from the database.
	 *
	 * @access  public
	 * @since   1.0
	 * @param array $args {
	 *     Optional. Arguments to retrieve referrals from the database.
	 *
	 *     @type int          $number         Number of referrals to retrieve. Accepts -1 for all. Default 20.
	 *     @type int          $offset         Number of referrals to offset in the query. Default 0.
	 *     @type int|array    $referral_id    Specific referral ID or array of IDs to query for. Default 0 (all).
	 *     @type int|array    $affiliate_id   Affiliate ID or array of IDs to query referrals for. Default 0 (all).
	 *     @type int|array    $payout_id      Payout ID or array of IDs to query referrals for. Default 0 (all).
	 *     @type float|array  $amount {
	 *         Specific amount to query for or min/max range. If float, can be used with `$amount_compare`.
	 *         If array, `BETWEEN` is used.
	 *
	 *         @type float $min Minimum amount to query for.
	 *         @type float $max Maximum amount to query for.
	 *     }
	 *     @type string       $amount_compare Comparison operator to use with `$amount`. Accepts '>', '<', '>=',
	 *                                        '<=', '=', or '!='. Default '='.
	 *     @type string|array $date {
	 *         Date string or start/end range to retrieve referrals for.
	 *
	 *         @type string $start Start date to retrieve referrals for.
	 *         @type string $end   End date to retrieve referrals for.
	 *     }
	 *     @type string       $reference      Specific reference to query referrals for (usually an order number).
	 *                                        Default empty.
	 *     @type string       $context        Specific context to query referrals for. Default empty.
	 *     @type string       $campaign       Specific campaign to query referrals for. Default empty.
	 *     @type string|array $status         Referral status or array of statuses to query referrals for.
	 *                                        Default empty (all).
	 *     @type string       $orderby        Column to order results by. Accepts any valid referrals table column.
	 *                                        Default 'referral_id'.
	 *     @type string       $order          How to order results. Accepts 'ASC' (ascending) or 'DESC' (descending).
	 *                                        Default 'DESC'.
	 *     @type bool         $search         Whether a search query is being performed. Default false.
	 *     @type string       $fields         Fields to query for. Accepts 'ids' or '*' (all). Default '*'.
	 * }
	 * @param   bool  $count  Optional. Whether to return only the total number of results found. Default false.
	*/
	public function get_referrals( $args = array(), $count = false ) {

		global $wpdb;

		$defaults = array(
			'number'       => 20,
			'offset'       => 0,
			'referral_id'  => 0,
			'payout_id'    => 0,
			'affiliate_id' => 0,
			'amount'       => 0,
			'amount_compare' => '=',
			'reference'    => '',
			'context'      => '',
			'campaign'     => '',
			'status'       => '',
			'orderby'      => 'referral_id',
			'order'        => 'DESC',
			'search'       => false,
			'fields'       => '',
		);

		$args  = wp_parse_args( $args, $defaults );

		if( $args['number'] < 1 ) {
			$args['number'] = 999999999999;
		}

		$where = $join = '';

		// Specific referrals
		if( ! empty( $args['referral_id'] ) ) {

			if( is_array( $args['referral_id'] ) ) {
				$referral_ids = implode( ',', array_map( 'intval', $args['referral_id'] ) );
			} else {
				$referral_ids = intval( $args['referral_id'] );
			}

			$where .= "WHERE `referral_id` IN( {$referral_ids} ) ";

		}

		// Referrals for specific affiliates
		if( ! empty( $args['affiliate_id'] ) ) {

			if( is_array( $args['affiliate_id'] ) ) {
				$affiliate_ids = implode( ',', array_map( 'intval', $args['affiliate_id'] ) );
			} else {
				$affiliate_ids = intval( $args['affiliate_id'] );
			}

			$where .= "WHERE `affiliate_id` IN( {$affiliate_ids} ) ";

		}

		// Referrals for specific payouts
		if( ! empty( $args['payout_id'] ) ) {

			if( is_array( $args['payout_id'] ) ) {
				$payout_ids = implode( ',', array_map( 'intval', $args['payout_id'] ) );
			} else {
				$payout_ids = intval( $args['payout_id'] );
			}

			$where .= "WHERE `payout_id` IN( {$payout_ids} ) ";

		}

		// Amount.
		if ( ! empty( $args['amount'] ) ) {

			$amount = $args['amount'];

			$where .= empty( $where ) ? " WHERE" : " AND";

			if ( is_array( $amount ) && ! empty( $amount['min'] ) && ! empty( $amount['max'] ) ) {

				$minimum = absint( $amount['min'] );
				$maximum = absint( $amount['max'] );

				$where .= " `amount` BETWEEN {$minimum} AND {$maximum}";
			} else {

				$amount  = absint( $amount );
				$compare = '=';

				if ( ! empty( $args['amount_compare'] ) ) {
					$compare = $args['amount_compare'];

					if ( ! in_array( $compare, array( '>', '<', '>=', '<=', '=', '!=' ) ) ) {
						$compare = '=';
					}
				}

				$where .= " `amount` {$compare} {$amount}";
			}
		}

		if( ! empty( $args['status'] ) ) {

			if( empty( $where ) ) {
				$where .= " WHERE";
			} else {
				$where .= " AND";
			}

			if( is_array( $args['status'] ) ) {
				$where .= " `status` IN('" . implode( "','", array_map( 'esc_sql', $args['status'] ) ) . "') ";
			} else {
				$where .= " `status` = '" . esc_sql( $args['status'] ) . "' ";
			}

		}

		if( ! empty( $args['date'] ) ) {

			if( is_array( $args['date'] ) ) {

				if( ! empty( $args['date']['start'] ) ) {

					if( false !== strpos( $args['date']['start'], ':' ) ) {
						$format = 'Y-m-d H:i:s';
					} else {
						$format = 'Y-m-d 00:00:00';
					}

					$start = esc_sql( date( $format, strtotime( $args['date']['start'] ) ) );

					if ( ! empty( $where ) ) {
						$where .= " AND `date` >= '{$start}'";
					} else {
						$where .= " WHERE `date` >= '{$start}'";
					}

				}

				if ( ! empty( $args['date']['end'] ) ) {

					if ( false !== strpos( $args['date']['end'], ':' ) ) {
						$format = 'Y-m-d H:i:s';
					} else {
						$format = 'Y-m-d 23:59:59';
					}

					$end = esc_sql( date( $format, strtotime( $args['date']['end'] ) ) );

					if( ! empty( $where ) ) {
						$where .= " AND `date` <= '{$end}'";
					} else {
						$where .= " WHERE `date` <= '{$end}'";
					}

				}

			} else {

				$year  = date( 'Y', strtotime( $args['date'] ) );
				$month = date( 'm', strtotime( $args['date'] ) );
				$day   = date( 'd', strtotime( $args['date'] ) );

				if( empty( $where ) ) {
					$where .= " WHERE";
				} else {
					$where .= " AND";
				}

				$where .= " $year = YEAR ( date ) AND $month = MONTH ( date ) AND $day = DAY ( date )";
			}

		}

		if( ! empty( $args['reference'] ) ) {

			if( empty( $where ) ) {
				$where .= " WHERE";
			} else {
				$where .= " AND";
			}

			if( is_array( $args['reference'] ) ) {
				$where .= " `reference` IN(" . implode( ',', array_map( 'esc_sql', $args['reference'] ) ) . ") ";
			} else {
				$reference = esc_sql( $args['reference'] );

				if( ! empty( $args['search'] ) ) {
					$where .= " `reference` LIKE '%%" . $reference . "%%' ";
				} else {
					$where .= " `reference` = '" . $reference . "' ";
				}
			}

		}

		if( ! empty( $args['context'] ) ) {

			if( empty( $where ) ) {
				$where .= " WHERE";
			} else {
				$where .= " AND";
			}

			if( is_array( $args['context'] ) ) {
				$where .= " `context` IN('" . implode( "','", array_map( 'esc_sql', $args['context'] ) ) . "') ";
			} else {
				$context = esc_sql( $args['context'] );

				if ( ! empty( $args['search'] ) ) {
					$where .= " `context` LIKE '%%" . $context . "%%' ";
				} else {
					$where .= " `context` = '" . $context . "' ";
				}
			}

		}

		if( ! empty( $args['campaign'] ) ) {

			if( empty( $where ) ) {
				$where .= " WHERE";
			} else {
				$where .= " AND";
			}

			if( is_array( $args['campaign'] ) ) {
				$where .= " `campaign` IN(" . implode( ',', array_map( 'esc_sql', $args['campaign'] ) ) . ") ";
			} else {
				$campaign = esc_sql( $args['campaign'] );

				if ( ! empty( $args['search'] ) ) {
					$where .= " `campaign` LIKE '%%" . $campaign . "%%' ";
				} else {
					$where .= " `campaign` = '" . $campaign . "' ";
				}
			}

		}

		$orderby = array_key_exists( $args['orderby'], $this->get_columns() ) ? $args['orderby'] : $this->primary_key;

		// Non-column orderby exception;
		if ( 'amount' === $args['orderby'] ) {
			$orderby = 'amount+0';
		}

		// There can be only two orders.
		if ( 'DESC' === strtoupper( $args['order'] ) ) {
			$order = 'DESC';
		} else {
			$order = 'ASC';
		}

		// Overload args values for the benefit of the cache.
		$args['orderby'] = $orderby;
		$args['order']   = $order;

		$fields = "*";

		if ( ! empty( $args['fields'] ) ) {
			if ( 'ids' === $args['fields'] ) {
				$fields = "$this->primary_key";
			} elseif ( array_key_exists( $args['fields'], $this->get_columns() ) ) {
				$fields = $args['fields'];
			}
		}

		$key = ( true === $count ) ? md5( 'affwp_referrals_count' . serialize( $args ) ) : md5( 'affwp_referrals_' . serialize( $args ) );

		$last_changed = wp_cache_get( 'last_changed', $this->cache_group );
		if ( ! $last_changed ) {
			wp_cache_set( 'last_changed', microtime(), $this->cache_group );
		}

		$cache_key = "{$key}:{$last_changed}";

		$results = wp_cache_get( $cache_key, $this->cache_group );

		if ( false === $results ) {

			$clauses = compact( 'fields', 'join', 'where', 'orderby', 'order', 'count' );

			$results = $this->get_results( $clauses, $args, 'affwp_get_referral' );
		}

		wp_cache_add( $cache_key, $results, $this->cache_group, HOUR_IN_SECONDS );

		return $results;

	}

	/**
	 * Return the number of results found for a given query
	 *
	 * @param  array  $args
	 * @return int
	 */
	public function count( $args = array() ) {
		return $this->get_referrals( $args, true );
	}

	/**
	 * Get the total paid earnings
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function paid_earnings( $date = '', $affiliate_id = 0, $format = true ) {

		$args                 = array();
		$args['status']       = 'paid';
		$args['affiliate_id'] = $affiliate_id;
		$args['number']       = '-1';

		if( 'alltime' == $date ) {
			return $this->get_alltime_earnings();
		}

		if( ! empty( $date ) ) {

			// Back-compat for string date rates.
			if ( is_string( $date ) ) {
				switch ( $date ) {

					case 'month' :

						$date = array(
							'start' => date( 'Y-m-01 00:00:00', current_time( 'timestamp' ) ),
							'end'   => date( 'Y-m-' . cal_days_in_month( CAL_GREGORIAN, date( 'n' ), date( 'Y' ) ) . ' 23:59:59', current_time( 'timestamp' ) ),
						);
						break;

					case 'last-month':
						$date = array(
							'start' => date( 'Y-m-01 00:00:00', ( current_time( 'timestamp' ) - MONTH_IN_SECONDS ) ),
							'end'   => date( 'Y-m-' . cal_days_in_month( CAL_GREGORIAN, date( 'n' ), date( 'Y' ) ) . ' 23:59:59', ( current_time( 'timestamp' ) - MONTH_IN_SECONDS ) ),
						);
						break;
				}
			}

			$args['date'] = $date;
		}

		$referrals = $this->get_referrals( $args );

		$earnings  = array_sum( wp_list_pluck( $referrals, 'amount' ) );

		if( $format ) {
			$earnings = affwp_currency_filter( affwp_format_amount( $earnings ) );
		}

		return $earnings;

	}

	/**
	 * Get the total unpaid earnings
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function get_alltime_earnings() {
		return get_option( 'affwp_alltime_earnings', 0.00 );
	}

	/**
	 * Get the total unpaid earnings
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function unpaid_earnings( $date = '', $affiliate_id = 0, $format = true ) {

		$args                 = array();
		$args['status']       = 'unpaid';
		$args['affiliate_id'] = $affiliate_id;
		$args['number']       = '-1';

		if( ! empty( $date ) ) {

			if ( is_string( $date ) ) {
				switch( $date ) {

					case 'month' :

						$date = array(
							'start' => date( 'Y-m-01 00:00:00', current_time( 'timestamp' ) ),
							'end'   => date( 'Y-m-' . cal_days_in_month( CAL_GREGORIAN, date( 'n' ), date( 'Y' ) ) . ' 23:59:59', current_time( 'timestamp' ) ),
						);
						break;

					case 'last-month' :

						$date = array(
							'start' => date( 'Y-m-01 00:00:00', ( current_time( 'timestamp' ) - MONTH_IN_SECONDS ) ),
							'end'   => date( 'Y-m-' . cal_days_in_month( CAL_GREGORIAN, date( 'n' ), date( 'Y' ) ) . ' 23:59:59', ( current_time( 'timestamp' ) - MONTH_IN_SECONDS ) ),
						);
						break;

				}
			}

			$args['date'] = $date;
		}

		$referrals = $this->get_referrals( $args );

		$earnings  = array_sum( wp_list_pluck( $referrals, 'amount' ) );

		if( $format ) {
			$earnings = affwp_currency_filter( affwp_format_amount( $earnings ) );
		}

		return $earnings;

	}

	/**
	 * Counts the total number of referrals for the given status.
	 *
	 * @access public
	 * @since  1.8.6
	 *
	 * @param string $status       Referral status.
	 * @param int    $affiliate_id Optional. Affiliate ID. Default 0.
	 * @param string $date         Optional. Date range in which to search. Accepts 'month'. Default empty.
	 * @return int Number of referrals for the given status or 0 if the affiliate doesn't exist.
	 */
	public function count_by_status( $status, $affiliate_id = 0, $date = '' ) {

		$args = array(
			'status'       => $status,
			'affiliate_id' => absint( $affiliate_id ),
		);

		if ( ! empty( $date ) ) {

			// Whitelist for back-compat string values.
			if ( is_string( $date ) && ! in_array( $date, array( 'month', 'last-month' ) ) ) {
				$date = '';
			}

			if ( is_string( $date ) ) {
				switch( $date ) {
					case 'month':
						$date = array(
							'start' => date( 'Y-m-01 00:00:00', current_time( 'timestamp' ) ),
							'end'   => date( 'Y-m-' . cal_days_in_month( CAL_GREGORIAN, date( 'n' ), date( 'Y' ) ) . ' 23:59:59', current_time( 'timestamp' ) ),
						);
						break;

					case 'last-month':
						$date = array(
							'start' => date( 'Y-m-01 00:00:00', ( current_time( 'timestamp' ) - MONTH_IN_SECONDS ) ),
							'end'   => date( 'Y-m-' . cal_days_in_month( CAL_GREGORIAN, date( 'n' ), date( 'Y' ) ) . ' 23:59:59', ( current_time( 'timestamp' ) - MONTH_IN_SECONDS ) ),
						);
						break;
				}
			}
			$args['date'] = $date;
		}

		return $this->count( $args );
	}

	/**
	 * Count the total number of unpaid referrals
	 *
	 * @access  public
	 * @since   1.0
	 * @since   1.8.6 Converted to a wrapper for count_by_status()
	 *
	 * @see count_by_status()
	 *
	 * @param string $date         Optional. Date range in which to search. Accepts 'month'. Default empty.
	 * @param int    $affiliate_id Optional. Affiliate ID. Default 0.
	 * @return int Number of referrals for the given status or 0 if the affiliate doesn't exist.
	*/
	public function unpaid_count( $date = '', $affiliate_id = 0 ) {
		return $this->count_by_status( 'unpaid', $affiliate_id, $date );
	}

	/**
	 * Set the status of multiple referrals at once
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function bulk_update_status( $referral_ids = array(), $status = '' ) {

		global $wpdb;

		if( empty( $referral_ids ) ) {
			return false;
		}

		if( empty( $status ) ) {
			return false;
		}

		$referral_ids = implode( ',', array_map( 'intval', $referral_ids ) );

		// Not working yet
		$update = $wpdb->query( $wpdb->prepare( "UPDATE $this->table_name SET status = '%s' WHERE $this->primary_key IN(%s)", $status, $referral_ids ) );

		if( $update ) {
			return true;
		}
		return false;
	}

	/**
	 * Create the table
	 *
	 * @access  public
	 * @since   1.0
	*/
	public function create_table() {

		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE " . $this->table_name . " (
		referral_id bigint(20) NOT NULL AUTO_INCREMENT,
		affiliate_id bigint(20) NOT NULL,
		visit_id bigint(20) NOT NULL,
		description longtext NOT NULL,
		status tinytext NOT NULL,
		amount mediumtext NOT NULL,
		currency char(3) NOT NULL,
		custom longtext NOT NULL,
		context tinytext NOT NULL,
		campaign varchar(30) NOT NULL,
		reference mediumtext NOT NULL,
		products mediumtext NOT NULL,
		payout_id bigint(20) NOT NULL,
		date datetime NOT NULL,
		PRIMARY KEY  (referral_id),
		KEY affiliate_id (affiliate_id)
		) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}
}
