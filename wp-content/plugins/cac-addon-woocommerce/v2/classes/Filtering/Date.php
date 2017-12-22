<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @property ACA_Types_Column $column
 */
class ACA_WC_Filtering_Date extends ACP_Filtering_Model_Meta {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_data_type( 'date' );
	}

	public function register_settings() {
		$this->column->add_setting(
			new ACP_Filtering_Settings_Date( $this->column )
		);
	}

	/**
	 * @param array        $vars
	 * @param array|string $value
	 *
	 * @return mixed
	 */
	public function get_filtering_vars( $vars ) {

		$vars = $this->get_filtering_vars_date( $vars, array(
			'filter_format' => $this->get_filter_format(),
			'date_format'   => 'Y-m-d',
		) );

		return $vars;
	}

	public function get_filtering_data() {
		$format = $this->get_filter_format();

		$display_format = 'Y-m-d';

		// Use display date format for dropdown options when it's daily
		if ( 'daily' === $format && $display_format ) {
			$format = $display_format;
		}

		// Future / Past
		$options = $this->get_date_options_relative( $format );

		// Specific Date
		if ( ! $options ) {
			$options = $this->get_date_options( $this->get_meta_values(), $format, 'Y-m-d' );
		}

		return array(
			'empty_option' => true,
			'order'        => false,
			'options'      => $options,
		);
	}

	private function get_filter_format() {
		$format = $this->column->get_setting( 'filter' )->get_value( 'filter_format' );

		if ( ! $format ) {
			$format = 'daily';
		}

		return $format;
	}

}
