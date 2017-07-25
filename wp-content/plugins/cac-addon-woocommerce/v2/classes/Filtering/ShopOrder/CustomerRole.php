<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_WC_Column_ShopOrder_CustomerRole $column
 */
class ACA_WC_Filtering_ShopOrder_CustomerRole extends ACP_Filtering_Model_Meta {

	public function __construct( ACA_WC_Column_ShopOrder_CustomerRole $column ) {
		parent::__construct( $column );
	}

	public function get_filtering_vars( $vars ) {
		$users = get_users( array(
				'role'   => $this->get_filter_value(),
				'fields' => 'id',
			)
		);

		$vars['meta_query'][] = array(
			'key'     => $this->column->get_meta_key(),
			'value'   => $users,
			'compare' => 'IN',
		);

		return $vars;
	}

	public function get_filtering_data() {
		$user_ids = $this->get_meta_values();

		if ( ! $user_ids ) {
			return false;
		}

		$options = array();
		foreach ( $user_ids as $user_id ) {
			$names = $this->column->get_role_names_by_user( $user_id );

			$options = array_merge( $options, $names );
		}

		return array(
			'options' => $options,
		);
	}

}
