<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Template header
 */

$us_layout = US_Layout::instance();
?>
<!DOCTYPE HTML>
<html class="<?php echo $us_layout->html_classes() ?>" <?php language_attributes( 'html' ) ?>>
<head>
	<meta charset="UTF-8">

	<?php wp_head() ?>

	<?php if ( ( defined( 'US_DEV' ) AND US_DEV ) OR us_get_option( 'optimize_assets', 0 ) == 0 ): ?>
		<style id='us-theme-options-css' type="text/css"><?php echo us_get_theme_options_css() ?></style>
	<?php endif; ?>
	<?php if ( $us_layout->header_show != 'never' ): ?>
		<style id='us-header-css' type="text/css"><?php echo us_minify_css( us_get_template( 'config/header.css' ) ) ?></style>
	<?php endif; ?>
	<?php if ( us_get_option( 'optimize_assets', 0 ) == 0 AND ( $us_custom_css = us_get_option( 'custom_css', '' ) ) != '' ): ?>
		<style id='us-custom-css' type="text/css"><?php echo us_minify_css( $us_custom_css ) ?></style>
	<?php endif; ?>

</head>
<body <?php body_class( 'l-body ' . $us_layout->body_classes() );
if ( us_get_option( 'schema_markup' ) ) {
	echo ' itemscope itemtype="https://schema.org/WebPage"';
} ?>>
<?php
global $us_iframe;
if ( ! ( isset( $us_iframe ) AND $us_iframe ) AND us_get_option( 'preloader' ) != 'disabled' ) {
	add_action( 'us_before_canvas', 'us_display_preloader', 100 );
	function us_display_preloader() {
		$preloader_type = us_get_option( 'preloader' );
		if ( ! in_array( $preloader_type, array_merge( us_get_preloader_numeric_types(), array( 'custom' ) ) ) ) {
			$preloader_type = 1;
		}

		$preloader_image = us_get_option( 'preloader_image' );
		$preloader_image_html = '';
		$img = usof_get_image_src( $preloader_image, 'medium' );
		if ( $preloader_type == 'custom' AND $img[0] != '' ) {
			$preloader_image_html .= '<img src="' . esc_url( $img[0] ) . '"';
			if ( ! empty( $img[1] ) AND ! empty( $img[2] ) ) {
				// Image sizes may be missing when logo is a direct URL
				$preloader_image_html .= ' width="' . $img[1] . '" height="' . $img[2] . '"';
			}
			$preloader_image_html .= ' alt="" />';
		}

		?>
		<div class="l-preloader"><div class="l-preloader-spinner">
			<div class="g-preloader type_<?php echo $preloader_type ?>"><div><?php echo $preloader_image_html ?></div></div>
		</div></div>
		<?php
	}
}

do_action( 'us_before_canvas' ) ?>

<div class="l-canvas <?php echo $us_layout->canvas_classes() ?>">

	<?php if ( $us_layout->header_show != 'never' ): ?>

		<?php do_action( 'us_before_header' ) ?>

		<?php us_load_template( 'templates/l-header' ) ?>

		<?php do_action( 'us_after_header' ) ?>

	<?php endif/*( $us_layout->header_show != 'never' )*/
	; ?>
