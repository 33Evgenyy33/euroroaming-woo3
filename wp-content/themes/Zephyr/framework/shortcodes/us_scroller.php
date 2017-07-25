<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_scroller
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $atts           array Shortcode attributes
 *
 * @param $atts           ['speed'] string Scroll Speed
 * @param $atts           ['dots'] bool Show navigation dots?
 * @param $atts           ['dots_pos'] string Dots Position
 * @param $atts           ['dots_size'] string Dots Size
 * @param $atts           ['dots_color'] string Dots color value
 * @param $atts           ['disable_width'] string Dots color value
 * @param $atts           ['el_class'] string Extra class name
 */

$atts = us_shortcode_atts( $atts, 'us_scroller' );

$classes = $dot_inner_css = '';

if ( $atts['dots_size'] != '' ) {
	$dot_inner_css = 'font-size:' . $atts['dots_size'] . ';';
}
if ( $atts['dots_color'] != '' ) {
	$dot_inner_css .= 'background-color:' . $atts['dots_color'] . ';box-shadow:0 0 0 2px ' . $atts['dots_color'] . ';';
}

$data_atts = '';
if ( $atts['speed'] != '' ) {
	$data_atts = ' data-speed="' . $atts['speed'] . '"';
}
if ( $atts['disable_width'] != '' ) {
	$data_atts .= ' data-disablewidth="' . intval( $atts['disable_width'] ) . '"';
}

$classes .= ' style_' . $atts['dots_style'] . ' pos_' . $atts['dots_pos'];

if ( ! empty( $atts['el_class'] ) ) {
	$classes .= ' ' . $atts['el_class'];
}

?>
<div class="w-scroller<?php echo $classes ?>"<?php echo $data_atts; ?> aria-hidden="true">
	<?php if ( $atts['dots'] ) { ?>
		<div class="w-scroller-dots">
			<a href="javascript:void(0);" class="w-scroller-dot"><span style="<?php echo $dot_inner_css ?>"></span></a>
		</div>
	<?php } ?>
</div>
