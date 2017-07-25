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
 * The Local Pickup Plus shipping method class.
 *
 * Uses WooCommerce Shipping Method API to add a new shipping method, which in turn extends the WooCommerce Settings API.
 *
 * The core API requires to use the same class for both admin and frontend, hence there are settings and frontend functionality in the same class.
 *
 * This class tries to limit its responsibility to handle shipping method settings in both back end (along with settings UI) and front end, where it also instantiates additional classes where it delegates actual checkout and shipping logic.
 *
 * @since 1.4
 */
class WC_Shipping_Local_Pickup_Plus extends WC_Shipping_Method {


	/**
	 * Initialize the Local Pickup Plus shipping method class.
	 *
	 * @since 1.4
	 */
	public function __construct() {

		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte_2_6() ) {
			parent::__construct();
		}

		$this->id                 = WC_Local_Pickup_Plus::SHIPPING_METHOD_ID;
		$this->method_title       = __( 'Local Pickup Plus', 'woocommerce-shipping-local-pickup-plus' );
		$this->method_description = __( 'Local Pickup Plus is a shipping method which allows customers to pick up their orders at a specified pickup location.', 'woocommerce-shipping-local-pickup-plus' );

		// load and init shipping method settings
		$this->handle_settings();

		if ( $this->is_enabled() ) {
			// set Local Pickup Plus as the default shipping method
			add_filter( 'woocommerce_shipping_chosen_method', array( $this, 'set_default_shipping_method' ), 100, 2 );
		}

		/**
		 * Local Pickup Plus shipping method init.
		 *
		 * @since 1.4
		 *
		 * @param \WC_Shipping_Local_Pickup_Plus $shipping_method instance of this class
		 */
		do_action( 'wc_shipping_local_pickup_plus_init', $this );
	}


	/**
	 * Get the shipping method ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_method_id() {
		return $this->id;
	}


	/**
	 * Get the shipping method name.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_method_title() {
		// looks for a user entered title first, defaults to parent method title which is filtered
		return $this->get_option( 'title', parent::get_method_title() );
	}


	/**
	 * Check whether the shipping method is available at checkout.
	 *
	 * @since 1.4
	 *
	 * @param array $package optional, a package as an array
	 * @return bool
	 */
	public function is_available( $package = array() ) {

		// the shipping method must be enabled and there must be at least one pickup location published
		$is_available = $this->is_enabled() && wc_local_pickup_plus()->get_pickup_locations_instance()->get_pickup_locations_count( array( 'post_status' => 'publish' ) ) > 0;

		return (bool) apply_filters( "woocommerce_shipping_{$this->id}_is_available", $is_available, $package );
	}


	/**
	 * Handle shipping method settings.
	 *
	 * @since 2.0.0
	 */
	private function handle_settings() {

		// load the form fields
		$this->form_fields = $this->get_settings_fields();

		// load the settings
		$this->init_settings();

		// init user settings
		foreach ( $this->settings as $setting_key => $setting ) {
			$this->$setting_key = $setting;
		}

		// save settings in admin when updated
		add_action( "woocommerce_update_options_shipping_{$this->id}", array( $this, 'process_admin_options' ) );
	}


	/**
	 * Get shipping method settings form fields
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	protected function get_settings_fields() {

		$form_fields = array(

			'enabled' => array(
				'title'   => __( 'Enable', 'woocommerce-shipping-local-pickup-plus' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Local Pickup Plus', 'woocommerce-shipping-local-pickup-plus' ),
				'default' => 'no',
			),

			'title' => array(
				'id'          => 'title',
				'title'       => __( 'Title', 'woocommerce-shipping-local-pickup-plus' ),
				'description' => __( 'The shipping method title that customers see during checkout.', 'woocommerce-shipping-local-pickup-plus' ),
				'type'        => 'text',
				'default'     => __( 'Local Pickup', 'woocommerce-shipping-local-pickup-plus' ),
			),

			'google_maps_api_key' => array(
				'id'          => 'google_maps_api_key',
				'title'       => __( 'Google Maps Geocoding API Key', 'woocommerce-shipping-local-pickup-plus' ),
				'desc_tip'    => __( 'Use Google Maps Geocoding API to geocode your pickup locations and enable customers to search pickup locations by distance.', 'woocommerce-shipping-local-pickup-plus' ),
				'placeholder' => __( '(optional)', 'woocommerce-shipping-local-pickup-plus' ),
				'type'        => 'password',
				'default'     => '',
			),

			'enable_logging' => array(
				'id'          => 'enable_logging',
				'title'    => __( 'Enable logging', 'woocommerce-shipping-local-pickup-plus' ),
				'desc_tip' => __( 'Log Google Maps Geocoding API responses and errors.', 'woocommerce-shipping-local-pickup-plus' ),
				'type'     => 'checkbox',
				'default'  => 'no',
			),

			'checkout_display_start' => array(
				'name' => __( 'Checkout Display', 'woocommerce-shipping-local-pickup-plus' ),
				'desc' => __( 'Determine how pickup locations are shown to the customer at checkout.', 'woocommerce-shipping-local-pickup-plus' ),
				'type' => 'section_start',
			),

			'hide_shipping_address' => array(
				'title'   => __( 'Hide Shipping Address', 'woocommerce-shipping-local-pickup-plus' ),
				'type'    => 'checkbox',
				'label'   => __( 'Hide the shipping address when Local Pickup Plus is selected at checkout.', 'woocommerce-shipping-local-pickup-plus' ),
				'default' => 'no',
			),

			'pickup_locations_sort_order' => array(
				'title'       => __( 'Location Sort Order', 'woocommerce-shipping-local-pickup-plus' ),
				'desc_tip'    => __( 'Choose how the pickup location will be listed to the customer at checkout. Default is the default sort order determined by WordPress.', 'woocommerce-shipping-local-pickup-plus' ),
				'description' => __( 'Sorting by distance is only available with a Google Maps Geocoding API key to enable geocoding.', 'woocommerce-shipping-local-pickup-plus' ),
				'type'        => 'select',
				'options'     => array(
					'default'               => __( 'Default', 'woocommerce-shipping-local-pickup-plus' ),
					'distance_customer'     => __( 'Distance from customer', 'woocommerce-shipping-local-pickup-plus' ),
					'location_alphabetical' => __( 'Alphabetical by location name', 'woocommerce-shipping-local-pickup-plus' ),
					'location_date_added'   => __( 'Most recently added location', 'woocommerce-shipping-local-pickup-plus' ),
				),
				'default'     => 'default',
			),

			'checkout_display_end' => array(
				'type' => 'section_end',
			),

			'pickup_appointments_start' => array(
				'name' => __( 'Pickup Appointments', 'woocommerce-shipping-local-pickup-plus' ),
				'desc' => __( 'Pickup scheduled appointments allow the customer to schedule an appointment for pickup at a selected pickup location on checkout.', 'woocommerce-shipping-local-pickup-plus' ),
				'type' => 'section_start',
			),

			'pickup_appointments_mode' => array(
				'title'       => __( 'Pickup Appointments Mode', 'woocommerce-shipping-local-pickup-plus' ),
				'type'        => 'select',
				'options'     => array(
					'disabled' => __( 'Do not offer appointments', 'woocommerce-shipping-local-pickup-plus' ),
					'enabled'  => __( 'Allow scheduled appointments', 'woocommerce-shipping-local-pickup-plus' ),
					'required' => __( 'Require scheduled appointments', 'woocommerce-shipping-local-pickup-plus' ),
				),
				'default'     => 'disabled',
			),

			'default_business_hours' => array(
				'title'       => __( 'Default Business Hours', 'woocommerce-shipping-local-pickup-plus' ),
				'description' => __( 'If using scheduled appointments and no business hours are defined, customers may not be able to select a location. The default schedule can be overridden by individual pickup locations.', 'woocommerce-shipping-local-pickup-plus' ),
				'type'        => 'business_hours',
				// default business hours: Monday to Friday from 9:00 to 17:00
				'default'     => array_fill( 1, 5, array(
					9 * HOUR_IN_SECONDS => 17 * HOUR_IN_SECONDS
				) ),
			),

			'default_public_holidays' => array(
				'title'       => __( 'Common Public Holidays', 'woocommerce-shipping-local-pickup-plus' ),
				'description' => __( 'Manually exclude specific days of the calendar to have a pickup appointment scheduled. You can override default dates from each pickup location.', 'woocommerce-shipping-local-pickup-plus' ),
				'type'        => 'public_holidays',
				'default'     => '',
			),

			'default_lead_time' => array(
				'title' => __( 'Default Lead Time', 'woocommerce-shipping-local-pickup-plus' ),
				'description' => __( 'Set a default pickup lead time for scheduling a local pickup. The default lead time can be overridden by individual pickup locations.', 'woocommerce-shipping-local-pickup-plus' ),
				'type'        => 'lead_time',
				'default'     => '2 days',
			),

			'default_deadline' => array(
				'title' => __( 'Default Deadline', 'woocommerce-shipping-local-pickup-plus' ),
				'description' => __( 'Set a default pickup deadline for scheduling a local pickup. A value of zero sets no deadline. The default deadline can be overridden by individual pickup locations.', 'woocommerce-shipping-local-pickup-plus' ),
				'type'        => 'deadline',
				'default'     => '1 months',
			),

			'pickup_appointments_end' => array(
				'type' => 'section_end',
			),

			'pickup_costs_discounts_start' => array(
				'name' => __( 'Price &amp; Tax', 'woocommerce-shipping-local-pickup-plus' ),
				'desc' => __( 'Set a default cost or discount when a customer chooses to pickup up an order and how taxation should be handled.', 'woocommerce-shipping-local-pickup-plus' ),
				'type' => 'section_start',
			),

			'default_price_adjustment' => array(
				'title'       => __( 'Default Price Adjustment', 'woocommerce-shipping-local-pickup-plus' ),
				'desc_tip'    => __( 'A cost or a discount applied when choosing Local Pickup Plus as the shipping method. You can set a fixed or a percentage amount. When using percentage, the value will be calculated based on cart contents value.', 'woocommerce-shipping-local-pickup-plus' ),
				'description' => __( 'Set to zero for no default adjustment. The default amount can be overridden by setting an adjustment in individual pickup locations.', 'woocommerce-shipping-local-pickup-plus' ),
				'type'        => 'price_adjustment',
				'default'     => '',
			),

			'apply_pickup_location_tax' => array(
				'title'   => __( 'Pickup Location Tax', 'woocommerce-shipping-local-pickup-plus' ),
				'type'    => 'checkbox',
				'label'   => __( 'When this shipping method is chosen, apply the tax rate based on the pickup location than for the customer\'s given address.', 'woocommerce-shipping-local-pickup-plus' ),
				'default' => 'no',
			),

			'pickup_costs_discounts_end' => array(
				'type' => 'section_end',
			),

		);

		/**
		 * Filter Local Pickup Plus shipping method settings fields.
		 *
		 * @since 1.14.0
		 *
		 * @param array $form_fields settings fields
		 */
		return (array) apply_filters( 'wc_local_pickup_plus_settings', $form_fields );
	}


	/**
	 * Generate HTML for a custom input field.
	 *
	 * @since 2.0.0
	 *
	 * @param string $field the type of field to output
	 * @param string $field_key the field key to identify the field ID and name values
	 * @param array $data input field data to build the markup
	 * @return string HTML
	 */
	private function get_custom_settings_field( $field, $field_key, array $data ) {

		ob_start();

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); if ( ! empty( $data['desc_tip'] ) ) { $data['desc_tip'] = false; } ?>
			</th>
			<td class="forminp">
				<fieldset
					class="<?php echo esc_attr( $data['class'] ); ?>"
					style="<?php echo esc_attr( $data['css'] ); ?>">
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<?php

					$field_object = null;
					$default_data = get_option( $field_key, '' );
					$default_data = empty( $default_data ) ? $data['default'] : $default_data;

					switch ( $field ) {
						case 'deadline' :
						case 'lead_time' :
							$field_object = new WC_Local_Pickup_Plus_Schedule_Adjustment( str_replace( '_', '-', $field ), $default_data );
						break;
						case 'business_hours' :
							$field_object = new WC_Local_Pickup_Plus_Business_Hours( (array) $default_data );
						break;
						case 'price_adjustment' :
							$field_object = new WC_Local_Pickup_Plus_Price_Adjustment( $default_data );
						break;
						case 'public_holidays' :
							$field_object = new WC_Local_Pickup_Plus_Public_Holidays( (array) $default_data );
						break;
					}

					if ( null !== $field_object ) {
						echo $field_object->get_field_html( $data );
						echo $this->get_description_html( $data );
					}

					?>
				</fieldset>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}


	/**
	 * Parse custom fields default arguments
	 *
	 * @since 2.0.0
	 *
	 * @param array $args field args
	 * @param array $defaults field default values
	 * @return array
	 */
	private function parse_custom_fields_default_args( array $args, array $defaults ) {
		return wp_parse_args( $args, wp_parse_args( $defaults, array(
			'title'       => '',
			'disabled'    => false,
			'class'       => '',
			'css'         => '',
			'placeholder' => '',
			'desc_tip'    => false,
			'description' => '',
		) ) );
	}


	/**
	 * Generate a price adjustment field HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key field key
	 * @param array $data field data
	 * @return string HTML
	 */
	protected function generate_price_adjustment_html( $key, array $data ) {

		$field_key = $this->get_field_key( $key );
		$data      = $this->parse_custom_fields_default_args( $data, array(
			'name'    => $field_key,
			'default' => get_option( $field_key, 0 ),
		) );

		return $this->get_custom_settings_field( 'price_adjustment', $field_key, $data );
	}


	/**
	 * Get a business hours field HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key field key
	 * @param array $data field data
	 * @return string HTML
	 */
	protected function generate_business_hours_html( $key, array $data ) {

		$field_key = $this->get_field_key( $key );
		$data      = $this->parse_custom_fields_default_args( $data, array(
			'name'    => $field_key,
			'default' => get_option( $field_key, array() ),
		) );

		return $this->get_custom_settings_field( 'business_hours', $field_key, $data );
	}


	/**
	 * Get a closure days calendar field HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key field key
	 * @param array $data field data
	 * @return string HTML
	 */
	protected function generate_public_holidays_html( $key, array $data ) {

		$field_key = $this->get_field_key( $key );
		$data      = $this->parse_custom_fields_default_args( $data, array(
			'name'    => $field_key,
			'default' => get_option( $field_key, array() ),
		) );

		return $this->get_custom_settings_field( 'public_holidays', $field_key, $data );
	}


	/**
	 * Get a lead time field HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key field key
	 * @param array $data field data
	 * @return string HTML
	 */
	protected function generate_lead_time_html( $key, array $data ) {

		$field_key = $this->get_field_key( $key );
		$data      = $this->parse_custom_fields_default_args( $data, array(
			'name'    => $field_key,
			'default' => get_option( $field_key, '2 days' ),
		) );

		return $this->get_custom_settings_field( 'lead_time', $field_key, $data );
	}


	/**
	 * Get a deadline field HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key field key
	 * @param array $data field data
	 * @return string HTML
	 */
	protected function generate_deadline_html( $key, array $data ) {

		$field_key = $this->get_field_key( $key );
		$data      = $this->parse_custom_fields_default_args( $data, array(
			'name'    => $field_key,
			'default' => $this->get_default_pickup_deadline(),
		) );

		return $this->get_custom_settings_field( 'deadline', $field_key, $data );
	}


	/**
	 * Generate fields section start HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key field key
	 * @param array $data field data
	 * @return string HTML
	 */
	protected function generate_section_start_html( $key, array $data ) {
		return $this->get_fields_section_html( 'start', $key, $data );
	}


	/**
	 * Generate fields section end HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param string $key field key
	 * @param array $data field data
	 * @return string HTML
	 */
	protected function generate_section_end_html( $key, array $data ) {
		return $this->get_fields_section_html( 'end', $key, $data );
	}


	/**
	 * Get fields section HTML.
	 *
	 * @since 2.0.0
	 *
	 * @param string $section which section to generate ('start' or 'end')
	 * @param string $key field key
	 * @param array $data field data
	 * @return string HTML
	 */
	private function get_fields_section_html( $section, $key, array $data ) {

		if ( 'end' === $section || ! $key ) {
			return '';
		}

		ob_start();

		?>
		<tr valign="top">
			<th scope="row" class="titledesc" colspan="2">
				<?php if ( isset( $data['name'] ) ) : ?>
					<h2><?php echo esc_html( $data['name'] ); ?></h2>
				<?php endif; ?>
				<?php if ( isset( $data['desc'] ) ) : ?>
					<p style="font-weight: normal;"><?php echo wp_kses_post( $data['desc'] ); ?></p>
				<?php endif; ?>
			</th>
		</tr>
		<?php

		return ob_get_clean();
	}


	/**
	 * Process admin options for the shipping method settings.
	 *
	 * @internal
	 *
	 * @since 1.4
	 *
	 * @return bool whether settings were saved
	 */
	public function process_admin_options() {

		// save the default price adjustment setting
		if ( isset( $_POST['woocommerce_local_pickup_plus_default_price_adjustment'], $_POST['woocommerce_local_pickup_plus_default_price_adjustment_amount'], $_POST['woocommerce_local_pickup_plus_default_price_adjustment_type'] ) ) {

			$adjustment = $_POST['woocommerce_local_pickup_plus_default_price_adjustment'];
			$amount     = $_POST['woocommerce_local_pickup_plus_default_price_adjustment_amount'];
			$type       = $_POST['woocommerce_local_pickup_plus_default_price_adjustment_type'];

			// validate and sanitize a valid price adjustment string
			$default_price_adjustment = new WC_Local_Pickup_Plus_Price_Adjustment();
			$default_price_adjustment->set_value( $adjustment, (float) $amount, $type );

			update_option( 'woocommerce_local_pickup_plus_default_price_adjustment', $default_price_adjustment->get_value() );
		}

		// get how we should handle appointment scheduling options
		$appointments_mode_disabled = true;

		if ( isset( $_POST['woocommerce_local_pickup_plus_pickup_appointments_mode'] ) && 'disabled' !== $_POST['woocommerce_local_pickup_plus_pickup_appointments_mode'] ) {
			$appointments_mode_disabled = false;
		}

		if ( ! $appointments_mode_disabled ) {

			// save the default business hours to schedule a pickup
			$business_hours = new WC_Local_Pickup_Plus_Business_Hours();

			update_option( 'woocommerce_local_pickup_plus_default_business_hours', $business_hours->get_field_value( 'woocommerce_local_pickup_plus_default_business_hours', $_POST ) );

			// save the default public holidays for pickup appointment scheduling
			if ( ! empty( $_POST['woocommerce_local_pickup_plus_default_public_holidays'] ) ) {

				$public_holidays = (array) $_POST['woocommerce_local_pickup_plus_default_public_holidays'];
				$calendar        = new WC_Local_Pickup_Plus_Public_Holidays( $public_holidays );

				update_option( 'woocommerce_local_pickup_plus_default_public_holidays', $calendar->get_calendar() );

				// prevents a PHP error when the Shipping Settings Page tries to save an array
				unset( $_POST['woocommerce_local_pickup_plus_default_public_holidays'] );
			}

			// save the default lead time affecting pickup scheduling
			if ( isset( $_POST['woocommerce_local_pickup_plus_default_lead_time_amount'], $_POST['woocommerce_local_pickup_plus_default_lead_time_interval'] ) ) {

				$amount   = max( 0, (int) $_POST['woocommerce_local_pickup_plus_default_lead_time_amount'] );
				$interval = $_POST['woocommerce_local_pickup_plus_default_lead_time_interval'];

				$default_lead_time = new WC_Local_Pickup_Plus_Schedule_Adjustment( 'lead-time' );
				$default_lead_time->set_value( $amount, $interval );

				update_option( 'woocommerce_local_pickup_plus_default_lead_time', $default_lead_time->get_value() );
			}

			// save the default deadline affecting pickup scheduling
			if ( isset( $_POST['woocommerce_local_pickup_plus_default_deadline_amount'], $_POST['woocommerce_local_pickup_plus_default_deadline_interval'] ) ) {

				$amount   = max( 0, (int) $_POST['woocommerce_local_pickup_plus_default_deadline_amount'] );
				$interval = $_POST['woocommerce_local_pickup_plus_default_deadline_interval'];

				$default_lead_time = new WC_Local_Pickup_Plus_Schedule_Adjustment( 'deadline' );
				$default_lead_time->set_value( $amount, $interval );

				update_option( 'woocommerce_local_pickup_plus_default_deadline', $default_lead_time->get_value() );
			}

		} else {

			// if we have disabled pickup appointments, destroy related data:
			delete_option( 'woocommerce_local_pickup_plus_default_business_hours'  );
			delete_option( 'woocommerce_local_pickup_plus_default_public_holidays' );
			delete_option( 'woocommerce_local_pickup_plus_default_lead_time' );
			delete_option( 'woocommerce_local_pickup_plus_default_deadline' );
		}

		// process other standard options
		return parent::process_admin_options();
	}


	/**
	 * Get the default pickup locations sort order.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function pickup_locations_sort_order() {

		$default_option  = 'default';
		$sort_order      = $this->get_option( 'pickup_locations_sort_order',  $default_option );
		$sorting_options = array(
			'default',
			'distance_customer',
			'location_alphabetical',
			'location_date_added',
		);

		return in_array( $sort_order, $sorting_options, true ) ? $sort_order : $default_option;
	}


	/**
	 * Whether applying the tax rate for the pickup location rather than the customer's given address.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public function apply_pickup_location_tax() {

		// we don't use $this->get_option() as this is a composite option handled differently
		return true === (bool) get_option( 'woocommerce_local_pickup_plus_apply_pickup_location_tax', false );
	}


	/**
	 * Returns the pickup appointments mode from user's settings.
	 *
	 * @since 2.0.0
	 *
	 * @return string Either 'disabled', 'enabled' or 'required'
	 */
	public function pickup_appointments_mode() {

		$default = 'disabled';
		$option  = $this->get_option( 'pickup_appointments_mode', $default );

		return in_array( $option, array( 'disabled', 'enabled', 'required' ), true ) ? $option : $default;
	}


	/**
	 * Get the Google Maps API Key, if set.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_google_maps_api_key() {

		$default = '';
		$api_key = $this->get_option( 'google_maps_api_key', $default );

		return is_string( $api_key ) ? $api_key : $default;
	}


	/**
	 * Get the global pickup lead time.
	 *
	 * This might be overridden by individual pickup locations.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_default_pickup_lead_time() {

		$default = '2 days';
		// we don't use $this->get_option() as this is a composite option handled differently
		$value   = get_option( 'woocommerce_local_pickup_plus_default_lead_time', $default );

		return is_string( $value ) ? $value : $default;
	}


	/**
	 * Get the global pickup deadline.
	 *
	 * This might be overridden by individual pickup locations.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_default_pickup_deadline() {

		$default = '1 months';
		// we don't use $this->get_option() as this is a composite option handled differently
		$value   = get_option( 'woocommerce_local_pickup_plus_default_deadline', $default );

		return is_string( $value ) ? $value : $default;
	}


	/**
	 * Get the default pickup location business hours.
	 *
	 * This might be overridden by individual pickup locations.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_default_business_hours() {

		$default = array_fill( 1, 5, array( 9 * HOUR_IN_SECONDS => 17 * HOUR_IN_SECONDS ) );

		// we don't use $this->get_option() as this is a composite option handled differently
		return (array) get_option( 'woocommerce_local_pickup_plus_default_business_hours', $default );
	}


	/**
	 * Get the global public holidays.
	 *
	 * This might be overridden by individual pickup locations.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_default_public_holidays() {

		// we don't use $this->get_option() as this is a composite option handled differently
		return (array) get_option( 'woocommerce_local_pickup_plus_default_public_holidays', array() );
	}


	/**
	 * Get the default price adjustment when completing a purchase with pickup.
	 *
	 * This might be overridden by individual pickup locations.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_default_price_adjustment() {

		$default = '';
		// we don't use $this->get_option() as this is a composite option handled differently
		$value   = get_option( 'woocommerce_local_pickup_plus_default_price_adjustment', $default );

		return is_string( $value ) || is_numeric( $value ) ? $value : $default;
	}


	/**
	 * Set the default shipping method for a package.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $shipping_method the default shipping method, normally with an instance suffix
	 * @param array $shipping_rates shipping rates available for a package
	 * @return string default shipping method for a package, when not user set
	 */
	public function set_default_shipping_method( $shipping_method, $shipping_rates ) {
		return array_key_exists( $this->id, $shipping_rates ) ? $this->id : $shipping_method;
	}


	/**
	 * Calculate shipping costs for local pickup of packages at chosen location.
	 *
	 * Extends parent method:
	 * @see \WC_Shipping_Method::calculate_shipping()
	 * @uses \WC_Shipping_Method::add_rate()
	 *
	 * @since 1.4
	 *
	 * @param array $package package data as associative array
	 */
	public function calculate_shipping( $package = array() ) {
		global $wp_query;

		$cost  = 0;
		$label = $this->get_method_title();

		if ( is_checkout() || ( $wp_query && defined( 'WC_DOING_AJAX' ) && 'update_order_review' === $wp_query->get( 'wc-ajax' ) ) ) {

			$pickup_location  = isset( $package['pickup_location_id'] ) && is_numeric( $package['pickup_location_id'] ) ? wc_local_pickup_plus_get_pickup_location( $package['pickup_location_id'] ) : null;
			$price_adjustment = $pickup_location ? $pickup_location->get_price_adjustment() : null;

			if ( $price_adjustment && isset( $package['contents_cost'] ) && $package['contents_cost'] > 0 ) {
				$cost = $price_adjustment->get_relative_amount( $package['contents_cost'] );
			}

			$cost     = ! empty( $cost ) ? $cost : 0;
			/* translators: Placeholder: %s - local pickup discount amount */
			$discount = $cost < 0 ? sprintf( __( '%s (discount!)', 'woocommerce-shipping-local-pickup-plus' ), wc_price( $cost ) ) : '';

			// we need to display the discount in the label as WooCommerce does not handle negative values in the 'cost' property of a shipping rate
			if ( ! empty( $discount ) ) {
				if ( ! is_rtl() ) {
					$label = trim( $this->get_method_title() ) . ': ' . $discount;
				} else {
					$label = $discount . ' :' . trim( $this->get_method_title() );
				}
			}
		}

		// register the rate for this package
		$this->add_rate( array(
			'id'       => $this->get_method_id(),      // default value (the method ID)
			'label'    => wp_strip_all_tags( $label ), // this might include a discount notice the customer will understand
			'cost'     => $cost > 0 ? $cost : 0,       // if there's a discount, dot not set a negative fee, later we will register a separate fee item as discount
			'taxes'    => $cost > 0 ? ''    : false,   // default values (taxes will be automatically calculated)
 			'calc_tax' => 'per_order',                 // applies to pickup package as a whole, regardless of items to be picked up
		) );
	}


}
