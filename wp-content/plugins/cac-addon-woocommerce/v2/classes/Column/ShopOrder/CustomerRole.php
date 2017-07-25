<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.0
 */
class ACA_WC_Column_ShopOrder_CustomerRole extends AC_Column_Meta
	implements ACP_Column_FilteringInterface {

	public function __construct() {
		$this->set_label( 'Customer Role' );
		$this->set_type( 'column-wc-order_customer_role' );
		$this->set_group( 'woocommerce' );
	}

	// Meta

	public function get_meta_key() {
		return '_customer_user';
	}

	// Display

	public function get_value( $id ) {
		$customer_id = $this->get_raw_value( $id );

		return implode( ', ', $this->get_role_names_by_user( $customer_id ) );
	}

	// Pro

	public function filtering() {
		return new ACA_WC_Filtering_ShopOrder_CustomerRole( $this );
	}

	/**
	 * @param WP_User|int $user
	 *
	 * @return array|bool
	 */
	public function get_role_names_by_user( $user ) {
		if ( ! $user instanceof WP_User ) {
			$user = get_userdata( $user );
		}

		if ( ! $user ) {
			return array();
		}

		$role_names = array();

		// Translations
		$roles = array();
		foreach ( wp_roles()->roles as $k => $role ) {
			$roles[ $k ] = translate_user_role( $role['name'] );
		}

		foreach ( (array) $user->roles as $role ) {
			if ( isset( $roles[ $role ] ) ) {
				$role_names[ $role ] = $roles[ $role ];
			}
		}

		return $role_names;
	}

}
