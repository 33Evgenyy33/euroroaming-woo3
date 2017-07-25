<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

add_action( 'admin_init', 'usof_mega_menu_init' );
function usof_mega_menu_init() {
	global $pagenow, $usof_version;
	if ( $pagenow == 'nav-menus.php') {
		global $us_template_directory_uri;

		add_action('admin_footer', 'usof_mega_menu_admin_footer');

		wp_enqueue_style( 'usof-styles', $us_template_directory_uri . '/framework/vendor/usof/css/usof.css', array(), $usof_version );
		add_thickbox();
		wp_enqueue_script( 'scriptaculous-dragdrop' );
	}
}

function usof_mega_menu_admin_footer() {
	?>
	<script type="text/javascript">
		jQuery(function ($) {
			"use strict";
			var menuId = $('input#menu').val();

			$('#menu-to-edit li.menu-item.menu-item-depth-0').each(function() {
				var $menuItem = $(this),
					itemId = parseInt($menuItem.attr('id').match(/[0-9]+/)[0], 10),
					$nextMenuItem = $menuItem.next();

				if ( $nextMenuItem.length == 0 || $nextMenuItem.is('li.menu-item.menu-item-depth-0') ) {
					return;
				}

				var $button = $('<a href="<?php echo admin_url(); ?>admin-ajax.php?action=usof_ajax_mega_menu&menu_id='+menuId+'&item_id='+itemId+'">')
					.addClass("us-mm-btn thickbox")
					.html('<?php _e( 'Dropdown Settings', 'us' ); ?>');

				$('.item-title', $menuItem).append($button);
			});
		});
	</script>
	<?php
}
