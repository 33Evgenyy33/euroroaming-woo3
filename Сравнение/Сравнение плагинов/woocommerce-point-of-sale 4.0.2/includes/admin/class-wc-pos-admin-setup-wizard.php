<?php
/**
 * Setup Wizard Class
 *
 * Takes new users through some basic steps to setup their store.
 *
 * @author      Actuality Extensions
 * @category    Admin
 * @package     WC_POS/Admin
 * @version     1.0.0
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_POS_Admin_Setup_Wizard class
 */
class WC_POS_Admin_Setup_Wizard {

	/** @var string Currenct Step */
	private $step   = '';

	/** @var array Steps for the setup wizard */
	private $steps  = array();


	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			add_action( 'admin_menu', array( $this, 'admin_menus' ) );
			add_action( 'admin_init', array( $this, 'setup_wizard' ) );
		}
	}

	/**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {
		add_dashboard_page( '', '', 'manage_options', WC_POS_TOKEN . '-setup', '' );
	}

	/**
	 * Show the setup wizard
	 */
	public function setup_wizard() {
		wc_pos_clear_transient();
		if ( empty( $_GET['page'] ) || WC_POS_TOKEN . '-setup' !== $_GET['page'] ) {
			return;
		}
		$this->steps = array(
			'introduction' => array(
				'name'    =>  __( 'Introduction', 'wc_point_of_sale' ),
				'view'    => array( $this, 'wc_pos_setup_introduction' ),
				'handler' => ''
			),
			'general_options' => array(
				'name'    =>  __( 'General Options', 'wc_point_of_sale' ),
				'view'    => array( $this, 'wc_pos_setup_general_options' ),
				'handler' => array( $this, 'wc_pos_setup_general_options_save' )
			),
			'tax' => array(
				'name'    =>  __( 'Tax & Payment', 'wc_point_of_sale' ),
				'view'    => array( $this, 'wc_pos_setup_tax' ),
				'handler' => array( $this, 'wc_pos_setup_tax_save' )
			),
			'outlet' => array(
				'name'    =>  __( 'Outlet Setup', 'wc_point_of_sale' ),
				'view'    => array( $this, 'wc_pos_setup_outlet' ),
				'handler' => array( $this, 'wc_pos_setup_outlet_save' )
			),
			'register' => array(
				'name'    =>  __( 'Register Setup', 'wc_point_of_sale' ),
				'view'    => array( $this, 'wc_pos_setup_register' ),
				'handler' => array( $this, 'wc_pos_setup_register_save' )
			),
			'next_steps' => array(
				'name'    =>  __( 'Finished!', 'wc_point_of_sale' ),
				'view'    => array( $this, 'wc_pos_setup_ready' ),
				'handler' => ''
			)
		);
		
		
		$this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );
		$suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
		wp_register_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2' . $suffix . '.js', array( 'jquery' ), '3.5.2' );
		wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js', array( 'jquery', 'select2' ), WC_VERSION );
		wp_localize_script( 'wc-enhanced-select', 'wc_enhanced_select_params', array(
			'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'wc_point_of_sale' ),
			'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'wc_point_of_sale' ),
			'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'wc_point_of_sale' ),
			'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'wc_point_of_sale' ),
			'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'wc_point_of_sale' ),
			'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'wc_point_of_sale' ),
			'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'wc_point_of_sale' ),
			'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'wc_point_of_sale' ),
			'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'wc_point_of_sale' ),
			'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'wc_point_of_sale' ),
			'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'wc_point_of_sale' ),
			'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'wc_point_of_sale' ),
			'ajax_url'                  => admin_url( 'admin-ajax.php' ),
			'search_products_nonce'     => wp_create_nonce( 'search-products' ),
			'search_customers_nonce'    => wp_create_nonce( 'search-customers' )
		) );
		wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
		wp_enqueue_style( WC_POS_TOKEN . '-setup', esc_url( WC_POS()->assets_url ) . 'css/wc-pos-setup.css', array( 'dashicons', 'install' ), WC_VERSION );



		//wp_enqueue_script();

		wp_register_script(WC_POS_TOKEN . '-sort', esc_url( WC_POS()->assets_url ) . 'js/setup.js', array('jquery-ui-sortable'), WC_POS_VERSION);
		wp_localize_script( WC_POS_TOKEN . '-sort', 'wc_country_select_params', array(
					'countries'                 => json_encode( array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() ) ),
					'i18n_select_state_text'    => esc_attr__( 'Select an option&hellip;', 'wc_point_of_sale' ),
					'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'wc_point_of_sale' ),
					'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'wc_point_of_sale' ),
					'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'wc_point_of_sale' ),
					'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'wc_point_of_sale' ),
					'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'wc_point_of_sale' ),
					'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'wc_point_of_sale' ),
					'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'wc_point_of_sale' ),
					'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'wc_point_of_sale' ),
					'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'wc_point_of_sale' ),
					'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'wc_point_of_sale' ),
					'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'wc_point_of_sale' ),
					'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'wc_point_of_sale' ),
				) );

		wp_register_script( WC_POS_TOKEN . '-setup', WC()->plugin_url() . '/assets/js/admin/wc-setup.min.js', array( 'jquery', 'wc-enhanced-select', 'jquery-blockui', 'jquery-ui-progressbar', WC_POS_TOKEN . '-sort' ), WC_VERSION );
		wp_localize_script( WC_POS_TOKEN . '-setup', 'wc_setup_params', array(
			'locale_info' => json_encode( include( WC()->plugin_path() . '/i18n/locale-info.php' ) )
		) );

		if ( ! empty( $_POST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
			call_user_func( $this->steps[ $this->step ]['handler'] );
		}

		header('Content-Type: text/html; charset=utf-8');
		ob_start();
		$this->setup_wizard_header();
		
			$this->setup_wizard_steps();			
			$this->setup_wizard_content();
		$this->setup_wizard_footer();
		exit;
	}

	public function get_next_step_link() {
		$keys = array_keys( $this->steps );
		return add_query_arg( 'step', $keys[ array_search( $this->step, array_keys( $this->steps ) ) + 1 ], remove_query_arg( 'translation_updated' ) );
	}

	/**
	 * Setup Wizard Header
	 */
	public function setup_wizard_header() {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php _e( 'WooCommerce Point of Sale &rsaquo; Setup Wizard', 'wc_point_of_sale' ); ?></title>
			<?php wp_print_scripts( WC_POS_TOKEN . '-setup' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php #do_action( 'admin_head' ); ?>
		</head>
		<body class="wc-setup wp-core-ui">
			<h2 id="logos">
				<img id="ae-logo" src="<?php echo esc_url( WC_POS()->assets_url ); ?>images/ae-logo.png" alt="Actuality Extensions" />
			</h2>
		<?php
	}

	/**
	 * Setup Wizard Footer
	 */
	public function setup_wizard_footer() {
		?>
			<?php if ( 'next_steps' === $this->step ) : ?>
				<a class="wc-return-to-dashboard" href="<?php echo esc_url( admin_url() ); ?>"><?php _e( 'Return to the WordPress Dashboard', 'wc_point_of_sale' ); ?></a>
			<?php endif; ?>
			</body>
		</html>
		<?php
	}

	/**
	 * Output the steps
	 */
	public function setup_wizard_steps() {
		$ouput_steps = $this->steps;
		array_shift( $ouput_steps );
		?>
		<ol class="wc-setup-steps">
			<?php foreach ( $ouput_steps as $step_key => $step ) : ?>
				<li class="<?php
					if ( $step_key === $this->step ) {
						echo 'active';
					} elseif ( array_search( $this->step, array_keys( $this->steps ) ) > array_search( $step_key, array_keys( $this->steps ) ) ) {
						echo 'done';
					}
				?>"><?php echo esc_html( $step['name'] ); ?></li>
			<?php endforeach; ?>
		</ol>
		<?php
	}

	/**
	 * Output the content for the current step
	 */
	public function setup_wizard_content() {
		echo '<div class="wc-setup-content">';
		call_user_func( $this->steps[ $this->step ]['view'] );
		echo '</div>';
	}

	/**
	 * Introduction step
	 */
	public function wc_pos_setup_introduction() {
		$this->create_receipt();
		?>
		<h1><?php _e( 'Welcome to WooCommerce Point of Sale!', 'wc_point_of_sale' ); ?></h1>
		<p><?php _e( 'Thank you for choosing WooCommerce Point of Sale to sell your products in-store! This quick setup wizard will help you configure the basic settings.', 'wc_point_of_sale' ); ?> <b><?php _e( 'It\'s completely optional and shouldn\'t take longer than five minutes.', 'wc_point_of_sale' ); ?></b></p>
		<p><?php _e( 'No time right now? If you don’t want to go through the wizard, you can skip and return to the WordPress dashboard. Come back anytime if you change your mind!', 'wc_point_of_sale' ); ?></p>
		<p class="wc-setup-actions step">
			<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button-primary button button-large button-next"><?php _e( 'Let\'s Go!', 'wc_point_of_sale' ); ?></a>
			<a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" class="button button-large"><?php _e( 'Not right now', 'wc_point_of_sale' ); ?></a>
		</p>
		<?php
	}

	private function create_receipt()
	{
		global $wpdb;
		if( $id = $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}wc_poin_of_sale_receipts LIMIT 1") ) {
			update_option('_pos_default_receipt', $id);
		}else{
			$receipt = WC_POS()->receipt();
			$data = $receipt::get_default_receipt_options();
			$data['name'] = __('Default Receipt', 'wc_point_of_sale');
			$table_name = $wpdb->prefix . "wc_poin_of_sale_receipts";
			$rows_affected = $wpdb->insert( $table_name, $data );
			$id = $wpdb->insert_id;
			update_option('_pos_default_receipt', $id);
		}
	}
	

	/**
	 * General settings
	 */
	public function wc_pos_setup_outlet() {		
		?>
		<h1><?php _e( 'Outlet Setup', 'wc_point_of_sale' ); ?></h1>
		<form method="post">
			<p><?php echo _e( 'Your store needs an outlet set up before you can load and use the registers to sell your products in-store. Outlets are also considered to be like your physical store.', 'wc_point_of_sale' ); ?></p>
			<table class="form-table">
				<tr>
					<th scope="row"><?php echo _e( 'Name', 'wc_point_of_sale' ); ?></th>
					<td>
						<input type="text" aria-required="true" size="40" style="width: 100%;" id="_outlet_name" name="_outlet_name">
						<span class="description">
							<?php _e( 'The name of the outlet as it appears when opening a register.', 'wc_point_of_sale' ); ?>
						</span>
					</td>
				</tr>
				<?php
				$outlet = WC_POS()->outlet();
				$outlet->init_form_fields();
				foreach ( $outlet->outlet_address_fields as $key => $field ) {
					if ( ! isset( $field['type'] ) )
						$field['type'] = 'text';
						$value = "";
						if(isset( $field['value']) ) {
							$value = $field['value'];
						}

					switch ( $field['type'] ) {
						case "select" :
							$cl = 'wc-enhanced-select';
							if( $key == 'country'){
								$cl = 'country_select country_to_state';
							}elseif( $key == 'state'){
								$cl = 'state_select';
							}
							wc_pos_select( array( 
								'id'                 => '_outlet_'. $key,
								'class'             => $cl,
								'label'             => isset($field['label']) ? $field['label'] : '',
								'options'           => $field['options'],
								'value'             => $value,
								'description'       => isset($field['description']) ? $field['description'] : '',
								'wrapper_tag'       => 'tr',
								'wrapper_label_tag' => '<th scope="row">%s</th>',
								'wrapper_field_tag' => '<td>%s</td>'));
						break;
						default :
							wc_pos_text_input( array(
								'id'                => '_outlet_' . $key,
								'label'             => isset($field['label']) ? $field['label'] : '',
								'value'             => $value,
								'description'       => isset($field['description']) ? $field['description'] : '',
								'wrapper_tag'       => 'tr',
								'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>',
								'wrapper_field_tag' => '<td>%s</td>') );
						break;
					}
				}
				?>
				<tr class="form-field">
					<th colspan="2">
						<h3><?php _e( 'Contact Details', 'wc_point_of_sale' ); ?></h3>
						<span class="description">
						<?php _e( 'Enter the contact details of the outlet as this will appear on receipts that are printed from registers at this outlet.', 'wc_point_of_sale' ); ?>
						</span>
					</th>
				</tr>
				<?php 
					foreach ( $outlet->outlet_contact_fields as $key => $field ) {
						if ( ! isset( $field['type'] ) )
							$field['type'] = 'text';
						$value = "";
						
						switch ( $field['type'] ) {
						case "select" :
							wc_pos_select( array( 
								'id'                 => '_outlet_'. $key,
								'label'             => isset($field['label']) ? $field['label'] : '',
								'options'           => $field['options'],
								'class'             => 'wc-enhanced-select',
								'value'             => $value,
								'description'       => isset($field['description']) ? $field['description'] : '',
								'wrapper_tag'       => 'tr',
								'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>',
								'wrapper_field_tag' => '<td>%s</td>'));
						break;
						default :
							wc_pos_text_input( array(
								'id'                => '_outlet_' . $key,
								'label'             => isset($field['label']) ? $field['label'] : '',
								'value'             => $value,
								'description'       => isset($field['description']) ? $field['description'] : '',
								'wrapper_tag'       => 'tr',
								'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>',
								'wrapper_field_tag' => '<td>%s</td>') );
						break;
						}
					}
				?>
				<tr class="form-field">
					<th colspan="2">
						<h3><?php _e( 'Social Details', 'wc_point_of_sale' ); ?></h3>
						<p class="description"><?php echo _e( 'Enter the social details of the outlet as this will appear on receipts that are printed from registers at this outlet.', 'wc_point_of_sale' ); ?></p>
					</th>
				</tr>
				<?php 
					foreach ( $outlet->outlet_social_fields as $key => $field ) {
						if ( ! isset( $field['type'] ) )
							$field['type'] = 'text';
							$value = "";							
							if($key == 'twitter'){
								$value = '@'.str_replace('@', '', $value);
							}
						switch ( $field['type'] ) {
							case "select" :
								wc_pos_select( array( 'id' => '_outlet_' . $key, 'label' => $field['label'], 'options' => $field['options'], 'value' => $value, 'description' =>  $field['description'], 'wrapper_tag' => 'tr', 'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>', 'wrapper_field_tag' => '<td>%s</td>', 'class'             => 'wc-enhanced-select' ) );
							break;
							default :
								wc_pos_text_input( array( 'id' => '_outlet_' . $key, 'label' => $field['label'], 'value' => $value, 'description' =>  $field['description'], 'wrapper_tag' => 'tr', 'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>', 'wrapper_field_tag' => '<td>%s</td>' ) );
							break;
						}
					}
				?>
			</table>
			<p class="wc-setup-actions step">
				<input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Continue', 'wc_point_of_sale' ); ?>" name="save_step" />
				<?php wp_nonce_field( WC_POS_TOKEN . '-setup' ); ?>
			</p>
		</form>
		<?php
	}

	/**
	 * Save Locale Settings
	 */
	public function wc_pos_setup_outlet_save() {
		check_admin_referer( WC_POS_TOKEN . '-setup' );

		$outlet_name         = $_POST['_outlet_name'];
		if( !empty($outlet_name) ){
			$outlet = WC_POS()->outlet();
			$outlet->save_outlet();
		}

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Tax setup
	 */
	public function wc_pos_setup_tax() {

		// Defaults
		$tax_based_on     = get_option( 'woocommerce_pos_calculate_tax_based_on', 'outlet' );
		$tax_calculation  = get_option( 'woocommerce_pos_tax_calculation', 'disabled' );
		$customer_address = get_option( 'woocommerce_pos_tax_default_customer_address', 'outlet' );
		$new_order_email  = get_option( 'wc_pos_email_notifications', 'no' );
		$new_account_email = get_option( 'wc_pos_automatic_emails', 'yes' );
		?>
		<h1><?php _e( 'Tax Setup', 'wc_point_of_sale' ); ?></h1>
		<form method="post">
			<table class="form-table" cellspacing="0">
				<tbody>
					<?php
					wc_pos_select( array( 
								'id'                 => 'woocommerce_pos_tax_calculation',
								'label'             => __( 'Tax Calculation', 'wc_point_of_sale' ),
								'class'             => 'wc-enhanced-select',
								'options'           => array(
									'enabled'  => __( 'Enabled (using WooCommerce configurations)', 'wc_point_of_sale' ),
									'disabled' => __( 'Disabled', 'wc_point_of_sale' ),
								),
								'value'             => $tax_calculation,
								'description'       => __( 'Enables the calculation of tax using the WooCommerce configurations.', 'wc_point_of_sale' ), 
								'wrapper_tag'       => 'tr',
								'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>',
								'wrapper_field_tag' => '<td>%s</td>'));
					wc_pos_select( array( 
								'id'                 => 'woocommerce_pos_calculate_tax_based_on',
								'label'             => __( 'Calculate Tax Based On', 'wc_point_of_sale' ),
								'class'             => 'wc-enhanced-select',
								'options'           => array(
									'default'  => __( 'Default WooCommerce', 'wc_point_of_sale' ),
									'shipping' => __( 'Customer shipping address', 'wc_point_of_sale' ),
									'billing'  => __( 'Customer billing address', 'wc_point_of_sale' ),
									'base'     => __( 'Shop base address', 'wc_point_of_sale' ),
									'outlet'   => __( 'Outlet address', 'wc_point_of_sale' ),
								),
								'value'             => $tax_based_on,
								'description'       => __( 'This option determines which address used to calculate tax.', 'wc_point_of_sale' ),
								'wrapper_tag'       => 'tr',
								'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>',
								'wrapper_field_tag' => '<td>%s</td>'));
					wc_pos_select( array( 
								'id'                 => 'woocommerce_pos_tax_default_customer_address',
								'label'             => __( 'Default Customer Address', 'wc_point_of_sale' ),
								'class'             => 'wc-enhanced-select',
								'options'           => array(
									'no_address' => __( 'No address', 'wc_point_of_sale' ),
									'base'       => __( 'Shop base address', 'wc_point_of_sale' ),
									'outlet'     => __( 'Outlet address', 'wc_point_of_sale' ),
								),
								'value'             => $customer_address,
								'description'       =>  __( 'This option determines which address used to calculate tax for the default customer such as Guest.', 'wc_point_of_sale' ),
								'wrapper_tag'       => 'tr',
								'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>',
								'wrapper_field_tag' => '<td>%s</td>'));
					?>
					<tr class="section_title">
						<td colspan="2">
							<h2><?php _e( 'Payment Methods', 'wc_point_of_sale' ); ?></h2>
						</td>
					</tr>
					<?php
					include_once( WC()->plugin_path().'/includes/admin/settings/class-wc-settings-page.php' );					
					$pos_ipm = include( WC_POS()->plugin_path().'/includes/admin/settings/wc-pos-settings-payment_methods.php' );
					$pos_ipm->installed_payment_gateways_setting();
					?>
					<tr class="section_title">
					<td colspan="2">
						<h2><?php _e( 'Email Options', 'wc_point_of_sale' ); ?></h2>
						<p><?php _e( 'The following options affect the email notifications when orders are placed and accounts are created.', 'wc_point_of_sale' ); ?></p>
					</td>
					</tr>
					<tr>
					<th scope="row"><label for="wc_pos_email_notifications"><?php echo _e( 'New Order', 'wc_point_of_sale' ); ?></label></th>
					<td>
						<input type="checkbox" id="wc_pos_email_notifications" <?php checked( $new_order_email, 'yes' ); ?> name="wc_pos_email_notifications" class="input-checkbox" value="1" />
						<label for="wc_pos_email_notifications"><?php _e( 'Enable new order notification', 'wc_point_of_sale' ); ?></label>
						<p class="description"><?php _e( 'New order emails are sent to the recipient list when an order is received as shown ', 'wc_point_of_sale' ); ?> <a href="<?php echo admin_url('admin.php?page=wc-settings&tab=email&section=wc_email_new_order');?>" target="_blank"><?php _e( 'here', 'wc_point_of_sale' ); ?></a>.</p>
					</td>
					</tr>
					<tr>
					<th scope="row"><label for="wc_pos_automatic_emails"><?php echo _e( 'Account Creation', 'wc_point_of_sale' ); ?></label></th>
					<td>
						<input type="checkbox" id="wc_pos_automatic_emails" <?php checked( $new_account_email, 'yes' ); ?> name="wc_pos_automatic_emails" class="input-checkbox" value="1" />
						<label for="wc_pos_automatic_emails"><?php _e( 'Enable account creation notification', 'wc_point_of_sale' ); ?></label>
						<p class="description"><?php _e( 'Customer emails are sent to the customer when a customer signs up via checkout or account pages as shown ', 'wc_point_of_sale' ); ?> <a href="<?php echo admin_url('admin.php?page=wc-settings&tab=email&section=wc_email_customer_new_account');?>" target="_blank"><?php _e( 'here', 'wc_point_of_sale' ); ?></a>.</p>
					</td>
					</tr>
				</tbody>
			</table>

			<p class="wc-setup-actions step">
				<input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Continue', 'wc_point_of_sale' ); ?>" name="save_step" />
			</p>
		</form>
		<?php
	}

	/**
	 * Save Tax Settings
	 */
	public function wc_pos_setup_tax_save() {
		$tax_based_on      = $_POST['woocommerce_pos_calculate_tax_based_on'];
		$tax_calculation   = $_POST['woocommerce_pos_tax_calculation'];
		$customer_address  = $_POST['woocommerce_pos_tax_default_customer_address'];
		$new_order_email   = isset($_POST['wc_pos_email_notifications']) ? 'yes' : 'no';
		$new_account_email = isset($_POST['wc_pos_automatic_emails']) ? 'yes' : 'no';

		update_option( 'woocommerce_pos_calculate_tax_based_on', $tax_based_on );
		update_option( 'woocommerce_pos_tax_calculation', $tax_calculation );
		update_option( 'woocommerce_pos_tax_default_customer_address', $customer_address );
		update_option( 'wc_pos_email_notifications', $new_order_email );
		update_option( 'wc_pos_automatic_emails', $new_account_email );

		$pos_enabled_gateways = ( isset( $_POST['pos_enabled_gateways'] ) ) ?  $_POST['pos_enabled_gateways'] : array();
		update_option( 'pos_enabled_gateways', $pos_enabled_gateways );

		$pos_exist_gateways = ( isset( $_POST['pos_exist_gateways'] ) ) ?  $_POST['pos_exist_gateways'] : array();
		update_option( 'pos_exist_gateways', $pos_exist_gateways );

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}


	/**
	 * Register setup
	 */
	public function wc_pos_setup_register() {
		?>
		<h1><?php _e( 'Register Setup', 'wc_point_of_sale' ); ?></h1>
		<form method="post">
			<table class="form-table" cellspacing="0">
				<tbody>
					<?php
					$register = WC_POS()->register();
					$register->init_form_fields();
					$fields = array('name', 'grid_template', 'receipt_template', 'outlet');
							foreach ( $register::$register_detail_fields as $key => $field ) {
								if( !in_array($key, $fields) ) continue;
								if ( ! isset( $field['type'] ) )
									$field['type'] = 'text';
									$value = "";
									if(isset( $field['value']) ) {
										$value = $field['value'];
									}

									if($key == 'default_customer' && $value !=0){
										$customer = get_userdata($value);
										$field['options'][$customer->ID] = $customer->first_name . ' ' . $customer->last_name . ' &ndash; ' . sanitize_email( $customer->user_email ); 
									}

								switch ( $field['type'] ) {
									case "select" :
										wc_pos_select( array( 'id' => '_register_'. $key, 'label' => $field['label'], 'class' => 'wc-enhanced-select', 'options' => $field['options'], 'value' => $value, 'description' =>  $field['description'], 'wrapper_tag' => 'tr', 'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>', 'wrapper_field_tag' => '<td>%s</td>'));
									break;
									case "radio" :
										wc_pos_radio( array( 'id' => '_register_'. $key, 'label' => $field['label'], 'options' => $field['options'], 'value' => $value, 'description' =>  $field['description'], 'wrapper_tag' => 'tr', 'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>', 'wrapper_field_tag' => '<td>%s</td>'));
									break;
									default :
										wc_pos_text_input( array( 'id' => '_register_' . $key, 'label' => $field['label'], 'value' => $value, 'description' =>  $field['description'], 'wrapper_tag' => 'tr', 'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>', 'wrapper_field_tag' => '<td>%s</td>') );
									break;
								}
							}
							foreach ( $register::$register_end_of_sale_fields as $key => $field ) {
									if ( ! isset( $field['type'] ) )
										$field['type'] = 'text';
									if ( ! isset( $field['value'] ) )
										$field['value'] = '';
										
									switch ( $field['type'] ) {
										case "select" :
											wc_pos_select( array( 'id' => '_register_' . $key, 'label' => $field['label'], 'class' => 'wc-enhanced-select', 'options' => $field['options'], 'value' => $field['value'], 'description' =>  $field['description'], 'wrapper_tag' => 'tr', 'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>', 'wrapper_field_tag' => '<td>%s</td>' ) );
										break;
										case "radio" :
											wc_pos_radio( array( 'id' => '_register_'. $key, 'label' => $field['label'], 'options' => $field['options'], 'value' => $field['value'], 'description' =>  $field['description'], 'wrapper_tag' => 'tr', 'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>', 'wrapper_field_tag' => '<td>%s</td>'));
										break;
										default :
											wc_pos_text_input( array( 'id' => '_register_' . $key, 'label' => $field['label'], 'value' => $field['value'], 'description' =>  $field['description'], 'wrapper_tag' => 'tr', 'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>', 'wrapper_field_tag' => '<td>%s</td>' ) );
										break;
									}
								}
						?>
				</tbody>
			</table>

			<p class="wc-setup-actions step">
				<input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Continue', 'wc_point_of_sale' ); ?>" name="save_step" />
			</p>
		</form>
		<?php
	}

	/**
	 * Save Register
	 */
	public function wc_pos_setup_register_save() {
		$register_name = isset($_POST['_register_name']) ? $_POST['_register_name'] : '';
		if( !empty($register_name) ){
			$register = WC_POS()->register();
			$register->save_register(false);
		}

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Fetch customers setup
	 */
	public function wc_pos_setup_general_options() {		
		// Defaults
		$country_setting = get_option( 'woocommerce_default_country', 'GB' );
		$discount_preset = get_option( 'woocommerce_pos_register_discount_presets', array(5, 10, 15, 20) );
		$quantity_increment = get_option( 'woocommerce_pos_register_instant_quantity', 'no' );
		$quantity_keypad = get_option( 'woocommerce_pos_register_instant_quantity_keypad', 'no' );
		$tile_layout = get_option( 'wc_pos_tile_layout', 'image_title' );
		$variables = get_option( 'wc_pos_tile_variables', 'overlay' );
		$order_status    = get_option( 'woocommerce_pos_end_of_sale_order_status', 'processing' );
		$save_order_status    = get_option( 'wc_pos_save_order_status', 'wc-pending' );
		$load_order_status    = get_option( 'wc_pos_load_order_status', 'wc-pending' );
		$load_web_orders    = get_option( 'wc_pos_load_web_order', 'no' );
		$ready_to_scan    = get_option( 'woocommerce_pos_register_ready_to_scan', 'no' );
		$cc_scanning    = get_option( 'woocommerce_pos_register_cc_scanning', 'no' );

		$order_statuses = wc_get_order_statuses();
		$statuses = array();
		foreach ($order_statuses as $key => $value) {
			$a = substr($key, 3);
			$statuses[$a] = $value;
		}
		?>
		<h1><?php _e( 'General Options', 'wc_point_of_sale' ); ?></h1>
		<form method="post">
			<table class="form-table" cellspacing="0">
				<tbody>
					<?php

					if ( strstr( $country_setting, ':' ) ) {
						$country_setting = explode( ':', $country_setting );
						$country         = current( $country_setting );
						$state           = end( $country_setting );
					} else {
						$country = $country_setting;
						$state   = '*';
					}
					?><tr valign="top">
						<th scope="row" class="titledesc">
							<?php _e( 'Default Country', 'wc_point_of_sale' ); ?>
						</th>
						<td>
							<select name="wc_pos_default_country" data-placeholder="<?php esc_attr_e( 'Choose a country&hellip;', 'wc_point_of_sale' ); ?>" title="<?php esc_attr_e( 'Country', 'wc_point_of_sale' ) ?>" class="wc-enhanced-select">
							<?php WC()->countries->country_dropdown_options( $country, $state ); ?>
							</select>
							<span class="description">
								<?php _e( 'Sets the default country for shipping and customer accounts.', 'wc_point_of_sale' ); ?>
							</span>
						</td>
					</tr>
					<?php
					wc_pos_select( array( 
								'id'                 => 'woocommerce_pos_register_discount_presets',
								'label'             => __( 'Discount Presets', 'wc_point_of_sale' ),
								'class'             => 'wc-enhanced-select',
								'type'    			=> 'multiselect',
								'options' => apply_filters('woocommerce_pos_register_discount_presets', array(
									5 => __( '5%', 'wc_point_of_sale' ),
									10 => __( '10%', 'wc_point_of_sale' ),
									15 => __( '15%', 'wc_point_of_sale' ),
									20 => __( '20%', 'wc_point_of_sale' ),
									25 => __( '25%', 'wc_point_of_sale' ),
									30 => __( '30%', 'wc_point_of_sale' ),
									35 => __( '35%', 'wc_point_of_sale' ),
									40 => __( '40%', 'wc_point_of_sale' ),
									45 => __( '45%', 'wc_point_of_sale' ),
									50 => __( '50%', 'wc_point_of_sale' ),
									55 => __( '55%', 'wc_point_of_sale' ),
									60 => __( '60%', 'wc_point_of_sale' ),
									65 => __( '65%', 'wc_point_of_sale' ),
									70 => __( '70%', 'wc_point_of_sale' ),
									75 => __( '75%', 'wc_point_of_sale' ),
									80 => __( '80%', 'wc_point_of_sale' ),
									85 => __( '85%', 'wc_point_of_sale' ),
									90 => __( '90%', 'wc_point_of_sale' ),
									95 => __( '95%', 'wc_point_of_sale' ),
									100 => __( '100%', 'wc_point_of_sale' )
									)),
								'value'             => $discount_preset,
								'description'       => __( 'Define the preset discount buttons when applying discount to the order.', 'wc_point_of_sale' ),
								'wrapper_tag'       => 'tr',
								'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>',
								'wrapper_field_tag' => '<td>%s</td>'));
					?>
					<tr class="section_title">
					<td colspan="2">
						<h2><?php _e( 'Tile Options', 'wc_point_of_sale' ); ?></h2>
						<p><?php echo _e( 'Select the options that affect how the tiles appear on the product grid.', 'wc_point_of_sale' ); ?></p
					</td>
					</tr>
					<tr>
					<th scope="row"><label for="woocommerce_pos_register_instant_quantity"><?php echo _e( 'Quantity Increment', 'wc_point_of_sale' ); ?></label></th>
					<td>
						<input type="checkbox" id="woocommerce_pos_register_instant_quantity" <?php checked( $quantity_increment, 'yes' ); ?> name="woocommerce_pos_register_instant_quantity" class="input-checkbox" value="1" />
						<label for="woocommerce_pos_register_instant_quantity"><?php _e( 'Enable quantity increment', 'wc_point_of_sale' ); ?></label>
						<p class="description"><?php _e( 'Shows a quantity increment button when adding products to the basket.', 'wc_point_of_sale' ); ?></p>
					</td>
					</tr>
					<tr>
					<th scope="row"><label for="woocommerce_pos_register_instant_quantity_keypad"><?php echo _e( 'Quantity Keypad', 'wc_point_of_sale' ); ?></label></th>
					<td>
						<input type="checkbox" id="woocommerce_pos_register_instant_quantity_keypad" <?php checked( $quantity_keypad, 'yes' ); ?> name="woocommerce_pos_register_instant_quantity_keypad" class="input-checkbox" value="1" />
						<label for="woocommerce_pos_register_instant_quantity_keypad"><?php _e( 'Enable quantity keypad', 'wc_point_of_sale' ); ?></label>
						<p class="description"><?php _e( 'Shows a quantity increment button and a keypad when adding products to the basket.', 'wc_point_of_sale' ); ?></p>
					</td>
					</tr>
					<?php
					wc_pos_select( array( 
								'id'                 => 'wc_pos_tile_layout',
								'label'             => __( 'Tile Layout', 'wc_point_of_sale' ),
								'class'             => 'wc-enhanced-select',
								'options' 		    => array(
									'image'       => __( 'Product image', 'wc_point_of_sale' ),
									'image_title' => __( 'Product image and title', 'wc_point_of_sale' ),
									'image_title_price' => __( 'Product image, title and price', 'wc_point_of_sale' ),
									),
								'value'             => $tile_layout,
								'description'       => __( 'This controls the layout of the tile on the product grid.', 'wc_point_of_sale' ),
								'wrapper_tag'       => 'tr',
								'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>',
								'wrapper_field_tag' => '<td>%s</td>'));
								
					wc_pos_select( array( 
								'id'                 => 'wc_pos_tile_variables',
								'label'             => __( 'Variables', 'wc_point_of_sale' ),
								'class'             => 'wc-enhanced-select',
								'options'  => array(
									'overlay' => __( 'Overlay', 'wc_point_of_sale' ),
									'tiles' => __( 'Tiles', 'wc_point_of_sale' ),
								),
								'value'             => $variables,
								'description'       => __( 'Settings to choose how variables can be shown.', 'wc_point_of_sale' ),
								'wrapper_tag'       => 'tr',
								'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>',
								'wrapper_field_tag' => '<td>%s</td>'));
					
					?>
					<tr class="section_title">
					<td colspan="2">
						<h2><?php _e( 'Status Options', 'wc_point_of_sale' ); ?></h2>
						<p><?php echo _e( 'Select the status of the orders processed through the register.', 'wc_point_of_sale' ); ?></p
					</td>
					</tr>
					<?php	
					wc_pos_select( array( 
								'id'                 => 'woocommerce_pos_end_of_sale_order_status',
								'label'             => __( 'Complete Order', 'wc_point_of_sale' ),
								'class'             => 'wc-enhanced-select',
								'options'           => apply_filters('woocommerce_pos_end_of_sale_order_status', $statuses),
								'value'             => $order_status,
								'description'       => __( 'Select the order status of completed orders when using the register.', 'wc_point_of_sale' ),
								'wrapper_tag'       => 'tr',
								'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>',
								'wrapper_field_tag' => '<td>%s</td>'));

					wc_pos_select( array( 
								'id'                 => 'wc_pos_save_order_status',
								'label'             => __( 'Save Order', 'wc_point_of_sale') ,
								'class'             => 'wc-enhanced-select',
								'options'           => apply_filters('wc_pos_save_order_status', $order_statuses),
								'value'             => $save_order_status,
								'description'       => __( 'Select the order status of saved orders when using the register.', 'wc_point_of_sale' ),
								'wrapper_tag'       => 'tr',
								'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>',
								'wrapper_field_tag' => '<td>%s</td>'));
					wc_pos_select( array( 
								'id'                 => 'wc_pos_load_order_status',
								'label'             =>  __( 'Load Order ', 'wc_point_of_sale' ) ,
								'class'             => 'wc-enhanced-select',
								'type'              => 'multiselect',
								'options'           => apply_filters('wc_pos_load_order_status', $order_statuses),
								'value'             => $load_order_status,
								'description'       => __( 'Select the order status of loaded orders when using the register.', 'wc_point_of_sale' ),
								'wrapper_tag'       => 'tr',
								'wrapper_label_tag' => '<th valign="top" scope="row">%s</th>',
								'wrapper_field_tag' => '<td>%s</td>'));
					?>
					<tr>
					<th scope="row"><label for="wc_pos_load_web_order"><?php echo _e( 'Load Web Orders', 'wc_point_of_sale' ); ?></label></th>
					<td>
						<input type="checkbox" id="wc_pos_load_web_order" <?php checked( $load_web_orders, 'yes' ); ?> name="wc_pos_load_web_order" class="input-checkbox" value="1" />
						<label for="wc_pos_load_web_order"><?php _e( 'Load web orders', 'wc_point_of_sale' ); ?></label>
						<p class="description"><?php _e( 'Check this box to load orders placed through the web store.', 'wc_point_of_sale' ); ?></p>
					</td>
					</tr>
					<tr class="section_title">
					<td colspan="2">
						<h2><?php _e( 'Scanning Options', 'wc_point_of_sale' ); ?></h2>
						<p><?php echo _e( 'The following options affect the use of scanning hardware such as barcode scanners and magnetic card readers.', 'wc_point_of_sale' ); ?></p
					</td>
					</tr>
					<tr>
					<th scope="row"><label for="woocommerce_pos_register_ready_to_scan"><?php echo _e( 'Barcode Scanning', 'wc_point_of_sale' ); ?></label></th>
					<td>
						<input type="checkbox" id="woocommerce_pos_register_ready_to_scan" <?php checked( $ready_to_scan, 'yes' ); ?> name="woocommerce_pos_register_ready_to_scan" class="input-checkbox" value="1" />
						<label for="woocommerce_pos_register_ready_to_scan"><?php _e( 'Enable barcode scanning', 'wc_point_of_sale' ); ?></label>
						<p class="description"><?php _e( 'Listens to barcode scanners and adds item to basket. Carriage return in scanner recommended.', 'wc_point_of_sale' ); ?></p>
					</td>
					</tr>
					<tr>
					<th scope="row"><label for="woocommerce_pos_register_cc_scanning"><?php echo _e( 'Credit/Debit Card Scanning', 'wc_point_of_sale' ); ?></label></th>
					<td>
						<input type="checkbox" id="woocommerce_pos_register_cc_scanning" <?php checked( $cc_scanning, 'yes' ); ?> name="woocommerce_pos_register_cc_scanning" class="input-checkbox" value="1" />
						<label for="woocommerce_pos_register_cc_scanning"><?php _e( 'Enable credit/debit card scanning', 'wc_point_of_sale' ); ?></label>
						<p class="description"><?php _e( 'Allows magnetic card readers to parse scanned output into checkout fields. Supported payment gateways can be found', 'wc_point_of_sale' ); ?> <a href="http://actualityextensions.com/supported-payment-gateways/" target="_blank"><?php _e( 'here', 'wc_point_of_sale' ); ?></a>.</p>
					</td>
					</tr>
				</tbody>
			</table>

			<p class="wc-setup-actions step">
				<input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Continue', 'wc_point_of_sale' ); ?>" name="save_step" />
			</p>
		</form>
		<?php
	}
	
	/**
	 * Fetch customers Settings
	 */
	public function wc_pos_setup_general_options_save() {
		$country_setting     = $_POST['wc_pos_default_country'];;
		$discount_preset     = $_POST['woocommerce_pos_register_discount_presets'];;
		$quantity_increment = isset( $_POST['woocommerce_pos_register_instant_quantity'] ) ? 'yes' : 'no';;
		$quantity_keypad = isset( $_POST['woocommerce_pos_register_instant_quantity_keypad'] ) ? 'yes' : 'no';;
		$tile_layout		 = $_POST['wc_pos_tile_layout'];;
		$variables		 = $_POST['wc_pos_tile_variables'];;
		$order_status        = $_POST['woocommerce_pos_end_of_sale_order_status'];;
		$save_order_status   = $_POST['wc_pos_save_order_status'];;
		$load_order_status   = $_POST['wc_pos_load_order_status'];;
		$load_web_orders = isset( $_POST['wc_pos_load_web_order'] ) ? 'yes' : 'no';;
		$ready_to_scan = isset( $_POST['woocommerce_pos_register_ready_to_scan'] ) ? 'yes' : 'no';;
		$cc_scanning = isset( $_POST['woocommerce_pos_register_cc_scanning'] ) ? 'yes' : 'no';;
		
		update_option( 'wc_pos_default_country', $country_setting );
		update_option( 'woocommerce_pos_register_discount_presets', $discount_preset );
		update_option( 'woocommerce_pos_register_instant_quantity', $quantity_increment );
		update_option( 'woocommerce_pos_register_instant_quantity_keypad', $quantity_keypad );
		update_option( 'wc_pos_tile_layout', $tile_layout );
		update_option( 'wc_pos_tile_variables', $variables );
		update_option( 'woocommerce_pos_end_of_sale_order_status', $order_status );
		update_option( 'wc_pos_save_order_status', $save_order_status );
		update_option( 'wc_pos_load_order_status', $load_order_status );
		update_option( 'wc_pos_load_web_order', $load_web_orders );
		update_option( 'woocommerce_pos_register_ready_to_scan', $ready_to_scan );
		update_option( 'woocommerce_pos_register_cc_scanning', $cc_scanning );

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}


	/**
	 * Final step
	 */
	public function wc_pos_setup_ready() {
		WC_POS_Admin_Notices::remove_notice( 'pos_install' );
		?>

		<h1><?php _e( 'WooCommerce Point of Sale is Ready!', 'wc_point_of_sale' ); ?></h1>

		<div class="wc-setup-next-steps">
			<div class="wc-setup-next-steps-first">
				<h2><?php _e( 'Next Steps', 'wc_point_of_sale' ); ?></h2>
				<ul>
					<li class="setup-product"><a class="button button-primary button-large" href="<?php echo esc_url( admin_url( 'admin.php?page=wc_pos_registers' ) ); ?>"><?php _e( 'View and manage your registers!', 'wc_point_of_sale' ); ?></a></li>
				</ul>
			</div>
			<div class="wc-setup-next-steps-last">
				<h2><?php _e( 'Learn More', 'wc_point_of_sale' ); ?></h2>
				<ul>
					<li class="newsletter"><a href="http://eepurl.com/Ybb5j" target="_blank"><?php _e( 'Subscribe to our newlsetter', 'wc_point_of_sale' ); ?></a></li>
					<li class="learn-more"><a href="http://actualityextensions.com/documentation/" target="_blank"><?php _e( 'Read more about getting started', 'wc_point_of_sale' ); ?></a></li>
					<li class="shop-more"><a href="http://codecanyon.net/user/actualityextensions/portfolio/" target="_blank"><?php _e( 'Explore our other powerful extensions', 'wc_point_of_sale' ); ?></a></li>
				</ul>
			</div>
		</div>
		<?php
	}

	public function exist_oulet()
	{
		global $wpdb;
		$result = false;
		$table  = $wpdb->prefix . 'wc_poin_of_sale_outlets';
		if( $d = $wpdb->get_var('SELECT ID FROM {$table} LIMIT 1') ){
			$result = true;
		}
		return $result;
	}
}

new WC_POS_Admin_Setup_Wizard();
