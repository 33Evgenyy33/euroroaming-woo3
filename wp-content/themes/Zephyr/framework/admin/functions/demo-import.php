<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Demo Import admin page
 */

$help_portal_url = 'https://help.us-themes.com/';
$help_portal_preview_url = $help_portal_url . 'uploads/demos/';
$help_portal_preview_url .= ( defined( 'US_ACTIVATION_THEMENAME' ) ) ? strtolower( US_ACTIVATION_THEMENAME ) . '/' : strtolower( US_THEMENAME ) . '/';
$help_portal_api_url = $help_portal_url . 'us.api/download_demo/';
$help_portal_api_url .= ( defined( 'US_ACTIVATION_THEMENAME' ) ) ? strtolower( US_ACTIVATION_THEMENAME ) : strtolower( US_THEMENAME );

add_action( 'admin_menu', 'us_add_demo_import_page', 30 );
function us_add_demo_import_page() {
	add_submenu_page( 'us-theme-options', __( 'Demo Import', 'us' ), __( 'Demo Import', 'us' ), 'manage_options', 'us-demo-import', 'us_demo_import' );
}

function us_demo_import() {

	global $help_portal_preview_url;
	$config = us_config( 'demo-import', array() );
	if ( count( $config ) < 1 ) {
		return;
	}
	reset( $config );

	if ( is_plugin_active( 'wordpress-importer/wordpress-importer.php' ) ) {
		deactivate_plugins( 'wordpress-importer/wordpress-importer.php' );
	}

	$update_notification = '';
	$update_themes = get_site_transient( 'update_themes' );
	if ( ! empty( $update_themes->response ) AND isset( $update_themes->response[US_THEMENAME] ) ) {
		$update_notification = sprintf( __( 'Some of demo data may be imported incorrectly, because you are using outdated Impreza version. %sUpdate the theme%s to import demos without possible issues.', 'us' ), '<a href="' . admin_url( 'themes.php' ) . '">', '</a>' );
	}
	?>

	<form class="w-importer" action="?page=us-demo-import" method="post">

		<h1 class="us-admin-title"><?php _e( 'Choose the demo for import', 'us' ) ?></h1>
		<p class="us-admin-subtitle"><?php _e( 'The images used in live demos will be replaced by placeholders due to copyright/license reasons.', 'us' ) ?></p>
		<p class="us-admin-subtitle"><strong><?php echo $update_notification; ?></strong></p>

		<div class="w-importer-list">

			<?php foreach ( $config as $name => $import ) {
				?>
				<div class="w-importer-item" data-demo-id="<?php echo $name; ?>">
					<input class="w-importer-item-radio" id="demo_<?php echo $name; ?>" type="radio" value="<?php echo $name; ?>" name="demo">
					<label class="w-importer-item-preview" for="demo_<?php echo $name; ?>" title="<?php _e( 'Click to choose', 'us' ) ?>">
						<h2 class="w-importer-item-title"><?php echo $import['title']; ?>
							<a class="btn" href="<?php echo $import['preview_url']; ?>" target="_blank" title="<?php _e( 'View this demo in a new tab', 'us' ) ?>"><?php echo us_translate( 'Preview' ) ?></a>
						</h2>
						<img src="<?php echo $help_portal_preview_url . $name . '/preview.jpg' ?>" alt="<?php echo $import['title']; ?>">
					</label>

					<div class="w-importer-item-options">
						<div class="w-importer-item-options-h">

							<label class="usof-checkbox content">
								<input type="checkbox" value="ON" name="content_all" checked="checked" class="parent_checkbox">
								<span class="usof-checkbox-icon"></span>
								<span class="usof-checkbox-text"><?php echo us_translate( 'All content' ) ?></span>
							</label>

							<?php if ( in_array( 'pages', $import['content'] ) ) { ?>
								<label class="usof-checkbox child">
									<input type="checkbox" value="ON" name="content_pages" checked class="child_checkbox">
									<span class="usof-checkbox-icon"></span>
									<span class="usof-checkbox-text"><?php echo us_translate( 'Pages' ) ?></span>
								</label>
							<?php } ?>

							<?php if ( in_array( 'posts', $import['content'] ) ) { ?>
								<label class="usof-checkbox child">
									<input type="checkbox" value="ON" name="content_posts" checked class="child_checkbox">
									<span class="usof-checkbox-icon"></span>
									<span class="usof-checkbox-text"><?php echo us_translate( 'Posts' ) ?></span>
								</label>
							<?php } ?>

							<?php if ( in_array( 'portfolio_items', $import['content'] ) ) { ?>
								<label class="usof-checkbox child">
									<input type="checkbox" value="ON" name="content_portfolio" <?php if ( us_get_option( 'enable_portfolio', 1 ) == 0 ) {
										echo ' disabled="disabled"';
									} else {
										echo 'checked="checked"';
									} ?> class="child_checkbox">
									<span class="usof-checkbox-icon"></span>
									<span class="usof-checkbox-text"><?php echo _e( 'Portfolio', 'us' ) ?></span>
									<?php if ( us_get_option( 'enable_portfolio', 1 ) == 0 ) { ?>
										<span class="usof-checkbox-note"> &mdash;
											<a href="<?php echo admin_url( 'admin.php?page=us-theme-options#advanced' )?>"><?php echo sprintf( __( 'Enable %s module', 'us' ), __( 'Portfolio', 'us' ) ) ?></a>
										</span>
									<?php } ?>
								</label>
							<?php } ?>

							<?php if ( in_array( 'testimonials', $import['content'] ) ) { ?>
								<label class="usof-checkbox child">
									<input type="checkbox" value="ON" name="content_testimonials"  <?php if ( us_get_option( 'enable_testimonials', 1 ) == 0 ) {
										echo ' disabled="disabled"';
									} else {
										echo 'checked="checked"';
									} ?> class="child_checkbox">
									<span class="usof-checkbox-icon"></span>
									<span class="usof-checkbox-text"><?php _e( 'Testimonials', 'us' ) ?></span>
									<?php if ( us_get_option( 'enable_testimonials', 1 ) == 0 ) { ?>
										<span class="usof-checkbox-note"> &mdash;
											<a href="<?php echo admin_url( 'admin.php?page=us-theme-options#advanced' )?>"><?php echo sprintf( __( 'Enable %s module', 'us' ), __( 'Testimonials', 'us' ) ) ?></a>
										</span>
									<?php } ?>
								</label>
							<?php } ?>

							<label class="usof-checkbox theme-options">
								<input type="checkbox" value="ON" name="theme_options" checked>
								<span class="usof-checkbox-icon"></span>
								<span class="usof-checkbox-text"><?php _e( 'Theme Options', 'us' ) ?></span>
							</label>

							<?php if ( in_array( 'widgets', $import['content'] ) ) { ?>
								<label class="usof-checkbox widgets">
									<input type="checkbox" value="ON" name="widgets" checked>
									<span class="usof-checkbox-icon"></span>
									<span class="usof-checkbox-text"><?php _e( 'Widgets & Sidebars', 'us' ) ?></span>
								</label>
							<?php } ?>

							<?php if ( isset( $import['sliders'] ) ) { ?>
								<label class="usof-checkbox rev-slider">
									<input type="checkbox" value="ON"
										   name="rev_slider"<?php if ( ! class_exists( 'RevSlider' ) ) {
										echo ' disabled="disabled"';
									} ?>>
									<span class="usof-checkbox-icon"></span>
									<span class="usof-checkbox-text"><?php _e( 'Revolution Sliders', 'us' ) ?></span>

									<?php if ( ! class_exists( 'RevSlider' ) ) { ?>
										<span class="usof-checkbox-note"> &mdash;
											<a href="<?php echo admin_url( 'admin.php?page=us-addons' ) ?>"><?php echo sprintf( us_translate( 'Install %s' ), 'Slider Revolution' ) ?></a>
											</span>
									<?php } ?>
								</label>
							<?php } ?>

							<?php if ( in_array( 'products', $import['content'] ) ) { ?>
								<label class="usof-checkbox woocommerce">
									<input type="checkbox" value="ON"
										   name="content_woocommerce"<?php if ( ! class_exists( 'woocommerce' ) ) {
										echo ' disabled="disabled"';
									} ?>>
									<span class="usof-checkbox-icon"></span>
									<span class="usof-checkbox-text"><?php _e( 'Shop Products', 'us' ) ?></span>
									<?php if ( ! class_exists( 'woocommerce' ) ) { ?>
										<span class="usof-checkbox-note"> &mdash;
											<a href="<?php echo admin_url( 'admin.php?page=us-addons' ) ?>"><?php echo sprintf( us_translate( 'Install %s' ), 'WooCommerce' ) ?></a>
											</span>
									<?php } ?>
								</label>
							<?php } ?>

						</div>

						<input type="hidden" name="action" value="perform_import">
						<input class="usof-button import_demo_data" type="submit" value="<?php echo us_translate( 'Import' ) ?>">

					</div>

					<div class="w-importer-message progress">
						<div class="g-preloader type_1"></div>
						<h2><?php _e( 'Importing Demo Content...', 'us' ) ?></h2>
						<p><?php _e( 'Don\'t close or refresh this page to not interrupt the import.', 'us' ) ?></p>
					</div>

					<div class="w-importer-message done">
						<h2><?php _e( 'Import completed', 'us' ) ?></h2>
						<p><?php echo sprintf( __( 'Just check the result on <a href="%s" target="_blank">your site</a> or start customize via <a href="%s">Theme Options</a>.', 'us' ), site_url(), admin_url( 'admin.php?page=us-theme-options' ) ) ?></p>
					</div>

				</div>
			<?php } ?>
		</div>

		<?php
		if ( ! ( get_option( 'us_license_activated', 0 ) OR ( defined( 'US_DEV' ) AND US_DEV ) ) ) {
			?><div class="us-screenlock"><div><?php echo sprintf( __( '<a href="%s">Activate the theme</a> to unlock Demo Import', 'us' ), admin_url( 'admin.php?page=us-home#activation' ) ) ?></div></div><?php
		}
		?>

	</form>
	<script type="text/javascript">
		jQuery(function($){
			var import_running = false;

			$('.w-importer-item-preview').click(function(){
				var $item = $(this).closest('.w-importer-item'),
					demoName = $item.attr('data-demo-id'),
					updateButtonState = function(){
						var $button = $item.find('.import_demo_data'),
							$checkboxes = $item.find('input[type=checkbox]'),
							isAnythingChecked = false;

						$checkboxes.each(function(){
							if ($(this).prop('checked')) {
								isAnythingChecked = true;
							}
						});

						if (isAnythingChecked) {
							$button.removeAttr('disabled');
						} else {
							$button.attr('disabled', 'disabled');
						}
					};

				$('.w-importer-item').removeClass('selected');
				$item.addClass('selected');

				$item.find('.usof-checkbox').off('click').click(function(){
					updateButtonState();
				});

				$item.find('.parent_checkbox').off('change').change(function(){
					$(this).removeClass('indeterminate');
					if ($(this).prop('checked')) {
						$item.find('.child_checkbox').not(':disabled').prop('checked', true);
					} else {
						$item.find('.child_checkbox').prop('checked', false);
					}

					updateButtonState();
				});

				$item.find('.child_checkbox').off('change').change(function(){
					var totalChild = 0,
						totalChildChecked = 0;
					$item.find('.child_checkbox').each(function(){
						if ($(this).is(':disabled')) {
							return;
						}
						totalChild++;
						if ($(this).prop('checked')) {
							totalChildChecked++;
						}
					});

					if (totalChildChecked == 0) {
						$item.find('.parent_checkbox').prop('checked', false);
						$item.find('.parent_checkbox').prop('indeterminate', false);
						$item.find('.parent_checkbox').removeClass('indeterminate');
					} else if (totalChildChecked == totalChild) {
						$item.find('.parent_checkbox').prop('checked', true);
						$item.find('.parent_checkbox').prop('indeterminate', false);
						$item.find('.parent_checkbox').removeClass('indeterminate');
					} else {
						$item.find('.parent_checkbox').prop('checked', false);
						$item.find('.parent_checkbox').prop('indeterminate', true);
						$item.find('.parent_checkbox').addClass('indeterminate');
					}
				});

				$item.find('.import_demo_data').off('click').click(function(){
					if (import_running) return false;

					var importQueue = [],
						processQueue = function(){
							if (importQueue.length != 0) {
								// Importing something
								var importAction = importQueue.shift();
								$.ajax({
									type: 'POST',
									url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
									data: {
										action: importAction,
										demo: demoName
									},
									success: function(data){
										if (data.success) {
											processQueue();
										} else {
											$('.w-importer-message.done h2').html(data.error_title);
											$('.w-importer-message.done p').html(data.error_description);
											$('.w-importer').addClass('error');
										}
									}
								});
							}
							else {
								// Import is completed
								$('.w-importer').addClass('success');
								import_running = false;
							}
						};

					if ($item.find('input[name=content_all]').prop('checked')) {
						importQueue.push('us_demo_import_content_all');
					} else {
						if ($item.find('input[name=content_pages]').prop('checked')) {
							importQueue.push('us_demo_import_content_pages');
						}
						if ($item.find('input[name=content_posts]').prop('checked')) {
							importQueue.push('us_demo_import_content_posts');
						}
						if ($item.find('input[name=content_portfolio]').prop('checked')) {
							importQueue.push('us_demo_import_content_portfolio');
						}
						if ($item.find('input[name=content_testimonials]').prop('checked')) {
							importQueue.push('us_demo_import_content_testimonials');
						}
					}
					if ($item.find('input[name=theme_options]').prop('checked')) importQueue.push('us_demo_import_options');
					if ($item.find('input[name=widgets]').prop('checked')) importQueue.push('us_demo_import_widgets');
					if ($item.find('input[name=content_woocommerce]').prop('checked')) importQueue.push('us_demo_import_woocommerce');
					if ($item.find('input[name=rev_slider]').prop('checked')) importQueue.push('us_demo_import_sliders');

					if (importQueue.length == 0) return false;

					import_running = true;
					$('.w-importer').addClass('importing');

					processQueue();

					return false;

				});

			});
		});
	</script>
	<?php
}

// Content Import

// All Content
add_action( 'wp_ajax_us_demo_import_content_all', 'us_demo_import_content_all' );
function us_demo_import_content_all() {
	global $help_portal_api_url;
	$config = us_config( 'demo-import', array() );

	//select which files to import
	$aviable_demos = array_keys( $config );
	$demo_version = $aviable_demos[0];
	if ( in_array( $_POST['demo'], $aviable_demos ) ) {
		$demo_version = $_POST['demo'];
	}

	$upload_dir = wp_upload_dir();
	$file_url = $help_portal_api_url . '?demo=' . $demo_version . '&file=all_content';
	$file_path = $upload_dir['basedir'] . '/all_content.xml';
	$file_copied = FALSE;

	if ( function_exists( 'curl_init' ) ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $file_url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );

		$contents = curl_exec( $ch );
		curl_close( $ch );

		if ( strlen( $contents ) > 50 ) {
			$fp = fopen( $file_path, 'w' );
			fwrite( $fp, $contents );
			$file_copied = TRUE;
		}
	} else {
		if ( copy( $file_url, $file_path ) AND filesize( $file_path ) > 50 ) {
			$file_copied = TRUE;
		}
	}

	if ( $file_copied ) {
		// Mega menu import filters and actions - START
		add_filter( 'wp_import_post_data_raw', 'us_demo_import_all_wp_import_post_data_raw' );
		function us_demo_import_all_wp_import_post_data_raw ( $post ) {
			global $us_demo_import_mega_menu_data;

			if ( $post['post_type'] != 'nav_menu_item' ) {
				return $post;
			}

			if ( isset( $post['postmeta'] ) AND is_array( $post['postmeta'] ) ) {
				foreach ( $post['postmeta'] as $postmeta ) {
					if ( is_array( $postmeta ) AND isset( $postmeta['key'] ) AND $postmeta['key'] == 'us_mega_menu_settings' AND ! empty( $postmeta['value'] ) ) {
						if ( ! isset( $us_demo_import_mega_menu_data ) OR ! is_array( $us_demo_import_mega_menu_data ) ) {
							$us_demo_import_mega_menu_data = array();
						}

						$us_demo_import_mega_menu_data[intval( $post['post_id'] )] = $postmeta['value'];
					}
				}

			}

			return $post;
		}
		
		add_action( 'import_end', us_demo_import_all_import_end );
		function us_demo_import_all_import_end () {
			global $wp_import, $us_demo_import_mega_menu_data;

			if ( is_array( $us_demo_import_mega_menu_data ) ) {
				foreach ( $us_demo_import_mega_menu_data as $menu_import_id => $mega_menu_data ) {
					if ( ! empty( $wp_import->processed_menu_items[$menu_import_id] ) ) {
						update_post_meta( intval( $wp_import->processed_menu_items[$menu_import_id] ), 'us_mega_menu_settings', maybe_unserialize( $mega_menu_data ) );
					}
				}
			}

		}
		// Mega menu import filters and actions - END

		us_demo_import_content( $file_path );
		unlink( $file_path );

		// Set menu
		if ( isset( $config[$demo_version]['nav_menu_locations'] ) ) {
			$locations = get_theme_mod( 'nav_menu_locations' );
			$menus = array();
			foreach ( wp_get_nav_menus() as $menu ) {
				if ( is_object( $menu ) ) {
					$menus[$menu->name] = $menu->term_id;
				}
			}
			foreach ( $config[$demo_version]['nav_menu_locations'] as $nav_location_key => $menu_name ) {
				if ( isset( $menus[$menu_name] ) ) {
					$locations[$nav_location_key] = $menus[$menu_name];
				}
			}

			set_theme_mod( 'nav_menu_locations', $locations );
		}

		// Set Front Page
		if ( isset( $config[$demo_version]['front_page'] ) ) {
			$front_page = get_page_by_title( $config[$demo_version]['front_page'] );

			if ( isset( $front_page->ID ) ) {
				update_option( 'show_on_front', 'page' );
				update_option( 'page_on_front', $front_page->ID );
			}
		}

		wp_send_json_success();

	} else {
		wp_send_json(
			array(
				'success' => FALSE,
				'error_title' => sprintf( __( 'Failed to import %s', 'us' ), __( 'Testimonials', 'us' ) ),
				'error_description' => __( 'Wrong path to the file or it is missing.', 'us' ),
			)
		);
	}

}

// Pages
add_action( 'wp_ajax_us_demo_import_content_pages', 'us_demo_import_content_pages' );

function us_demo_import_content_pages() {
	global $help_portal_api_url;
	$config = us_config( 'demo-import', array() );

	//select which files to import
	$aviable_demos = array_keys( $config );
	$demo_version = $aviable_demos[0];
	if ( in_array( $_POST['demo'], $aviable_demos ) ) {
		$demo_version = $_POST['demo'];
	}

	$upload_dir = wp_upload_dir();
	$file_url = $help_portal_api_url . '?demo=' . $demo_version . '&file=pages';
	$file_path = $upload_dir['basedir'] . '/pages.xml';
	$file_copied = FALSE;

	if ( function_exists( 'curl_init' ) ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $file_url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );

		$contents = curl_exec( $ch );
		curl_close( $ch );

		if ( strlen( $contents ) > 50 ) {
			$fp = fopen( $file_path, 'w' );
			fwrite( $fp, $contents );
			$file_copied = TRUE;
		}
	} else {
		if ( copy( $file_url, $file_path ) AND filesize( $file_path ) > 50 ) {
			$file_copied = TRUE;
		}
	}

	if ( $file_copied ) {
		us_demo_import_content( $file_path );
		unlink( $file_path );

		wp_send_json_success();
	} else {
		wp_send_json(
			array(
				'success' => FALSE,
				'error_title' => sprintf( __( 'Failed to import %s', 'us' ), us_translate( 'Pages' ) ),
				'error_description' => __( 'Wrong path to the file or it is missing.', 'us' ),
			)
		);
	}
}

// Posts
add_action( 'wp_ajax_us_demo_import_content_posts', 'us_demo_import_content_posts' );

function us_demo_import_content_posts() {
	global $help_portal_api_url;
	$config = us_config( 'demo-import', array() );

	//select which files to import
	$aviable_demos = array_keys( $config );
	$demo_version = $aviable_demos[0];
	if ( in_array( $_POST['demo'], $aviable_demos ) ) {
		$demo_version = $_POST['demo'];
	}

	$upload_dir = wp_upload_dir();
	$file_url = $help_portal_api_url . '?demo=' . $demo_version . '&file=posts';
	$file_path = $upload_dir['basedir'] . '/posts.xml';
	$file_copied = FALSE;

	if ( function_exists( 'curl_init' ) ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $file_url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );

		$contents = curl_exec( $ch );
		curl_close( $ch );

		if ( strlen( $contents ) > 50 ) {
			$fp = fopen( $file_path, 'w' );
			fwrite( $fp, $contents );
			$file_copied = TRUE;
		}
	} else {
		if ( copy( $file_url, $file_path ) AND filesize( $file_path ) > 50 ) {
			$file_copied = TRUE;
		}
	}

	if ( $file_copied ) {
		us_demo_import_content( $file_path );
		unlink( $file_path );

		wp_send_json_success();
	} else {
		wp_send_json(
			array(
				'success' => FALSE,
				'error_title' => sprintf( __( 'Failed to import %s', 'us' ), us_translate( 'Posts' ) ),
				'error_description' => __( 'Wrong path to the file or it is missing.', 'us' ),
			)
		);
	}
}

// Portfolio
add_action( 'wp_ajax_us_demo_import_content_portfolio', 'us_demo_import_content_portfolio' );

function us_demo_import_content_portfolio() {
	global $help_portal_api_url;
	$config = us_config( 'demo-import', array() );

	//select which files to import
	$aviable_demos = array_keys( $config );
	$demo_version = $aviable_demos[0];
	if ( in_array( $_POST['demo'], $aviable_demos ) ) {
		$demo_version = $_POST['demo'];
	}

	$upload_dir = wp_upload_dir();
	$file_url = $help_portal_api_url . '?demo=' . $demo_version . '&file=portfolio_items';
	$file_path = $upload_dir['basedir'] . '/portfolio_items.xml';
	$file_copied = FALSE;

	if ( function_exists( 'curl_init' ) ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $file_url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );

		$contents = curl_exec( $ch );
		curl_close( $ch );

		if ( strlen( $contents ) > 50 ) {
			$fp = fopen( $file_path, 'w' );
			fwrite( $fp, $contents );
			$file_copied = TRUE;
		}
	} else {
		if ( copy( $file_url, $file_path ) AND filesize( $file_path ) > 50 ) {
			$file_copied = TRUE;
		}
	}

	if ( $file_copied ) {
		us_demo_import_content( $file_path );
		unlink( $file_path );

		wp_send_json_success();
	} else {
		wp_send_json(
			array(
				'success' => FALSE,
				'error_title' => sprintf( __( 'Failed to import %s', 'us' ), __( 'Portfolio', 'us' ) ),
				'error_description' => __( 'Wrong path to the file or it is missing.', 'us' ),
			)
		);
	}
}

// Testimonials
add_action( 'wp_ajax_us_demo_import_content_testimonials', 'us_demo_import_content_testimonials' );

function us_demo_import_content_testimonials() {
	global $help_portal_api_url;
	$config = us_config( 'demo-import', array() );

	//select which files to import
	$aviable_demos = array_keys( $config );
	$demo_version = $aviable_demos[0];
	if ( in_array( $_POST['demo'], $aviable_demos ) ) {
		$demo_version = $_POST['demo'];
	}

	$upload_dir = wp_upload_dir();
	$file_url = $help_portal_api_url . '?demo=' . $demo_version . '&file=testimonials';
	$file_path = $upload_dir['basedir'] . '/testimonials.xml';
	$file_copied = FALSE;

	if ( function_exists( 'curl_init' ) ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $file_url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );

		$contents = curl_exec( $ch );
		curl_close( $ch );

		if ( strlen( $contents ) > 50 ) {
			$fp = fopen( $file_path, 'w' );
			fwrite( $fp, $contents );
			$file_copied = TRUE;
		}
	} else {
		if ( copy( $file_url, $file_path ) AND filesize( $file_path ) > 50 ) {
			$file_copied = TRUE;
		}
	}

	if ( $file_copied ) {
		us_demo_import_content( $file_path );
		unlink( $file_path );

		wp_send_json_success();
	} else {
		wp_send_json(
			array(
				'success' => FALSE,
				'error_title' => sprintf( __( 'Failed to import %s', 'us' ), __( 'Testimonials', 'us' ) ),
				'error_description' => __( 'Wrong path to the file or it is missing.', 'us' ),
			)
		);
	}

}

function us_demo_import_content( $file ) {
	global $us_template_directory, $wp_import;

	set_time_limit( 0 );

	if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
		define( 'WP_LOAD_IMPORTERS', TRUE );
	}

	if ( ! class_exists( 'WP_Import' ) ) {
		require_once( $us_template_directory . '/framework/vendor/wordpress-importer/wordpress-importer.php' );
	}


	$wp_import = new WP_Import();
	$wp_import->fetch_attachments = TRUE;

	ob_start();
	$wp_import->import( $file );
	ob_end_clean();
}

// Widgets Import
add_action( 'wp_ajax_us_demo_import_widgets', 'us_demo_import_widgets' );
function us_demo_import_widgets() {
	global $us_template_directory, $help_portal_api_url;
	$config = us_config( 'demo-import', array() );

	//select which files to import
	$aviable_demos = array_keys( $config );
	$demo_version = $aviable_demos[0];
	if ( in_array( $_POST['demo'], $aviable_demos ) ) {
		$demo_version = $_POST['demo'];
	}

	if ( isset( $config[$demo_version]['sidebars'] ) ) {
		$widget_areas = get_option( 'us_widget_areas' );
		if ( empty( $widget_areas ) ) {
			$widget_areas = array();
		}

		$args = array(
			'description' => __( 'Custom Sidebar', 'us' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>',
			'class' => 'us-custom-area',
		);

		foreach ( $config[$demo_version]['sidebars'] as $id => $name ) {
			if ( ! isset( $widget_areas[$id] ) ) {
				$args['name'] = $name;
				$args['id'] = $id;
				register_sidebar( $args );

				$widget_areas[$id] = $name;
			}

		}

		update_option( 'us_widget_areas', $widget_areas );
	}

	$upload_dir = wp_upload_dir();
	$file_url = $help_portal_api_url . '?demo=' . $demo_version . '&file=widgets';
	$file_path = $upload_dir['basedir'] . '/widgets.json';
	$file_copied = FALSE;

	if ( function_exists( 'curl_init' ) ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $file_url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );

		$contents = curl_exec( $ch );
		curl_close( $ch );

		if ( strlen( $contents ) > 50 ) {
			$fp = fopen( $file_path, 'w' );
			fwrite( $fp, $contents );
			$file_copied = TRUE;
		}
	} else {
		if ( copy( $file_url, $file_path ) AND filesize( $file_path ) > 50 ) {
			$file_copied = TRUE;
		}
	}

	if ( $file_copied ) {
		ob_start();
		require_once( $us_template_directory . '/framework/vendor/widget-importer-exporter/import.php' );
		us_wie_process_import_file( $file_path );
		ob_end_clean();
		unlink( $file_path );
	} else {
		wp_send_json(
			array(
				'success' => FALSE,
				'error_title' => sprintf( __( 'Failed to import %s', 'us' ), __( 'Widgets & Sidebars', 'us' ) ),
				'error_description' => __( 'Wrong path to the file or it is missing.', 'us' ),
			)
		);
	}

	wp_send_json_success();
}

// WooCommerce Import
add_action( 'wp_ajax_us_demo_import_woocommerce', 'us_demo_import_woocommerce' );
function us_demo_import_woocommerce() {
	global $us_template_directory, $help_portal_api_url;
	$config = us_config( 'demo-import', array() );

	set_time_limit( 0 );

	if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
		define( 'WP_LOAD_IMPORTERS', TRUE );
	}

	//select which files to import
	$aviable_demos = array_keys( $config );
	$demo_version = $aviable_demos[0];
	if ( in_array( $_POST['demo'], $aviable_demos ) ) {
		$demo_version = $_POST['demo'];
	}

	$upload_dir = wp_upload_dir();
	$file_url = $help_portal_api_url . '?demo=' . $demo_version . '&file=products';
	$file_path = $upload_dir['basedir'] . '/products.xml';
	$file_copied = FALSE;

	if ( function_exists( 'curl_init' ) ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $file_url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );

		$contents = curl_exec( $ch );
		curl_close( $ch );

		if ( strlen( $contents ) > 50 ) {
			$fp = fopen( $file_path, 'w' );
			fwrite( $fp, $contents );
			$file_copied = TRUE;
		}
	} else {
		if ( copy( $file_url, $file_path ) AND filesize( $file_path ) > 50 ) {
			$file_copied = TRUE;
		}
	}

	if ( $file_copied ) {
		if ( ! class_exists( 'WP_Import' ) ) {
			require_once( $us_template_directory . '/framework/vendor/wordpress-importer/wordpress-importer.php' );
		}

		$wp_import = new WP_Import();
		$wp_import->fetch_attachments = TRUE;

		// Creating attributes taxonomies
		global $wpdb;
		$parser = new WXR_Parser();
		$import_data = $parser->parse( $file_path );

		if ( isset( $import_data['posts'] ) ) {

			$posts = $import_data['posts'];

			if ( $posts && sizeof( $posts ) > 0 ) {
				foreach ( $posts as $post ) {
					if ( 'product' === $post['post_type'] ) {
						if ( ! empty( $post['terms'] ) ) {
							foreach ( $post['terms'] as $term ) {
								if ( strstr( $term['domain'], 'pa_' ) ) {
									if ( ! taxonomy_exists( $term['domain'] ) ) {
										$attribute_name = wc_sanitize_taxonomy_name( str_replace( 'pa_', '', $term['domain'] ) );

										// Create the taxonomy
										if ( ! in_array( $attribute_name, wc_get_attribute_taxonomies() ) ) {
											$attribute = array(
												'attribute_label' => $attribute_name,
												'attribute_name' => $attribute_name,
												'attribute_type' => 'select',
												'attribute_orderby' => 'menu_order',
												'attribute_public' => 0,
											);
											$wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute );
											delete_transient( 'wc_attribute_taxonomies' );
										}

										// Register the taxonomy now so that the import works!
										register_taxonomy(
											$term['domain'], apply_filters( 'woocommerce_taxonomy_objects_' . $term['domain'], array( 'product' ) ), apply_filters(
												'woocommerce_taxonomy_args_' . $term['domain'], array(
													'hierarchical' => TRUE,
													'show_ui' => FALSE,
													'query_var' => TRUE,
													'rewrite' => FALSE,
												)
											)
										);
									}
								}
							}
						}
					}
				}
			}
		}

		ob_start();
		$wp_import->import( $file_path );
		ob_end_clean();

		// Set WooCommerce Pages
		$shop_page = get_page_by_title( 'Shop' );
		if ( isset( $shop_page->ID ) ) {
			update_option( 'woocommerce_shop_page_id', $shop_page->ID );
		}
		$cart_page = get_page_by_title( 'Cart' );
		if ( isset( $cart_page->ID ) ) {
			update_option( 'woocommerce_cart_page_id', $cart_page->ID );
		}
		$checkout_page = get_page_by_title( 'Checkout' );
		if ( isset( $checkout_page->ID ) ) {
			update_option( 'woocommerce_checkout_page_id', $checkout_page->ID );
		}
		$my_account_page = get_page_by_title( 'My Account' );
		if ( isset( $my_account_page->ID ) ) {
			update_option( 'woocommerce_myaccount_page_id', $my_account_page->ID );
		}

		unlink( $file_path );

		wp_send_json_success();
	} else {
		wp_send_json(
			array(
				'success' => FALSE,
				'error_title' => sprintf( __( 'Failed to import %s', 'us' ), __( 'Shop Products', 'us' ) ),
				'error_description' => __( 'Wrong path to the file or it is missing.', 'us' ),
			)
		);
	}

}

//Import Options
add_action( 'wp_ajax_us_demo_import_options', 'us_demo_import_options' );
function us_demo_import_options() {
	global $us_template_directory, $help_portal_api_url;
	$config = us_config( 'demo-import', array() );

	//select which files to import
	$aviable_demos = array_keys( $config );
	$demo_version = $aviable_demos[0];
	if ( in_array( $_POST['demo'], $aviable_demos ) ) {
		$demo_version = $_POST['demo'];
	}

	$upload_dir = wp_upload_dir();
	$file_url = $help_portal_api_url . '?demo=' . $demo_version . '&file=theme_options';
	$file_path = $upload_dir['basedir'] . '/theme_options.json';
	$file_copied = FALSE;

	if ( function_exists( 'curl_init' ) ) {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $file_url );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );

		$contents = curl_exec( $ch );
		curl_close( $ch );

		if ( strlen( $contents ) > 50 ) {
			$fp = fopen( $file_path, 'w' );
			fwrite( $fp, $contents );
			$file_copied = TRUE;
		}
	} else {
		if ( copy( $file_url, $file_path ) AND filesize( $file_path ) > 50 ) {
			$file_copied = TRUE;
		}
	}

	if ( $file_copied ) {
		$updated_options = json_decode( file_get_contents( $file_path ), TRUE );

		if ( ! is_array( $updated_options ) ) {
			// Wrong file configuration
			wp_send_json(
				array(
					'success' => FALSE,
					'error_title' => sprintf( __( 'Failed to import %s', 'us' ), __( 'Theme Options', 'us' ) ),
					'error_description' => __( 'Wrong path to the file or it is missing.', 'us' ),
				)
			);
		}

		usof_save_options( $updated_options );
		unlink( $file_path );

		wp_send_json_success();
	} else {
		wp_send_json(
			array(
				'success' => FALSE,
				'error_title' => sprintf( __( 'Failed to import %s', 'us' ), __( 'Theme Options', 'us' ) ),
				'error_description' => __( 'Wrong path to the file or it is missing.', 'us' ),
			)
		);
	}

}

//Import Slider
add_action( 'wp_ajax_us_demo_import_sliders', 'us_demo_import_sliders' );
function us_demo_import_sliders() {
	global $help_portal_api_url;
	$config = us_config( 'demo-import', array() );

	//select which files to import
	$aviable_demos = array_keys( $config );
	$demo_version = $aviable_demos[0];
	if ( in_array( $_POST['demo'], $aviable_demos ) ) {
		$demo_version = $_POST['demo'];
	}

	$sliders = $config[$demo_version]['sliders'];

	if ( ! class_exists( 'RevSlider' ) OR ! ( count( $sliders ) > 0 ) ) {
		wp_send_json(
			array(
				'success' => FALSE,
				'error_title' => sprintf( __( 'Failed to import %s', 'us' ), __( 'Revolution Sliders', 'us' ) ),
				'error_description' => __( 'Wrong path to the file or it is missing.', 'us' ),
			)
		);
	}


	if ( count( $sliders ) > 0 ) {
		$upload_dir = wp_upload_dir();

		foreach ( $sliders as $slider_filename ) {
			$file_url = $help_portal_api_url . '?demo=' . $demo_version . '&file=' . $slider_filename;
			$file_path = $upload_dir['basedir'] . '/' . $slider_filename;
			$file_copied = FALSE;

			if ( function_exists( 'curl_init' ) ) {
				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_URL, $file_url );
				curl_setopt( $ch, CURLOPT_HEADER, 0 );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );

				$contents = curl_exec( $ch );
				curl_close( $ch );

				if ( strlen( $contents ) > 50 ) {
					$fp = fopen( $file_path, 'w' );
					fwrite( $fp, $contents );
					$file_copied = TRUE;
				}
			} else {
				if ( copy( $file_url, $file_path ) AND filesize( $file_path ) > 50 ) {
					$file_copied = TRUE;
				}
			}

			if ( $file_copied ) {
				ob_start();
				$_FILES["import_file"]["tmp_name"] = $file_path;
				$slider = new RevSlider();
				$response = $slider->importSliderFromPost();
				unset( $slider );
				unlink( $file_path );
				ob_end_clean();
			} else {
				wp_send_json(
					array(
						'success' => FALSE,
						'error_title' => sprintf( __( 'Failed to import %s', 'us' ), __( 'Revolution Sliders', 'us' ) ),
						'error_description' => __( 'Wrong path to the file or it is missing.', 'us' ),
					)
				);
			}

		}
	}
	
	wp_send_json_success();
}
