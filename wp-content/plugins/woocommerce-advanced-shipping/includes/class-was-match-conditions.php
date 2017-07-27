<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class WAS_Match_Conditions.
 *
 * The WAS Match Conditions class handles the matching rules for Shipping methods.
 *
 * @class		WAS_Match_Conditions
 * @author		Jeroen Sormani
 * @package 	WooCommerce Advanced Shipping
 * @version	1.0.0
 */
class WAS_Match_Conditions {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_filter( 'was_match_condition_subtotal', array( $this, 'was_match_condition_subtotal' ), 10, 4 );
		add_filter( 'was_match_condition_subtotal_ex_tax', array( $this, 'was_match_condition_subtotal_ex_tax' ), 10, 4 );
		add_filter( 'was_match_condition_tax', array( $this, 'was_match_condition_tax' ), 10, 4 );
		add_filter( 'was_match_condition_quantity', array( $this, 'was_match_condition_quantity' ), 10, 4 );
		add_filter( 'was_match_condition_contains_product', array( $this, 'was_match_condition_contains_product' ), 10, 4 );
		add_filter( 'was_match_condition_coupon', array( $this, 'was_match_condition_coupon' ), 10, 4 );
		add_filter( 'was_match_condition_weight', array( $this, 'was_match_condition_weight' ), 10, 4 );
		add_filter( 'was_match_condition_contains_shipping_class', array( $this, 'was_match_condition_contains_shipping_class' ), 10, 4 );

		add_filter( 'was_match_condition_zipcode', array( $this, 'was_match_condition_zipcode' ), 10, 4 );
		add_filter( 'was_match_condition_city', array( $this, 'was_match_condition_city' ), 10, 4 );
		add_filter( 'was_match_condition_state', array( $this, 'was_match_condition_state' ), 10, 4 );
		add_filter( 'was_match_condition_country', array( $this, 'was_match_condition_country' ), 10, 4 );
		add_filter( 'was_match_condition_role', array( $this, 'was_match_condition_role' ), 10, 4 );

		add_filter( 'was_match_condition_width', array( $this, 'was_match_condition_width' ), 10, 4 );
		add_filter( 'was_match_condition_height', array( $this, 'was_match_condition_height' ), 10, 4 );
		add_filter( 'was_match_condition_length', array( $this, 'was_match_condition_length' ), 10, 4 );
		add_filter( 'was_match_condition_stock', array( $this, 'was_match_condition_stock' ), 10, 4 );
		add_filter( 'was_match_condition_stock_status', array( $this, 'was_match_condition_stock_status' ), 10, 4 );
		add_filter( 'was_match_condition_category', array( $this, 'was_match_condition_category' ), 10, 4 );

	}


	/**
	 * Subtotal.
	 *
	 * Match the condition value against the cart subtotal.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @param  array  $package  List of shipping package details.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function was_match_condition_subtotal( $match, $operator, $value, $package ) {

		if ( ! isset( WC()->cart ) ) :
			return $match;
		endif;

		// Make sure its formatted correct
		$value = str_replace( ',', '.', $value );

		// WPML multi-currency support
		$value = apply_filters( 'wcml_shipping_price_amount', $value );

		$subtotal = WC()->cart->subtotal;

		if ( '==' == $operator ) :
			$match = ( $subtotal == $value );
		elseif ( '!=' == $operator ) :
			$match = ( $subtotal != $value );
		elseif ( '>=' == $operator ) :
			$match = ( $subtotal >= $value );
		elseif ( '<=' == $operator ) :
			$match = ( $subtotal <= $value );
		endif;

		return $match;

	}


	/**
	 * Subtotal excl. taxes.
	 *
	 * Match the condition value against the cart subtotal excl. taxes.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @param  array  $package  List of shipping package details.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function was_match_condition_subtotal_ex_tax( $match, $operator, $value, $package ) {

		if ( ! isset( WC()->cart ) ) :
			return $match;
		endif;

		// Make sure its formatted correct
		$value = str_replace( ',', '.', $value );

		// WPML multi-currency support
		$value = apply_filters( 'wcml_shipping_price_amount', $value );

		$subtotal = WC()->cart->subtotal_ex_tax;

		if ( '==' == $operator ) :
			$match = ( $subtotal == $value );
		elseif ( '!=' == $operator ) :
			$match = ( $subtotal != $value );
		elseif ( '>=' == $operator ) :
			$match = ( $subtotal >= $value );
		elseif ( '<=' == $operator ) :
			$match = ( $subtotal <= $value );
		endif;


		return $match;

	}


	/**
	 * Taxes.
	 *
	 * Match the condition value against the cart taxes.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @param  array  $package  List of shipping package details.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function was_match_condition_tax( $match, $operator, $value, $package ) {

		if ( ! isset( WC()->cart ) ) :
			return $match;
		endif;

		$taxes = array_sum( (array) WC()->cart->taxes );

		// WPML multi-currency support
		$value = apply_filters( 'wcml_shipping_price_amount', $value );

		if ( '==' == $operator ) :
			$match = ( $taxes == $value );
		elseif ( '!=' == $operator ) :
			$match = ( $taxes != $value );
		elseif ( '>=' == $operator ) :
			$match = ( $taxes >= $value );
		elseif ( '<=' == $operator ) :
			$match = ( $taxes <= $value );
		endif;

		return $match;

	}


	/**
	 * Quantity.
	 *
	 * Match the condition value against the cart quantity.
	 * This also includes product quantities.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @param  array  $package  List of shipping package details.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function was_match_condition_quantity( $match, $operator, $value, $package ) {

		if ( ! isset( WC()->cart ) ) :
			return $match;
		endif;

		$quantity = 0;
		foreach ( $package['contents'] as $item_key => $item ) :
			$quantity += $item['quantity'];
		endforeach;

		if ( '==' == $operator ) :
			$match = ( $quantity == $value );
		elseif ( '!=' == $operator ) :
			$match = ( $quantity != $value );
		elseif ( '>=' == $operator ) :
			$match = ( $quantity >= $value );
		elseif ( '<=' == $operator ) :
			$match = ( $quantity <= $value );
		endif;

		return $match;

	}


	/**
	 * Contains product.
	 *
	 * Matches if the condition value product is in the cart.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @param  array  $package  List of shipping package details.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function was_match_condition_contains_product( $match, $operator, $value, $package ) {

		$product_ids = array();
		foreach ( $package['contents'] as $product ) :
			$product_ids[] = $product['product_id'];
			if ( isset( $product['variation_id'] ) ) {
				$product_ids[] = $product['variation_id'];
			}
		endforeach;

		if ( '==' == $operator ) :
			$match = ( in_array( $value, $product_ids ) );
		elseif ( '!=' == $operator ) :
			$match = ( ! in_array( $value, $product_ids ) );
		endif;

		return $match;

	}


	/**
	 * Coupon.
	 *
	 * Match the condition value against the applied coupons.
	 *
	 * @since 1.0.0
	 * @since 1.0.10 - Add capability to set condition based on coupon amount/percentage
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @param  array  $package  List of shipping package details.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function was_match_condition_coupon( $match, $operator, $value, $package ) {

		if ( ! isset( WC()->cart ) ) :
			return $match;
		endif;

		$coupons = array( 'percent' => array(), 'fixed' => array() );
		foreach ( WC()->cart->get_coupons() as $coupon ) {
			/** @var $coupon WC_Coupon */
			if ( version_compare( WC()->version, '2.7', '>=' ) ) {
				$type               = str_replace( '_product', '', $coupon->get_discount_type() );
				$type               = str_replace( '_cart', '', $type );
				$coupons[ $type ][] = $coupon->get_amount();
			} else {
				$type               = str_replace( '_product', '', $coupon->discount_type );
				$type               = str_replace( '_cart', '', $type );
				$coupons[ $type ][] = $coupon->coupon_amount;
			}
		}

		// Match against coupon percentage
		if ( strpos( $value, '%' ) !== false ) {

			$percentage_value = str_replace( '%', '', $value );
			if ( '==' == $operator ) :
				$match = in_array( $percentage_value, $coupons['percent'] );
			elseif ( '!=' == $operator ) :
				$match = ! in_array( $percentage_value, $coupons['percent'] );
			elseif ( '>=' == $operator ) :
				$match = empty( $coupons['percent'] ) ? $match : ( min( $coupons['percent'] ) >= $percentage_value );
			elseif ( '<=' == $operator ) :
				$match = ! is_array( $coupons['percent'] ) ? false : ( max( $coupons['percent'] ) <= $percentage_value );
			endif;

		// Match against coupon amount
		} elseif ( strpos( $value, '$' ) !== false ) {

			$amount_value = str_replace( '$', '', $value );
			if ( '==' == $operator ) :
				$match = in_array( $amount_value, $coupons['fixed'] );
			elseif ( '!=' == $operator ) :
				$match = ! in_array( $amount_value, $coupons['fixed'] );
			elseif ( '>=' == $operator ) :
				$match = empty( $coupons['fixed'] ) ? $match : ( min( $coupons['fixed'] ) >= $amount_value );
			elseif ( '<=' == $operator ) :
				$match = ! is_array( $coupons['fixed'] ) ? $match : ( max( $coupons['fixed'] ) <= $amount_value );
			endif;

		// Match coupon codes
		} else {

			if ( '==' == $operator ) :
				$match = ( in_array( $value, WC()->cart->get_applied_coupons() ) );
			elseif ( '!=' == $operator ) :
				$match = ( ! in_array( $value, WC()->cart->get_applied_coupons() ) );
			endif;

		}

		return $match;

	}


	/**
	 * Weight.
	 *
	 * Match the condition value against the cart weight.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @param  array  $package  List of shipping package details.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function was_match_condition_weight( $match, $operator, $value, $package ) {

		$weight = 0;
		foreach ( $package['contents'] as $key => $item ) :
			/** @var $product WC_Product */
			$product = $item['data'];
			$weight += ( (float) $product->get_weight() * (int) $item['quantity'] );
		endforeach;

		$value = (string) $value;

		// Make sure its formatted correct
		$value = str_replace( ',', '.', $value );

		if ( '==' == $operator ) :
			$match = ( $weight == $value );
		elseif ( '!=' == $operator ) :
			$match = ( $weight != $value );
		elseif ( '>=' == $operator ) :
			$match = ( $weight >= $value );
		elseif ( '<=' == $operator ) :
			$match = ( $weight <= $value );
		endif;

		return $match;

	}


	/**
	 * Shipping class.
	 *
	 * Matches if the condition value shipping class is in the cart.
	 *
	 * @since 1.0.1
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @param  array  $package  List of shipping package details.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function was_match_condition_contains_shipping_class( $match, $operator, $value, $package ) {

		// True until proven false
		if ( $operator == '!=' ) :
			$match = true;
		endif;

		foreach ( $package['contents'] as $key => $product ) :

			$id      = ! empty( $product['variation_id'] ) ? $product['variation_id'] : $product['product_id'];
			$product = wc_get_product( $id );

			if ( $operator == '==' ) :
				if ( $product->get_shipping_class() == $value ) :
					return true;
				endif;
			elseif ( $operator == '!=' ) :
				if ( $product->get_shipping_class() == $value ) :
					return false;
				endif;
			endif;

		endforeach;

		return $match;

	}


/******************************************************
 * User conditions
 *****************************************************/


	/**
	 * Zipcode.
	 *
	 * Match the condition value against the users shipping zipcode.
	 *
	 * @since 1.0.0
	 * @since 1.0.9 - Add support for wildcards with asterisk (*)
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @param  array  $package  List of shipping package details.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function was_match_condition_zipcode( $match, $operator, $value, $package ) {

		$user_zipcode = $package['destination']['postcode'];

		// Prepare allowed values.
		$zipcodes = (array) preg_split( '/,+ */', $value );

		// Remove all non- letters and numbers
		foreach ( $zipcodes as $key => $zipcode ) :
			$zipcodes[ $key ] = preg_replace( '/[^0-9a-zA-Z\-\*]/', '', $zipcode );
		endforeach;

		if ( '==' == $operator ) :

			foreach ( $zipcodes as $zipcode ) :

				// @since 1.0.9 - Wildcard support (*)
				if ( strpos( $zipcode, '*' ) !== false ) :

					$user_zipcode = preg_replace( '/[^0-9a-zA-Z]/', '', $user_zipcode );
					$zipcode      = str_replace( '*', '', $zipcode );

					if ( empty( $zipcode ) ) continue;

					$parts = explode( '-', $zipcode );
					if ( count( $parts ) > 1 ) :
						$match = ( $user_zipcode >= min( $parts ) && $user_zipcode <= max( $parts ) );
					else :
						$match = preg_match( '/^' . preg_quote( $zipcode, '/' ) . '/i', $user_zipcode );
					endif;

				else :

					// BC when not using asterisk (wildcard)
					$match = ( (double) $user_zipcode == (double) $zipcode );

				endif;

				if ( $match == true ) {
					return true;
				}

			endforeach;

		elseif ( '!=' == $operator ) :

			// True until proven false
			$match = true;

			foreach ( $zipcodes as $zipcode ) :

				// @since 1.0.9 - Wildcard support (*)
				if ( strpos( $zipcode, '*' ) !== false ) :

					$user_zipcode = preg_replace( '/[^0-9a-zA-Z]/', '', $user_zipcode );
					$zipcode      = str_replace( '*', '', $zipcode );

					if ( empty( $zipcode ) ) continue;

					$parts = explode( '-', $zipcode );
					if ( count( $parts ) > 1 ) :
						$zipcode_match = ( $user_zipcode >= min( $parts ) && $user_zipcode <= max( $parts ) );
					else :
						$zipcode_match = preg_match( '/^' . preg_quote( $zipcode, '/' ) . '/i', $user_zipcode );
					endif;

					if ( $zipcode_match == true ) :
						return $match = false;
					endif;

				else :

					// BC when not using asterisk (wildcard)
					$zipcode_match = ( (double) $user_zipcode == (double) $zipcode );

					if ( $zipcode_match == true ) :
						return $match = false;
					endif;

				endif;

			endforeach;

		elseif ( '>=' == $operator ) :
			$match = ( (double) $user_zipcode >= (double) $value );
		elseif ( '<=' == $operator ) :
			$match = ( (double) $user_zipcode <= (double) $value );
		endif;

		return $match;

	}


	/**
	 * City.
	 *
	 * Match the condition value against the users shipping city.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @param  array  $package  List of shipping package details.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function was_match_condition_city( $match, $operator, $value, $package ) {

		if ( ! isset( WC()->customer ) ) return $match;

		$customer_city = strtolower( WC()->customer->get_shipping_city() );
		$value         = strtolower( $value );

		if ( '==' == $operator ) :

			if ( preg_match( '/\, ?/', $value ) ) :
				$match = ( in_array( $customer_city, preg_split( '/\, ?/', $value ) ) );
			else :
				$match = ( $value == $customer_city );
			endif;

		elseif ( '!=' == $operator ) :

			if ( preg_match( '/\, ?/', $value ) ) :
				$match = ( ! in_array( $customer_city, preg_split( '/\, ?/', $value ) ) );
			else :
				$match = ! ( $value == $customer_city );
			endif;

		endif;

		return $match;

	}


	/**
	 * State.
	 *
	 * Match the condition value against the users shipping state
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @param  array  $package  List of shipping package details.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function was_match_condition_state( $match, $operator, $value, $package ) {

		if ( ! isset( WC()->customer ) ) :
			return $match;
		endif;

		$state = WC()->customer->get_shipping_country() . '_' . WC()->customer->get_shipping_state();

		if ( '==' == $operator ) :
			$match = ( $state == $value );
		elseif ( '!=' == $operator ) :
			$match = ( $state != $value );
		endif;

		return $match;

	}


	/**
	 * Country.
	 *
	 * Match the condition value against the users shipping country.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @param  array  $package  List of shipping package details.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function was_match_condition_country( $match, $operator, $value, $package ) {

		if ( ! isset( WC()->customer ) ) :
			return $match;
		endif;

		$user_country = WC()->customer->get_shipping_country();

		if ( method_exists( WC()->countries, 'get_continent_code_for_country' ) ) :
			$user_continent = WC()->countries->get_continent_code_for_country( $user_country );
		endif;

		if ( '==' == $operator ) :
			$match = stripos( $user_country, $value ) === 0;

			// Check for continents if available
			if ( ! $match && isset( $user_continent ) && strpos( $value, 'CO_' ) === 0 ) :
				$match = stripos( $user_continent, str_replace( 'CO_', '', $value ) ) === 0;
			endif;
		elseif ( '!=' == $operator ) :
			$match = stripos( $user_country, $value ) === false;

			// Check for continents if available
			if ( $match && isset( $user_continent ) && strpos( $value, 'CO_' ) === 0 ) :
				$match = stripos( $user_continent, str_replace( 'CO_', '', $value ) ) === false;
			endif;
		endif;

		return $match;

	}


	/**
	 * User role.
	 *
	 * Match the condition value against the users role.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @param  array  $package  List of shipping package details.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function was_match_condition_role( $match, $operator, $value, $package ) {

		global $current_user;

		if ( '==' == $operator ) :
			$match = ( array_key_exists( $value, $current_user->caps ) );
		elseif ( '!=' == $operator ) :
			$match = ( ! array_key_exists( $value, $current_user->caps ) );
		endif;

		return $match;

	}


/******************************************************
 * Product conditions
 *****************************************************/


	/**
	 * Width.
	 *
	 * Match the condition value against the widest product in the cart.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @param  array  $package  List of shipping package details.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function was_match_condition_width( $match, $operator, $value, $package ) {

		if ( ! isset( WC()->cart ) ) :
			return $match;
		endif;

		$product_widths = array();
		foreach ( WC()->cart->get_cart() as $item ) :
			/** @var $product WC_Product */
			$product = $item['data'];
			$product_widths[] = $product->get_width();
		endforeach;

		$max_width = max( $product_widths );

		if ( '==' == $operator ) :
			$match = ( $max_width == $value );
		elseif ( '!=' == $operator ) :
			$match = ( $max_width != $value );
		elseif ( '>=' == $operator ) :
			$match = ( $max_width >= $value );
		elseif ( '<=' == $operator ) :
			$match = ( $max_width <= $value );
		endif;

		return $match;

	}


	/**
	 * Height.
	 *
	 * Match the condition value against the highest product in the cart.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @param  array  $package  List of shipping package details.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function was_match_condition_height( $match, $operator, $value, $package ) {

		if ( ! isset( WC()->cart ) ) :
			return $match;
		endif;

		$product_heights = array();
		foreach ( WC()->cart->get_cart() as $item ) :
			/** @var $product WC_Product */
			$product = $item['data'];
			$product_heights[] = $product->get_height();
		endforeach;

		$max_height = max( $product_heights );

		if ( '==' == $operator ) :
			$match = ( $max_height == $value );
		elseif ( '!=' == $operator ) :
			$match = ( $max_height != $value );
		elseif ( '>=' == $operator ) :
			$match = ( $max_height >= $value );
		elseif ( '<=' == $operator ) :
			$match = ( $max_height <= $value );
		endif;

		return $match;

	}


	/**
	 * Length.
	 *
	 * Match the condition value against the lenghtiest product in the cart.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @param  array  $package  List of shipping package details.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function was_match_condition_length( $match, $operator, $value, $package ) {

		if ( ! isset( WC()->cart ) ) :
			return $match;
		endif;

		$product_lengths = array();
		foreach ( WC()->cart->get_cart() as $item ) :
			/** @var $product WC_Product */
			$product = $item['data'];
			$product_lengths[] = $product->get_length();
		endforeach;

		$max_length = max( $product_lengths );

		if ( '==' == $operator ) :
			$match = ( $max_length == $value );
		elseif ( '!=' == $operator ) :
			$match = ( $max_length != $value );
		elseif ( '>=' == $operator ) :
			$match = ( $max_length >= $value );
		elseif ( '<=' == $operator ) :
			$match = ( $max_length <= $value );
		endif;

		return $match;

	}


	/**
	 * Product stock.
	 *
	 * Match the condition value against all cart products stock.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @param  array  $package  List of shipping package details.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function was_match_condition_stock( $match, $operator, $value, $package ) {

		$product_stocks = array();
		foreach ( WC()->cart->get_cart() as $item ) :
			/** @var $product WC_Product */
			$product = $item['data'];
			$product_stocks[] = $product->get_stock_quantity();
		endforeach;

		// Get lowest value
		$min_stock = min( $product_stocks );

		if ( '==' == $operator ) :
			$match = ( $min_stock == $value );
		elseif ( '!=' == $operator ) :
			$match = ( $min_stock != $value );
		elseif ( '>=' == $operator ) :
			$match = ( $min_stock >= $value );
		elseif ( '<=' == $operator ) :
			$match = ( $min_stock <= $value );
		endif;

		return $match;

	}


	/**
	 * Stock status.
	 *
	 * Match the condition value against all cart products stock statuses.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @param  array  $package  List of shipping package details.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function was_match_condition_stock_status( $match, $operator, $value, $package ) {

		if ( '==' == $operator ) :

			$match = true;
			foreach ( $package['contents'] as $item ) :

				/** @var $product WC_Product */
				$product = $item['data'];

				if ( method_exists( $product, 'get_stock_status' ) ) { // WC 2.7 compatibility
					$stock_status = $product->get_stock_status();
				} else {
					$id = $product->variation_has_stock ? $product->variation_id : $item['product_id'];
					$stock_status = ( get_post_meta( $id, '_stock_status', true ) );
				}

				if ( $stock_status != $value ) :
					$match = false;
				endif;

			endforeach;

		elseif ( '!=' == $operator ) :

			$match = true;
			foreach ( $package['contents'] as $item ) :

				/** @var $product WC_Product */
				$product = $item['data'];

				if ( method_exists( $product, 'get_stock_status' ) ) { // WC 2.7 compatibility
					$stock_status = $product->get_stock_status();
				} else {
					$id = $product->variation_has_stock ? $product->variation_id : $item['product_id'];
					$stock_status = ( get_post_meta( $id, '_stock_status', true ) );
				}

				if ( $stock_status != $value ) :
					$match = false;
				endif;

			endforeach;

		endif;

		return $match;

	}


	/**
	 * Category.
	 *
	 * Match the condition value against all the cart products category.
	 * With this condition, all the products in the cart must have the given class.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $match    Current match value.
	 * @param  string $operator Operator selected by the user in the condition row.
	 * @param  mixed  $value    Value given by the user in the condition row.
	 * @param  array  $package  List of shipping package details.
	 * @return BOOL             Matching result, TRUE if results match, otherwise FALSE.
	 */
	public function was_match_condition_category( $match, $operator, $value, $package ) {

		if ( ! isset( WC()->cart ) ) :
			return $match;
		endif;

		$match = true;

		if ( '==' == $operator ) :

			foreach ( WC()->cart->get_cart() as $product ) :

				if ( ! has_term( $value, 'product_cat', $product['product_id'] ) ) :
					$match = false;
				endif;

			endforeach;

		elseif ( '!=' == $operator ) :

			foreach ( WC()->cart->get_cart() as $product ) :

				if ( has_term( $value, 'product_cat', $product['product_id'] ) ) :
					$match = false;
				endif;

			endforeach;

		endif;

		return $match;

	}


}
