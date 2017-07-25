<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class WAS_Ajax.
 *
 * Initialize the AJAX class.
 *
 * @class		WAS_Ajax
 * @author		Jeroen Sormani
 * @package		WooCommerce Advanced Shipping
 * @version		1.0.0
 */
class WAS_Ajax {


	/**
	 * Constructor.
	 *
	 * Add ajax actions in order to work.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Add elements
		add_action( 'wp_ajax_was_add_condition', array( $this, 'was_add_condition' ) );
		add_action( 'wp_ajax_was_add_condition_group', array( $this, 'was_add_condition_group' ) );

		// Update elements
		add_action( 'wp_ajax_was_update_condition_value', array( $this, 'was_update_condition_value' ) );
		add_action( 'wp_ajax_was_update_condition_description', array( $this, 'was_update_condition_description' ) );

		// Save shipping method ordering
		add_action( 'wp_ajax_was_save_shipping_rates_table', array( $this, 'save_shipping_rates_table' ) );

	}


	/**
	 * Add condition.
	 *
	 * Create a new WAS_Condition class and render.
	 *
	 * @since 1.0.0
	 */
	public function was_add_condition() {

		check_ajax_referer( 'was-ajax-nonce', 'nonce' );

		new WAS_Condition( null, $_POST['group'] );
		die();

	}


	/**
	 * Condition group.
	 *
	 * Render new condition group.
	 *
	 * @since 1.0.0
	 */
	public function was_add_condition_group() {

		check_ajax_referer( 'was-ajax-nonce', 'nonce' );

		?><div class='condition-group condition-group-<?php echo $_POST['group']; ?>' data-group='<?php echo $_POST['group']; ?>'>

			<p class='or-match'><?php _e( 'Or match all of the following rules to allow this shipping method:', 'woocommerce-advanced-shipping' );?></p><?php

			new was_Condition( null, $_POST['group'] );

		?></div>

		<p class='or-text'><strong><?php _e( 'Or', 'woocommerce-advanced-shipping' ); ?></strong></p><?php

		die();

	}


	/**
	 * Update values.
	 *
	 * Retreive and render the new condition values according to the condition key.
	 *
	 * @since 1.0.0
	 */
	public function was_update_condition_value() {

		check_ajax_referer( 'was-ajax-nonce', 'nonce' );

		was_condition_values( $_POST['id'], $_POST['group'], $_POST['condition'] );
		die();

	}


	/**
	 * Update description.
	 *
	 * Render the corresponding description for the condition key.
	 *
	 * @since 1.0.0
	 */
	public function was_update_condition_description() {

		check_ajax_referer( 'was-ajax-nonce', 'nonce' );

		was_condition_description( $_POST['condition'] );
		die();

	}


	/**
	 * Save order.
	 *
	 * Save the shipping method order.
	 *
	 * @since 1.0.4
	 */
	public function save_shipping_rates_table() {

		global $wpdb;

		check_ajax_referer( 'was-ajax-nonce', 'nonce' );

		$args = wp_parse_args( $_POST['form'] );

		// Save order
		$menu_order = 0;
		foreach ( $args['sort'] as $sort ) :

			$wpdb->update(
				$wpdb->posts,
				array( 'menu_order' => $menu_order ),
				array( 'ID' => $sort ),
				array( '%d' ),
				array( '%d' )
			);

			$menu_order++;

		endforeach;


		// Save priorities
		foreach ( $args['method_priority'] as $rate_id => $priority ) :
			update_post_meta( absint( $rate_id ), '_priority', absint( $priority ) );
		endforeach;

		die;

	}


}
