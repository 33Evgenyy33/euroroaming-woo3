<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Template footer
 */

$us_layout = US_Layout::instance();
?>
</div>

<?php
global $us_iframe, $us_hide_footer;
if ( ( ! isset( $us_iframe ) OR ! $us_iframe ) AND ( ! isset( $us_hide_footer ) OR ! $us_hide_footer ) ) {
	do_action( 'us_before_footer' );
?>
<footer class="l-footer"<?php echo ( us_get_option( 'schema_markup' ) ) ? ' itemscope itemtype="https://schema.org/WPFooter"' : ''; ?>>

	<?php
	$hide_footer = FALSE;

	// Default footer option
	$footer_id = us_get_option( 'footer_id', NULL );
	if ( is_singular( array( 'us_portfolio' ) ) ) {
		if ( us_get_option( 'footer_portfolio_defaults', 1 ) == 0 ) {
			$footer_id = us_get_option( 'footer_portfolio_id', NULL );
		}
	} elseif ( is_singular( array( 'post', 'attachment' ) ) ) {
		if ( us_get_option( 'footer_post_defaults', 1 ) == 0 ) {
			$footer_id = us_get_option( 'footer_post_id', NULL );
		}
	} elseif ( function_exists( 'is_woocommerce' ) AND is_woocommerce() ) {
		if ( is_singular() ) {
			if ( us_get_option( 'footer_product_defaults', 1 ) == 0 ) {
				$footer_id = us_get_option( 'footer_product_id', NULL );
			}
		} else {
			if ( us_get_option( 'footer_shop_defaults', 1 ) == 0 ) {
				$footer_id = us_get_option( 'footer_shop_id', NULL );
			}
			if ( ! is_search() AND ! is_tax() ) {
				if ( usof_meta( 'us_footer', array(), wc_get_page_id( 'shop' ) ) == 'hide' ) {
					$hide_footer = TRUE;
				}
				if ( usof_meta( 'us_footer', array(), wc_get_page_id( 'shop' ) ) == 'custom' ) {
					$footer_id = usof_meta( 'us_footer_id', array(), wc_get_page_id( 'shop' ) );
				}
			}
		}
	} elseif ( is_archive() OR is_search() ) {
		if ( us_get_option( 'footer_archive_defaults', 1 ) == 0 ) {
			$footer_id = us_get_option( 'footer_archive_id', NULL );
		}
	}

	$footer_content = '';
	if ( is_singular() OR ( is_404() AND $page_404 = get_page_by_path( 'error-404' ) ) ) {
		if ( is_singular() ) {
			$postID = get_the_ID();
		} elseif ( is_404() ) {
			$postID = $page_404->ID;
		}
		if ( usof_meta( 'us_footer', array(), $postID ) == 'hide' ) {
			$hide_footer = TRUE;
		}
		if ( usof_meta( 'us_footer', array(), $postID ) == 'custom' ) {
			$footer_id = usof_meta( 'us_footer_id', array(), $postID );
		}
	}

	if ( ! $hide_footer ) {
		$footer = FALSE;
		if ( ! empty( $footer_id ) ) {
			$footer = get_page_by_path( $footer_id, OBJECT, 'us_footer' );
		}
		if ( ! $footer ) {
			us_open_wp_query_context();
			$footer_templates_query = new WP_Query(
				array(
					'post_type' => 'us_footer',
					'posts_per_page' => '-1',
					'post_status' => 'any',
				)
			);
			if ( $footer_templates_query->have_posts() ) {
				$footer_templates_query->the_post();
				global $post;
				$footer_id = $post->post_name;
				$footer = get_page_by_path( $footer_id, OBJECT, 'us_footer' );
			}
			us_close_wp_query_context();
		}
		us_open_wp_query_context();
		if ( $footer ) {
			$translated_footer_id = apply_filters( 'wpml_object_id', $footer->ID, 'us_footer', TRUE );
			if ( $translated_footer_id != $footer->ID ) {
				$footer = get_post( $translated_footer_id );
			}
			global $wp_query, $vc_manager, $us_is_in_footer, $us_footer_id;
			$us_is_in_footer = TRUE;
			$us_footer_id = $translated_footer_id;
			$wp_query = new WP_Query( array(
				'p' => $translated_footer_id,
				'post_type' => 'any'
			) );
			if ( ! empty( $vc_manager ) AND is_object( $vc_manager )) {
				$vc_manager->vc()->addPageCustomCss( $translated_footer_id );
				$vc_manager->vc()->addShortcodesCustomCss( $translated_footer_id );
			}
			$footer_content = $footer->post_content;
		}
		us_close_wp_query_context();
		// Applying filters to footer content and echoing it ouside of us_open_wp_query_context so all WP widgets (like WP Nav Menu) would work as they should
		echo apply_filters( 'us_footer_the_content', $footer_content );

		$us_is_in_footer = FALSE;
	}
	?>

</footer>
<?php
	do_action( 'us_after_footer' );
}
if ( us_get_option( 'back_to_top', 1 ) ) {
	?>
	<a class="w-toplink pos_<?php echo us_get_option( 'back_to_top_pos', 'right' ) ?>" href="#" title="<?php _e( 'Back to top', 'us' ); ?>" aria-hidden="true"></a>
	<?php
}
if ( $us_layout->header_show != 'never' ) {
	?>
	<a class="w-header-show" href="javascript:void(0);"><span><?php echo us_translate( 'Menu' ) ?></span></a>
	<div class="w-header-overlay"></div>
	<?php
	}
?>
<script type="text/javascript">
	// Store some global theme options used in JS
	if (window.$us === undefined) window.$us = {};
	$us.canvasOptions = ($us.canvasOptions || {});
	$us.canvasOptions.disableEffectsWidth = <?php echo intval( us_get_option( 'disable_effects_width', 900 ) ) ?>;
	$us.canvasOptions.responsive = <?php echo us_get_option( 'responsive_layout', TRUE ) ? 'true' : 'false' ?>;
	$us.canvasOptions.backToTopDisplay = <?php echo intval( us_get_option( 'back_to_top_display', 100 ) ) ?>;

	$us.langOptions = ($us.langOptions || {});
	$us.langOptions.magnificPopup = ($us.langOptions.magnificPopup || {});
	$us.langOptions.magnificPopup.tPrev = '<?php _e( 'Previous (Left arrow key)', 'us' ); ?>';
	$us.langOptions.magnificPopup.tNext = '<?php _e( 'Next (Right arrow key)', 'us' ); ?>';
	$us.langOptions.magnificPopup.tCounter = '<?php _ex( '%curr% of %total%', 'Example: 3 of 12', 'us' ); ?>';

	$us.navOptions = ($us.navOptions || {});
	$us.navOptions.mobileWidth = <?php echo intval( us_get_option( 'menu_mobile_width', 900 ) ) ?>;
	$us.navOptions.togglable = <?php echo us_get_option( 'menu_togglable_type', TRUE ) ? 'true' : 'false' ?>;
	$us.ajaxLoadJs = <?php echo us_get_option( 'ajax_load_js', 0 ) ? 'true' : 'false' ?>;
	$us.templateDirectoryUri = '<?php global $us_template_directory_uri; echo $us_template_directory_uri; ?>';
</script>
<?php wp_footer(); ?>
</body>
</html>
