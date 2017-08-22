<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * About admin page
 */

add_action( 'admin_menu', 'us_add_info_home_page', 50 );
function us_add_info_home_page() {
	add_submenu_page( 'us-theme-options', US_THEMENAME . ': Home', us_translate( 'About' ), 'manage_options', 'us-home', 'us_welcome_page', 11 );
}

function us_welcome_page() {

	// Predefined URLs
	$help_portal = 'https://help.us-themes.com';
	$help_portal_api_url = 'https://help.us-themes.com/envato_auth';
	
	$urlparts = parse_url( site_url() );
	$domain = $urlparts['host'];
	$return_url = admin_url( 'admin.php?page=us-home' );

	if ( ! empty( $_GET['activation_action'] ) ) {
		if ( $_GET['activation_action'] == 'activate' AND ! empty( $_GET['secret'] ) ) {
			$url = $help_portal_api_url . '?secret=' . $_GET['secret'] . '&domain=' . $domain;

			$response = us_api_remote_request( $url );

			if ( $response == '1' ) {
				update_option( 'us_license_activated', 1 );
				update_option( 'us_license_secret', $_GET['secret'] );
				delete_transient( 'us_update_addons_data_' . US_THEMENAME );
			}

		}
	} elseif ( get_option( 'us_license_activated', 0 ) == 1 ) {
		$url = $help_portal_api_url . '?secret=' . get_option( 'us_license_secret' ) . '&domain=' . $domain;

		$response = wp_remote_get( $url );

		if ( ! is_wp_error( $response ) ) {
			if ( $response['body'] != '1' ) {
				update_option( 'us_license_activated', 0 );
				update_option( 'us_license_secret', '' );
				delete_transient( 'us_update_addons_data_' . US_THEMENAME );
			}
		}

	}

	?>
	<div class="wrap about-wrap us-home">

		<div class="us-header">
			<h1><?php echo sprintf( __( 'Welcome to <strong>%s</strong>', 'us' ), US_THEMENAME . ' ' . US_THEMEVERSION ) ?></h1>

			<div class="us-header-links">
				<div class="us-header-link">
					<a href="<?php echo $help_portal ?>/<?php echo ( defined( 'US_ACTIVATION_THEMENAME' ) ) ? strtolower( US_ACTIVATION_THEMENAME ) : strtolower( US_THEMENAME ); ?>/" target="_blank"><?php _e( 'Online Documentation', 'us' ) ?></a>
				</div>
				<div class="us-header-link">
					<a href="<?php echo $help_portal ?>/<?php echo ( defined( 'US_ACTIVATION_THEMENAME' ) ) ? strtolower( US_ACTIVATION_THEMENAME ) : strtolower( US_THEMENAME ); ?>/tickets/" target="_blank"><?php _e( 'Support Portal', 'us' ) ?></a>
				</div>
				<div class="us-header-link">
					<a href="<?php echo $help_portal ?>/<?php echo ( defined( 'US_ACTIVATION_THEMENAME' ) ) ? strtolower( US_ACTIVATION_THEMENAME ) : strtolower( US_THEMENAME ); ?>/changelog/" target="_blank"><?php _e( 'Theme Changelog', 'us' ) ?></a>
				</div>
			</div>
		</div>

		<div class="us-features">
			<div class="one-third">
				<h4><i class="dashicons dashicons-screenoptions"></i><?php _e( 'Install Addons', 'us' ) ?></h4>

				<p><?php echo sprintf( __( '%s comes with popular plugins which increase theme abilities, install them via one click.', 'us' ), US_THEMENAME ); ?></p>
				<a class="button us-button" href="<?php echo admin_url( 'admin.php?page=us-addons' ); ?>"><?php _e( 'Go to Addons page', 'us' ) ?></a>
			</div>
			<div class="one-third">
				<h4><i class="dashicons dashicons-download"></i><?php _e( 'Import Demo Content', 'us' ) ?></h4>

				<p><?php _e( 'Installed this theme for the first time? Import demo content to build your site not from scratch.', 'us' ) ?></p>
				<a class="button us-button" href="<?php echo admin_url( 'admin.php?page=us-demo-import' ); ?>">
					<?php _e( 'Go to Demo Import', 'us' ) ?></a>
			</div>
			<div class="one-third">
				<h4><i class="dashicons dashicons-admin-appearance"></i><?php _e( 'Customize Appearance', 'us' ) ?></h4>

				<p><?php _e( 'To customize the look and feel of your site (colors, layouts, fonts) go to the Theme Options panel.', 'us' ) ?></p>
				<a class="button us-button" href="<?php echo admin_url( 'admin.php?page=us-theme-options' ); ?>"><?php _e( 'Go to Theme Options', 'us' ) ?></a>
			</div>
		</div>

		<?php if ( get_option( 'us_license_activated', 0 ) == 1 ) { ?>
			<div class="us-activation">
				<div class="us-activation-status yes"><?php echo sprintf( __( '%s is activated', 'us' ), US_THEMENAME ); ?></div>
				<p><?php echo sprintf( __( 'You can deactivate it on your %sLicenses%s page.', 'us' ), '<a href="' . $help_portal . '/user/licenses/" target="_blank">', '</a>' ); ?></p>
			</div>

			<?php
		} else {
			$host_is_dev = FALSE;

			$host = $_SERVER['HTTP_HOST'];

			$chunks = explode( '.', $host );

			if ( ( 1 === count( $chunks ) ) OR ( in_array(
					end( $chunks ), array(
						'local',
						'dev',
						'wp',
						'test',
						'example',
						'localhost',
						'invalid',
					)
				) ) OR preg_match( '/^[0-9\.]+$/', $host )
			) {
				$host_is_dev = TRUE;
			}

			if ( $host_is_dev ) {
				?>
				<div class="updated hidden"><p>You are working on localhost development environment</p></div>
				<?php
			}

			$config = us_config( 'envato', array( 'purchase_url' => '#' ) );
			$purchase_url = $config['purchase_url'];

			?>

			<form class="us-activation" id="activation" method="post" action="<?php echo $help_portal_api_url; ?>">
				<input type="hidden" name="domain" value="<?php echo $domain; ?>">
				<input type="hidden" name="return_url" value="<?php echo $return_url; ?>">
				<input type="hidden" name="theme" value="<?php echo ( defined( 'US_ACTIVATION_THEMENAME' ) ) ? US_ACTIVATION_THEMENAME : US_THEMENAME; ?>">

				<div class="us-activation-status no">
					<span><?php echo sprintf( __( '%s is not activated', 'us' ), US_THEMENAME ); ?></span>

					<div class="us-activation-desc">
						<div class="us-activation-desc-sign"></div>
						<div class="us-activation-desc-text">
							<p><?php _e( 'By activating theme license you will unlock premium options:', 'us' ) ?></p>
							<ul>
								<li><?php _e( 'Access to official support portal', 'us' ) ?></li>
								<li><?php _e( 'Access to premium addons for free', 'us' ) ?></li>
								<li><?php _e( 'Ability to import any of theme demos', 'us' ) ?></li>
								<li><?php _e( 'Direct theme and addons updates from our server', 'us' ) ?></li>
							</ul>
							<p><?php _e( 'Don\'t have valid license yet?', 'us' ) ?> <a target="_blank" href="<?php echo $purchase_url; ?>"><?php echo sprintf( __( 'Purchase %s license', 'us' ), US_THEMENAME ); ?></a></p>
						</div>
					</div>
				</div>
				<input class="button button-primary" type="submit" value="<?php echo us_translate( 'Activate' ) ?>" name="activate">
			</form>

		<?php } ?>

	</div>
	<?php
}
