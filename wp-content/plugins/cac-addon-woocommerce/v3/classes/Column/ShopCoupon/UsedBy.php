<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.0
 */
class ACA_WC_Column_ShopCoupon_UsedBy extends AC_Column
	implements ACP_Export_Column {

	public function __construct() {
		$this->set_type( 'column-wc-coupon_user' );
		$this->set_label( __( 'Used By', 'codepress-admin-columns' ) );
		$this->set_group( 'woocommerce' );
	}

	/**
	 * @param int $id
	 *
	 * @return array
	 */
	public function get_raw_value( $id ) {
		$coupon = new WC_Coupon( $id );
		$users = $coupon->get_used_by();

		if ( ! $users ) {
			return array();
		}

		return $users;
	}

	/**
	 * @param int $id
	 *
	 * @return string
	 */
	public function get_value( $id ) {
		$users = $this->get_raw_value( $id );

		if ( empty( $users ) ) {
			return $this->get_empty_char();
		}

		$values = array();

		foreach ( $users as $user ) {
			if ( is_numeric( $user ) ) {
				if ( $user = get_userdata( $user ) ) {
					$values[] = ac_helper()->html->link( get_edit_user_link( $user->ID ), ac_helper()->user->get_display_name( $user ) );
				}
			} else if ( is_email( $user ) ) {
				$values[] = ac_helper()->html->link( 'mailto:' . $user, $user, array( 'tooltip' => __( 'Not a registered user', 'codepress-admin-columns' ) ) );
			}
		}

		return ac_helper()->html->more( $values, 5 );
	}

	public function export() {
		return new ACP_Export_Model_StrippedValue( $this );
	}

}
