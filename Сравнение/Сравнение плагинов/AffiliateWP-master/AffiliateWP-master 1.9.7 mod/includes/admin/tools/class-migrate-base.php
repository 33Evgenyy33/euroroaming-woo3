<?php

class Affiliate_WP_Migrate_Base {

	public function __construct() { }

	public function process( $step = 1, $part = '' ) {


	}

	public function step_forward() {

		$step = isset( $_GET['step'] ) ? absint( $_GET['step'] ) : 1;
		$part = isset( $_GET['part'] ) ? $_GET['part'] : 'affiliates';

		$step++;

		$redirect = add_query_arg(
			array(
				'page' => 'affiliate-wp-migrate',
				'type' => 'affiliates-pro',
				'part' => $part,
				'step' => $step
			),
			admin_url( 'index.php' )
		);

		wp_safe_redirect( $redirect );

		exit;

	}

	public function finish() {

		$redirect = add_query_arg(
			array(
				'page' => 'affiliate-wp'
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect );

		exit;
	}

	/**
	 * Retrieves stored data by key.
	 *
	 * Given a key, get the information from the database directly.
	 *
	 * @access protected
	 * @since  1.9.5
	 *
	 * @param string $key The stored option key.
	 * @return mixed|false The stored data, otherwise false.
	 */
	protected function get_stored_data( $key ) {
		global $wpdb;
		$value = $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = '%s'", $key ) );

		return empty( $value ) ? false : maybe_unserialize( $value );
	}

	/**
	 * Retrieves the total count of migrated items.
	 *
	 * @access public
	 * @since  1.9.5
	 * @static
	 *
	 * @param string $key The stored option key.
	 * @return mixed|false The stored data, otherwise false.
	 */
	public static function get_items_total( $key ) {
		$self = new self();
		return $self->get_stored_data( $key );
	}

	/**
	 * Store some data based on key and value.
	 *
	 * @access protected
	 * @since  1.9.5
	 *
	 * @param string $key     The option_name.
	 * @param mixed  $value   The value to store.
	 * @param array  $formats Optional. Array of formats to pass for key, value, and autoload.
	 *                        Default empty (all strings).
	 */
	protected function store_data( $key, $value, $formats = array() ) {
		global $wpdb;

		$value = maybe_serialize( $value );

		$data = array(
			'option_name'  => $key,
			'option_value' => $value,
			'autoload'     => 'no',
		);

		if ( empty( $formats ) ) {
			$formats = array(
				'%s', '%s', '%s',
			);
		}

		$wpdb->replace( $wpdb->options, $data, $formats );
	}

	/**
	 * Deletes a piece of stored data by key.
	 *
	 * @access protected
	 * @since  1.9.5
	 *
	 * @param string $key The stored option name to delete.
	 */
	protected function delete_data( $key ) {
		global $wpdb;

		$wpdb->delete( $wpdb->options, array( 'option_name' => $key ) );
	}

	/**
	 * Deletes the total count of migrated items.
	 *
	 * @access public
	 * @since  1.9.5
	 * @static
	 *
	 * @param string $key The stored option name to delete.
	 */
	public static function clear_items_total( $key ) {
		$self = new self();
		$self->delete_data( $key );
	}
}
