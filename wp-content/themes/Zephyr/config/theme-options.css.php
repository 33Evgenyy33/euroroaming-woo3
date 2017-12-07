<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Generates and outputs theme options' generated styleshets
 *
 * @action Before the template: us_before_template:config/theme-options.css
 * @action After the template: us_after_template:config/theme-options.css
 */

global $us_template_directory_uri;
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

$prefixes = array( 'heading', 'body', 'menu' );
$font_families = array();
$default_font_weights = array_fill_keys( $prefixes, 400 );
foreach ( $prefixes as $prefix ) {
	$font = explode( '|', us_get_option( $prefix . '_font_family', 'none' ), 2 );
	if ( $font[0] == 'none' ) {
		// Use the default font
		$font_families[ $prefix ] = '';
	} elseif ( strpos( $font[0], ',' ) === FALSE ) {
		// Use some specific font from Google Fonts
		if ( ! isset( $font[1] ) OR empty( $font[1] ) ) {
			// Fault tolerance for missing font-variants
			$font[1] = '400,700';
		}
		// The first active font-weight will be used for "normal" weight
		$default_font_weights[ $prefix ] = intval( $font[1] );
		$fallback_font_family = us_config( 'google-fonts.' . $font[0] . '.fallback', 'sans-serif' );
		$font_families[ $prefix ] = 'font-family: "' . $font[0] . '", ' . $fallback_font_family . ";\n";
	} else {
		// Web-safe font combination
		$font_families[ $prefix ] = 'font-family: ' . $font[0] . ";\n";
	}
}

?>

/* CSS paths need to be absolute
   =============================================================================================================================== */
@font-face {
	font-family: 'FontAwesome';
	src: url('<?php echo $us_template_directory_uri ?>/framework/fonts/fontawesome-webfont.woff2?v=4.7.0') format('woff2'),
	url('<?php echo $us_template_directory_uri ?>/framework/fonts/fontawesome-webfont.woff?v=4.7.0') format('woff');
	font-weight: normal;
	font-style: normal;
	}
.style_phone6-1 > div {
	background-image: url(<?php echo $us_template_directory_uri ?>/framework/img/phone-6-black-real.png);
	}
.style_phone6-2 > div {
	background-image: url(<?php echo $us_template_directory_uri ?>/framework/img/phone-6-white-real.png);
	}
.style_phone6-3 > div {
	background-image: url(<?php echo $us_template_directory_uri ?>/framework/img/phone-6-black-flat.png);
	}
.style_phone6-4 > div {
	background-image: url(<?php echo $us_template_directory_uri ?>/framework/img/phone-6-white-flat.png);
	}
<?php if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) { ?>
.wc-credit-card-form-card-number.visa {
	background-image: url(<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/icons/credit-cards/visa.svg);
	}
.wc-credit-card-form-card-number.mastercard {
	background-image: url(<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/icons/credit-cards/mastercard.svg);
	}
.wc-credit-card-form-card-number.discover {
	background-image: url(<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/icons/credit-cards/discover.svg);
	}
.wc-credit-card-form-card-number.amex {
	background-image: url(<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/icons/credit-cards/amex.svg);
	}
.wc-credit-card-form-card-number.maestro {
	background-image: url(<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/icons/credit-cards/maestro.svg);
	}
.wc-credit-card-form-card-number.jcb {
	background-image: url(<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/icons/credit-cards/jcb.svg);
	}
.wc-credit-card-form-card-number.dinersclub {
	background-image: url(<?php echo WP_PLUGIN_URL ?>/woocommerce/assets/images/icons/credit-cards/diners.svg);
	}
<?php } ?>

/* Typography
   ========================================================================== */
html,
.w-nav .widget {
	<?php echo $font_families['body'] ?>
	font-size: <?php echo us_get_option( 'body_fontsize' ) ?>px;
	line-height: <?php echo us_get_option( 'body_lineheight' ) ?>px;
	font-weight: <?php echo $default_font_weights['body'] ?>;
	}
	
.w-text.font_main_menu,
.w-nav-list.level_1 {
	<?php echo $font_families['menu'] ?>
	font-weight: <?php echo $default_font_weights['menu'] ?>;
	}

h1, h2, h3, h4, h5, h6,
.w-text.font_heading,
.w-blog-post.format-quote blockquote,
.w-counter-number,
.w-pricing-item-price,
.w-tabs-item-title,
.stats-block .stats-desc .stats-number {
	<?php echo $font_families['heading'] ?>
	font-weight: <?php echo $default_font_weights['heading'] ?>;
	}
h1 {
	font-size: <?php echo us_get_option( 'h1_fontsize' ) ?>px;
	font-weight: <?php echo us_get_option( 'h1_fontweight' ) ?>;
	letter-spacing: <?php echo us_get_option( 'h1_letterspacing' ) ?>em;
	<?php if ( is_array( us_get_option( 'h1_transform' ) ) AND in_array( 'italic', us_get_option( 'h1_transform' ) ) ): ?>
	font-style: italic;
	<?php endif; if ( is_array( us_get_option( 'h1_transform' ) ) AND in_array( 'uppercase', us_get_option( 'h1_transform' ) ) ): ?>
	text-transform: uppercase;
	<?php endif; ?>
	}
h2 {
	font-size: <?php echo us_get_option( 'h2_fontsize' ) ?>px;
	font-weight: <?php echo us_get_option( 'h2_fontweight' ) ?>;
	letter-spacing: <?php echo us_get_option( 'h2_letterspacing' ) ?>em;
	<?php if ( is_array( us_get_option( 'h2_transform' ) ) AND in_array( 'italic', us_get_option( 'h2_transform' ) ) ): ?>
	font-style: italic;
	<?php endif; if ( is_array( us_get_option( 'h2_transform' ) ) AND in_array( 'uppercase', us_get_option( 'h2_transform' ) ) ): ?>
	text-transform: uppercase;
	<?php endif; ?>
	}
h3 {
	font-size: <?php echo us_get_option( 'h3_fontsize' ) ?>px;
	font-weight: <?php echo us_get_option( 'h3_fontweight' ) ?>;
	letter-spacing: <?php echo us_get_option( 'h3_letterspacing' ) ?>em;
	<?php if ( is_array( us_get_option( 'h3_transform' ) ) AND in_array( 'italic', us_get_option( 'h3_transform' ) ) ): ?>
	font-style: italic;
	<?php endif; if ( is_array( us_get_option( 'h3_transform' ) ) AND in_array( 'uppercase', us_get_option( 'h3_transform' ) ) ): ?>
	text-transform: uppercase;
	<?php endif; ?>
	}
h4,
.widgettitle,
.comment-reply-title,
.woocommerce #reviews h2,
.woocommerce .related > h2,
.woocommerce .upsells > h2,
.woocommerce .cross-sells > h2 {
	font-size: <?php echo us_get_option( 'h4_fontsize' ) ?>px;
	font-weight: <?php echo us_get_option( 'h4_fontweight' ) ?>;
	letter-spacing: <?php echo us_get_option( 'h4_letterspacing' ) ?>em;
	<?php if ( is_array( us_get_option( 'h4_transform' ) ) AND in_array( 'italic', us_get_option( 'h4_transform' ) ) ): ?>
	font-style: italic;
	<?php endif; if ( is_array( us_get_option( 'h4_transform' ) ) AND in_array( 'uppercase', us_get_option( 'h4_transform' ) ) ): ?>
	text-transform: uppercase;
	<?php endif; ?>
	}
h5 {
	font-size: <?php echo us_get_option( 'h5_fontsize' ) ?>px;
	font-weight: <?php echo us_get_option( 'h5_fontweight' ) ?>;
	letter-spacing: <?php echo us_get_option( 'h5_letterspacing' ) ?>em;
	<?php if ( is_array( us_get_option( 'h5_transform' ) ) AND in_array( 'italic', us_get_option( 'h5_transform' ) ) ): ?>
	font-style: italic;
	<?php endif; if ( is_array( us_get_option( 'h5_transform' ) ) AND in_array( 'uppercase', us_get_option( 'h5_transform' ) ) ): ?>
	text-transform: uppercase;
	<?php endif; ?>
	}
h6 {
	font-size: <?php echo us_get_option( 'h6_fontsize' ) ?>px;
	font-weight: <?php echo us_get_option( 'h6_fontweight' ) ?>;
	letter-spacing: <?php echo us_get_option( 'h6_letterspacing' ) ?>em;
	<?php if ( is_array( us_get_option( 'h6_transform' ) ) AND in_array( 'italic', us_get_option( 'h6_transform' ) ) ): ?>
	font-style: italic;
	<?php endif; if ( is_array( us_get_option( 'h6_transform' ) ) AND in_array( 'uppercase', us_get_option( 'h6_transform' ) ) ): ?>
	text-transform: uppercase;
	<?php endif; ?>
	}
@media (max-width: 767px) {
html {
	font-size: <?php echo us_get_option( 'body_fontsize_mobile' ) ?>px;
	line-height: <?php echo us_get_option( 'body_lineheight_mobile' ) ?>px;
	}
h1 {
	font-size: <?php echo us_get_option( 'h1_fontsize_mobile' ) ?>px;
	}
h1.vc_custom_heading {
	font-size: <?php echo us_get_option( 'h1_fontsize_mobile' ) ?>px !important;
	}
h2 {
	font-size: <?php echo us_get_option( 'h2_fontsize_mobile' ) ?>px;
	}
h2.vc_custom_heading {
	font-size: <?php echo us_get_option( 'h2_fontsize_mobile' ) ?>px !important;
	}
h3 {
	font-size: <?php echo us_get_option( 'h3_fontsize_mobile' ) ?>px;
	}
h3.vc_custom_heading {
	font-size: <?php echo us_get_option( 'h3_fontsize_mobile' ) ?>px !important;
	}
h4,
.widgettitle,
.comment-reply-title,
.woocommerce #reviews h2,
.woocommerce .related > h2,
.woocommerce .upsells > h2,
.woocommerce .cross-sells > h2 {
	font-size: <?php echo us_get_option( 'h4_fontsize_mobile' ) ?>px;
	}
h4.vc_custom_heading {
	font-size: <?php echo us_get_option( 'h4_fontsize_mobile' ) ?>px !important;
	}
h5 {
	font-size: <?php echo us_get_option( 'h5_fontsize_mobile' ) ?>px;
	}
h5.vc_custom_heading {
	font-size: <?php echo us_get_option( 'h5_fontsize_mobile' ) ?>px !important;
	}
h6 {
	font-size: <?php echo us_get_option( 'h6_fontsize_mobile' ) ?>px;
	}
h6.vc_custom_heading {
	font-size: <?php echo us_get_option( 'h6_fontsize_mobile' ) ?>px !important;
	}
}

/* Layout
   =============================================================================================================================== */
<?php if ( us_get_option( 'body_bg_image' ) AND $body_bg_image = usof_get_image_src( us_get_option( 'body_bg_image' ) ) ): ?>
body {
	background-image: url(<?php echo $body_bg_image[0] ?>);
	background-attachment: <?php echo ( us_get_option( 'body_bg_image_attachment' ) ) ? 'scroll' : 'fixed'; ?>;
	background-position: <?php echo us_get_option( 'body_bg_image_position' ) ?>;
	background-repeat: <?php echo us_get_option( 'body_bg_image_repeat' ) ?>;
	background-size: <?php echo us_get_option( 'body_bg_image_size' ) ?>;
}
<?php endif; ?>
body,
.header_hor .l-header.pos_fixed {
	min-width: <?php echo us_get_option( 'site_canvas_width' ) ?>px;
	}
.l-canvas.type_boxed,
.l-canvas.type_boxed .l-subheader,
.l-canvas.type_boxed .l-section.type_sticky,
.l-canvas.type_boxed ~ .l-footer {
	max-width: <?php echo us_get_option( 'site_canvas_width' ) ?>px;
	}
.header_hor .l-subheader-h,
.l-titlebar-h,
.l-main-h,
.l-section-h,
.w-tabs-section-content-h,
.w-blog-post-body {
	max-width: <?php echo us_get_option( 'site_content_width' ) ?>px;
	}
	
/* Hide carousel arrows before they cut by screen edges */
@media (max-width: <?php echo us_get_option( 'site_content_width' ) + 150 ?>px) {
.l-section:not(.width_full) .owl-nav {
	display: none;
	}
}
@media (max-width: <?php echo us_get_option( 'site_content_width' ) + 200 ?>px) {
.l-section:not(.width_full) .w-blog .owl-nav {
	display: none;
	}
}

.l-sidebar {
	width: <?php echo us_get_option( 'sidebar_width' ) ?>%;
	}
.l-content {
	width: <?php echo us_get_option( 'content_width' ) ?>%;
	}
	
/* Columns Stacking Width */
@media (max-width: <?php echo us_get_option( 'columns_stacking_width' ) - 1 ?>px) {
.g-cols > div:not([class*=" vc_col-"]) {
	clear: both;
	float: none;
	width: 100%;
	margin: 0 0 2rem;
	}
.g-cols.type_boxes > div,
.g-cols > div:last-child,
.g-cols > div.has-fill {
	margin-bottom: 0;
	}
.vc_wp_custommenu.layout_hor,
.align_center_xs,
.align_center_xs .w-socials {
	text-align: center;
	}
}

/* Portfolio Responsive Behavior */
@media screen and (max-width: <?php echo us_get_option( 'portfolio_breakpoint_1_width' ) ?>px) {
<?php for ( $i = us_get_option( 'portfolio_breakpoint_1_cols' ); $i <= 6; $i++ ) {?>
.w-portfolio.cols_<?php echo $i; ?> .w-portfolio-item {
	width: <?php echo 100 / us_get_option( 'portfolio_breakpoint_1_cols' ) ?>%;
	}
<?php if ( us_get_option( 'portfolio_breakpoint_1_cols' ) != 1 ): ?>
.w-portfolio.cols_<?php echo $i; ?> .w-portfolio-item.size_2x1,
.w-portfolio.cols_<?php echo $i; ?> .w-portfolio-item.size_2x2 {
	width: <?php echo 200 / us_get_option( 'portfolio_breakpoint_1_cols' ) ?>%;
	}
<?php endif; ?>
<?php } ?>
}
@media screen and (max-width: <?php echo us_get_option( 'portfolio_breakpoint_2_width' ) ?>px) {
<?php for ( $i = us_get_option( 'portfolio_breakpoint_2_cols' ); $i <= 6; $i++ ) {?>
.w-portfolio.cols_<?php echo $i; ?> .w-portfolio-item {
	width: <?php echo 100 / us_get_option( 'portfolio_breakpoint_2_cols' ) ?>%;
	}
<?php if ( us_get_option( 'portfolio_breakpoint_2_cols' ) != 1 ): ?>
.w-portfolio.cols_<?php echo $i; ?> .w-portfolio-item.size_2x1,
.w-portfolio.cols_<?php echo $i; ?> .w-portfolio-item.size_2x2 {
	width: <?php echo 200 / us_get_option( 'portfolio_breakpoint_2_cols' ) ?>%;
	}
<?php endif; ?>
<?php } ?>
}
@media screen and (max-width: <?php echo us_get_option( 'portfolio_breakpoint_3_width' ) ?>px) {
<?php for ( $i = us_get_option( 'portfolio_breakpoint_3_cols' ); $i <= 6; $i++ ) {?>
.w-portfolio.cols_<?php echo $i; ?> .w-portfolio-item {
	width: <?php echo 100 / us_get_option( 'portfolio_breakpoint_3_cols' ) ?>%;
	}
<?php if ( us_get_option( 'portfolio_breakpoint_3_cols' ) != 1 ): ?>
.w-portfolio.cols_<?php echo $i; ?> .w-portfolio-item.size_2x1,
.w-portfolio.cols_<?php echo $i; ?> .w-portfolio-item.size_2x2 {
	width: <?php echo 200 / us_get_option( 'portfolio_breakpoint_3_cols' ) ?>%;
	}
<?php endif; ?>
<?php } ?>
}

/* Blog Responsive Behavior */
@media screen and (max-width: <?php echo us_get_option( 'blog_breakpoint_1_width' ) ?>px) {
<?php for ( $i = us_get_option( 'blog_breakpoint_1_cols' ); $i <= 6; $i++ ) {?>
.w-blog.cols_<?php echo $i ?> .w-blog-post {
	width: <?php echo 100 / us_get_option( 'blog_breakpoint_1_cols' ) ?>%;
	}
<?php } ?>
}
@media screen and (max-width: <?php echo us_get_option( 'blog_breakpoint_2_width' ) ?>px) {
<?php for ( $i = us_get_option( 'blog_breakpoint_2_cols' ); $i <= 6; $i++ ) {?>
.w-blog.cols_<?php echo $i ?> .w-blog-post {
	width: <?php echo 100 / us_get_option( 'blog_breakpoint_2_cols' ) ?>%;
	}
<?php } ?>
}
@media screen and (max-width: <?php echo us_get_option( 'blog_breakpoint_3_width' ) ?>px) {
<?php for ( $i = us_get_option( 'blog_breakpoint_3_cols' ); $i <= 6; $i++ ) {?>
.w-blog.cols_<?php echo $i ?> .w-blog-post {
	width: <?php echo 100 / us_get_option( 'blog_breakpoint_3_cols' ) ?>%;
	}
<?php } ?>
}

/* Back to top Button */
.w-header-show,
.w-toplink {
	background-color: <?php echo us_get_option( 'back_to_top_color' ) ?>;
	}

/* Color Styles
   ========================================================================== */

body {
	background-color: <?php echo us_get_option( 'color_body_bg' ) ?>;
	-webkit-tap-highlight-color: <?php echo us_hex2rgba( us_get_option( 'color_content_primary' ), 0.2 ) ?>;
	}

/*************************** HEADER ***************************/

/* Top Header Colors */
.l-subheader.at_top,
.l-subheader.at_top .w-dropdown-list,
.l-subheader.at_top .type_mobile .w-nav-list.level_1 {
	background-color: <?php echo us_get_option( 'color_header_top_bg' ) ?>;
	}
.l-subheader.at_top,
.l-subheader.at_top .w-dropdown.active,
.l-subheader.at_top .type_mobile .w-nav-list.level_1 {
	color: <?php echo us_get_option( 'color_header_top_text' ) ?>;
	}
.no-touch .l-subheader.at_top a:hover,
.no-touch .l-header.bg_transparent .l-subheader.at_top .w-dropdown.active a:hover {
	color: <?php echo us_get_option( 'color_header_top_text_hover' ) ?>;
	}

/* Middle Header Colors */
.header_ver .l-header,
.header_hor .l-subheader.at_middle,
.l-subheader.at_middle .w-dropdown-list,
.l-subheader.at_middle .type_mobile .w-nav-list.level_1 {
	background-color: <?php echo us_get_option( 'color_header_middle_bg' ) ?>;
	}
.l-subheader.at_middle,
.l-subheader.at_middle .w-dropdown.active,
.l-subheader.at_middle .type_mobile .w-nav-list.level_1 {
	color: <?php echo us_get_option( 'color_header_middle_text' ) ?>;
	}
.no-touch .l-subheader.at_middle a:hover,
.no-touch .l-header.bg_transparent .l-subheader.at_middle .w-dropdown.active a:hover {
	color: <?php echo us_get_option( 'color_header_middle_text_hover' ) ?>;
	}

/* Bottom Header Colors */
.l-subheader.at_bottom,
.l-subheader.at_bottom .w-dropdown-list,
.l-subheader.at_bottom .type_mobile .w-nav-list.level_1 {
	background-color: <?php echo us_get_option( 'color_header_bottom_bg' ) ?>;
	}
.l-subheader.at_bottom,
.l-subheader.at_bottom .w-dropdown.active,
.l-subheader.at_bottom .type_mobile .w-nav-list.level_1 {
	color: <?php echo us_get_option( 'color_header_bottom_text' ) ?>;
	}
.no-touch .l-subheader.at_bottom a:hover,
.no-touch .l-header.bg_transparent .l-subheader.at_bottom .w-dropdown.active a:hover {
	color: <?php echo us_get_option( 'color_header_bottom_text_hover' ) ?>;
	}

/* Transparent Header Colors */
.l-header.bg_transparent:not(.sticky) .l-subheader {
	color: <?php echo us_get_option( 'color_header_transparent_text' ) ?>;
	}
.no-touch .l-header.bg_transparent:not(.sticky) a:not(.w-nav-anchor):hover,
.no-touch .l-header.bg_transparent:not(.sticky) .type_desktop .menu-item.level_1:hover > .w-nav-anchor {
	color: <?php echo us_get_option( 'color_header_transparent_text_hover' ) ?>;
	}
.l-header.bg_transparent:not(.sticky) .w-nav-title:after {
	background-color: <?php echo us_get_option( 'color_header_transparent_text_hover' ) ?>;
	}
	
/* Search Colors */
.w-search-form {
	background-color: <?php echo us_get_option( 'color_header_search_bg' ) ?>;
	color: <?php echo us_get_option( 'color_header_search_text' ) ?>;
	}
.w-search.layout_fullscreen .w-search-background {
	background-color: <?php echo us_get_option( 'color_header_search_bg' ) ?>;
	}
.w-search.layout_fullscreen input:focus + .w-form-row-field-bar:before,
.w-search.layout_fullscreen input:focus + .w-form-row-field-bar:after {
	background-color: <?php echo us_get_option( 'color_header_search_text' ) ?>;
	}

/*************************** HEADER MENU ***************************/

/* Menu Hover Colors */
.no-touch .menu-item.level_1:hover > .w-nav-anchor {
	background-color: <?php echo us_get_option( 'color_menu_hover_bg' ) ?>;
	color: <?php echo us_get_option( 'color_menu_hover_text' ) ?>;
	}
.w-nav-title:after {
	background-color: <?php echo us_get_option( 'color_menu_hover_text' ) ?>;
	}

/* Menu Active Colors */
.menu-item.level_1.current-menu-item > .w-nav-anchor,
.menu-item.level_1.current-menu-parent > .w-nav-anchor,
.menu-item.level_1.current-menu-ancestor > .w-nav-anchor {
	background-color: <?php echo us_get_option( 'color_menu_active_bg' ) ?>;
	color: <?php echo us_get_option( 'color_menu_active_text' ) ?>;
	}

/* Transparent Menu Active Text Color */
.l-header.bg_transparent:not(.sticky) .type_desktop .menu-item.level_1.current-menu-item > .w-nav-anchor,
.l-header.bg_transparent:not(.sticky) .type_desktop .menu-item.level_1.current-menu-ancestor > .w-nav-anchor {
	color: <?php echo us_get_option( 'color_menu_transparent_active_text' ) ?>;
	}

/* Dropdown Colors */
.w-nav-list:not(.level_1) {
	background-color: <?php echo us_get_option( 'color_drop_bg' ) ?>;
	color: <?php echo us_get_option( 'color_drop_text' ) ?>;
	}
.w-nav-anchor:not(.level_1) .ripple {
	background-color: <?php echo us_get_option( 'color_drop_text' ) ?>;
	}

/* Dropdown Hover Colors */
.no-touch .menu-item:not(.level_1):hover > .w-nav-anchor {
	background-color: <?php echo us_get_option( 'color_drop_hover_bg' ) ?>;
	color: <?php echo us_get_option( 'color_drop_hover_text' ) ?>;
	}

/* Dropdown Active Colors */
.menu-item:not(.level_1).current-menu-item > .w-nav-anchor,
.menu-item:not(.level_1).current-menu-parent > .w-nav-anchor,
.menu-item:not(.level_1).current-menu-ancestor > .w-nav-anchor {
	background-color: <?php echo us_get_option( 'color_drop_active_bg' ) ?>;
	color: <?php echo us_get_option( 'color_drop_active_text' ) ?>;
	}

/* Header Button */
.w-cart-quantity,
.btn.w-menu-item,
.btn.menu-item.level_1 > a,
.l-footer .vc_wp_custommenu.layout_hor .btn > a {
	background-color: <?php echo us_get_option( 'color_menu_button_bg' ) ?> !important;
	color: <?php echo us_get_option( 'color_menu_button_text' ) ?> !important;
	}
.no-touch .btn.w-menu-item:hover,
.no-touch .btn.menu-item.level_1 > a:hover,
.no-touch .l-footer .vc_wp_custommenu.layout_hor .btn > a:hover {
	background-color: <?php echo us_get_option( 'color_menu_button_hover_bg' ) ?> !important;
	color: <?php echo us_get_option( 'color_menu_button_hover_text' ) ?> !important;
	}

/*************************** MAIN CONTENT ***************************/

/* Background Color */
body.us_iframe,
.l-preloader,
.l-canvas,
.l-footer,
.l-popup-box-content,
.w-blog.layout_flat .w-blog-post-h,
.w-cart-dropdown,
.w-pricing.style_1 .w-pricing-item-h,
.w-person.layout_card,
.select2-dropdown,
.us-woo-shop_modern .product-h,
.no-touch .us-woo-shop_modern .product-meta,
.woocommerce #payment .payment_box,
.wpcf7-form-control-wrap.type_select:after {
	background-color: <?php echo us_get_option( 'color_content_bg' ) ?>;
	}
.woocommerce #payment .payment_methods li > input:checked + label,
.woocommerce .blockUI.blockOverlay {
	background-color: <?php echo us_get_option( 'color_content_bg' ) ?> !important;
	}
button.w-btn.color_contrast.style_raised,
a.w-btn.color_contrast.style_raised,
.w-iconbox.style_circle.color_contrast .w-iconbox-icon {
	color: <?php echo us_get_option( 'color_content_bg' ) ?>;
	}

/* Alternate Background Color */
.l-section.color_alternate,
.l-titlebar.color_alternate,
.l-section.for_blogpost .w-blog-post-preview,
.l-section.for_related > .l-section-h,
.l-canvas.sidebar_none .l-section.for_comments,
.w-actionbox.color_light,
.w-author,
.w-blog.layout_latest .w-blog-post-meta-date,
.no-touch .w-btn.style_flat:hover,
.no-touch .pagination a.page-numbers:hover,
.g-filters-item .ripple,
.w-form.for_protected,
.w-iconbox.style_circle.color_light .w-iconbox-icon,
.g-loadmore-btn,
.no-touch .owl-prev:hover,
.no-touch .owl-next:hover,
.w-profile,
.w-pricing.style_1 .w-pricing-item-header,
.w-pricing.style_2 .w-pricing-item-h,
.w-progbar-bar,
.w-progbar.style_3 .w-progbar-bar:before,
.w-progbar.style_3 .w-progbar-bar-count,
.l-main .w-socials-item-link,
.w-tabs-item .ripple,
.w-tabs.layout_timeline .w-tabs-item,
.w-tabs.layout_timeline .w-tabs-section-header-h,
.w-testimonials.style_1 .w-testimonial-h,
.widget_calendar #calendar_wrap,
.no-touch .l-main .widget_nav_menu a:hover,
.select2-selection__choice,
.woocommerce .login,
.woocommerce .track_order,
.woocommerce .checkout_coupon,
.woocommerce .lost_reset_password,
.woocommerce .register,
.no-touch .us-woo-shop_modern .product-h .button:hover,
.woocommerce .comment-respond,
.woocommerce .cart_totals,
.no-touch .woocommerce .product-remove a:hover,
.woocommerce .checkout #order_review,
.woocommerce ul.order_details,
.widget_shopping_cart,
.smile-icon-timeline-wrap .timeline-wrapper .timeline-block,
.smile-icon-timeline-wrap .timeline-feature-item.feat-item {
	background-color: <?php echo us_get_option( 'color_content_bg_alt' ) ?>;
	}
.timeline-wrapper .timeline-post-right .ult-timeline-arrow l,
.timeline-wrapper .timeline-post-left .ult-timeline-arrow l,
.timeline-feature-item.feat-item .ult-timeline-arrow l {
	border-color: <?php echo us_get_option( 'color_content_bg_alt' ) ?>;
	}

/* Border Color */
hr,
td,
th,
input,
textarea,
select,
.l-section,
.vc_column_container,
.vc_column-inner,
.w-form-row-field input:focus,
.w-form-row-field textarea:focus,
.widget_search input[type="text"]:focus,
.w-image,
.w-separator,
.w-sharing-item,
.w-tabs-list,
.w-tabs-section,
.w-tabs-section-header:before,
.l-main .widget_nav_menu .menu,
.l-main .widget_nav_menu .menu-item a,
.wpml-ls-legacy-dropdown a,
.wpml-ls-legacy-dropdown-click a,
.woocommerce .quantity.buttons_added input.qty,
.woocommerce .quantity.buttons_added .plus,
.woocommerce .quantity.buttons_added .minus,
.woocommerce-tabs .tabs,
.woocommerce .related,
.woocommerce .upsells,
.woocommerce .cross-sells,
.woocommerce ul.order_details li,
.select2-selection,
.smile-icon-timeline-wrap .timeline-line {
	border-color: <?php echo us_get_option( 'color_content_border' ) ?>;
	}
.w-iconbox.style_default.color_light .w-iconbox-icon,
.w-separator,
.pagination .page-numbers {
	color: <?php echo us_get_option( 'color_content_border' ) ?>;
	}
button.w-btn.color_light.style_raised,
a.w-btn.color_light.style_raised,
.no-touch .color_alternate .w-btn.style_flat:hover,
.no-touch .g-loadmore-btn:hover,
.color_alternate .g-filters-item .ripple,
.color_alternate .w-tabs-item .ripple,
.no-touch .color_alternate .owl-prev:hover,
.no-touch .color_alternate .owl-next:hover,
.no-touch .color_alternate .pagination a.page-numbers:hover,
.no-touch .woocommerce #payment .payment_methods li > label:hover,
.widget_price_filter .ui-slider:before {
	background-color: <?php echo us_get_option( 'color_content_border' ) ?>;
	}

/* Heading Color */
h1, h2, h3, h4, h5, h6,
.w-counter-number {
	color: <?php echo us_get_option( 'color_content_heading' ) ?>;
	}
.w-progbar.color_heading .w-progbar-bar-h {
	background-color: <?php echo us_get_option( 'color_content_heading' ) ?>;
	}

/* Text Color */
.l-canvas,
.l-footer,
.l-popup-box-content,
button.w-btn.color_light.style_raised,
a.w-btn.color_light.style_raised,
.w-blog.layout_flat .w-blog-post-h,
.w-cart-dropdown,
.w-iconbox.style_circle.color_light .w-iconbox-icon,
.w-pricing-item-h,
.w-person.layout_card,
.w-tabs.layout_timeline .w-tabs-item,
.w-tabs.layout_timeline .w-tabs-section-header-h,
.w-testimonials.style_1 .w-testimonial-h,
.woocommerce .form-row .chosen-drop,
.us-woo-shop_modern .product-h,
.select2-dropdown {
	color: <?php echo us_get_option( 'color_content_text' ) ?>;
	}
button.w-btn.color_contrast.style_raised,
a.w-btn.color_contrast.style_raised,
.w-iconbox.style_circle.color_contrast .w-iconbox-icon,
.w-progbar.color_text .w-progbar-bar-h,
.w-scroller-dot span {
	background-color: <?php echo us_get_option( 'color_content_text' ) ?>;
	}
.w-scroller-dot span {
	box-shadow: 0 0 0 2px <?php echo us_get_option( 'color_content_text' ) ?>;
	}
	
/* Link Color */
a {
	color: <?php echo us_get_option( 'color_content_link' ) ?>;
	}

/* Link Hover Color */
.no-touch a:hover,
.no-touch a:hover + .w-blog-post-body .w-blog-post-title a,
.no-touch .w-blog-post-title a:hover {
	color: <?php echo us_get_option( 'color_content_link_hover' ) ?>;
	}
.no-touch .w-cart-dropdown a:not(.button):hover {
	color: <?php echo us_get_option( 'color_content_link_hover' ) ?> !important;
	}

/* Primary Color */
.highlight_primary,
.g-preloader,
button.w-btn.color_primary.style_flat,
a.w-btn.color_primary.style_flat,
.w-counter.color_primary .w-counter-number,
.w-iconbox.style_default.color_primary .w-iconbox-icon,
.g-filters-item.active,
.w-form-row.focused:before,
.w-form-row.focused > i,
.no-touch .w-sharing.type_simple.color_primary .w-sharing-item:hover .w-sharing-icon,
.w-separator.color_primary,
.w-tabs-item.active,
.w-tabs-section.active .w-tabs-section-header,
.l-main .widget_nav_menu .menu-item.current-menu-item > a,
.no-touch .us-woo-shop_modern .product-h a.button,
.woocommerce-tabs .tabs li.active,
.woocommerce #payment .payment_methods li > input:checked + label,
input[type="radio"]:checked + .wpcf7-list-item-label:before,
input[type="checkbox"]:checked + .wpcf7-list-item-label:before {
	color: <?php echo us_get_option( 'color_content_primary' ) ?>;
	}
.l-section.color_primary,
.l-titlebar.color_primary,
.no-touch .l-navigation-item:hover .l-navigation-item-arrow,
.highlight_primary_bg,
.w-actionbox.color_primary,
.w-blog-post-preview-icon,
button,
input[type="submit"],
a.w-btn.color_primary.style_raised,
.pagination .page-numbers.current,
.w-form-row.focused .w-form-row-field-bar:before,
.w-form-row.focused .w-form-row-field-bar:after,
.w-iconbox.style_circle.color_primary .w-iconbox-icon,
.w-pricing.style_1 .type_featured .w-pricing-item-header,
.w-pricing.style_2 .type_featured .w-pricing-item-h,
.w-progbar.color_primary .w-progbar-bar-h,
.w-sharing.type_solid.color_primary .w-sharing-item,
.w-sharing.type_fixed.color_primary .w-sharing-item,
.w-socials-item-link-hover,
.w-tabs-list-bar,
.w-tabs.layout_timeline .w-tabs-item.active,
.no-touch .w-tabs.layout_timeline .w-tabs-item:hover,
.w-tabs.layout_timeline .w-tabs-section.active .w-tabs-section-header-h,
.rsDefault .rsThumb.rsNavSelected,
.woocommerce .button.alt,
.woocommerce .button.checkout,
.widget_price_filter .ui-slider-range,
.widget_price_filter .ui-slider-handle,
.select2-results__option--highlighted,
.smile-icon-timeline-wrap .timeline-separator-text .sep-text,
.smile-icon-timeline-wrap .timeline-wrapper .timeline-dot,
.smile-icon-timeline-wrap .timeline-feature-item .timeline-dot,
.l-body .cl-btn {
	background-color: <?php echo us_get_option( 'color_content_primary' ) ?>;
	}
.l-content blockquote,
.g-filters-item.active,
input:focus,
textarea:focus,
.w-separator.color_primary,
.owl-dot.active span,
.rsBullet.rsNavSelected span,
.woocommerce .quantity.buttons_added input.qty:focus,
.validate-required.woocommerce-validated input:focus,
.validate-required.woocommerce-invalid input:focus,
.us-woo-shop_modern .button.loading:before,
.us-woo-shop_modern .button.loading:after,
.woocommerce .form-row .chosen-search input[type="text"]:focus,
.woocommerce-tabs .tabs li.active {
	border-color: <?php echo us_get_option( 'color_content_primary' ) ?>;
	}
input:focus,
textarea:focus {
	box-shadow: 0 -1px 0 0 <?php echo us_get_option( 'color_content_primary' ) ?> inset;
	}

/* Secondary Color */
.highlight_secondary,
.no-touch .w-blognav-item:hover .w-blognav-title,
button.w-btn.color_secondary.style_flat,
a.w-btn.color_secondary.style_flat,
.w-counter.color_secondary .w-counter-number,
.w-iconbox.style_default.color_secondary .w-iconbox-icon,
.w-iconbox.style_default .w-iconbox-link:active .w-iconbox-icon,
.no-touch .w-iconbox.style_default .w-iconbox-link:hover .w-iconbox-icon,
.w-iconbox-link:active .w-iconbox-title,
.no-touch .w-iconbox-link:hover .w-iconbox-title,
.no-touch .w-sharing.type_simple.color_secondary .w-sharing-item:hover .w-sharing-icon,
.w-separator.color_secondary,
.no-touch .woocommerce .stars:hover a,
.no-touch .woocommerce .stars a:hover,
.woocommerce .star-rating span:before {
	color: <?php echo us_get_option( 'color_content_secondary' ) ?>;
	}
.l-section.color_secondary,
.l-titlebar.color_secondary,
.highlight_secondary_bg,
.no-touch .w-blog.layout_tiles .w-blog-post-meta-category a:hover,
.no-touch .l-section.preview_trendy .w-blog-post-meta-category a:hover,
button.w-btn.color_secondary.style_raised,
a.w-btn.color_secondary.style_raised,
.w-actionbox.color_secondary,
.w-iconbox.style_circle.color_secondary .w-iconbox-icon,
.w-progbar.color_secondary .w-progbar-bar-h,
.w-sharing.type_solid.color_secondary .w-sharing-item,
.w-sharing.type_fixed.color_secondary .w-sharing-item,
.no-touch .w-toplink.active:hover,
.no-touch .tp-leftarrow.tparrows.custom:hover,
.no-touch .tp-rightarrow.tparrows.custom:hover,
p.demo_store,
.woocommerce .onsale,
.woocommerce .form-row .chosen-results li.highlighted {
	background-color: <?php echo us_get_option( 'color_content_secondary' ) ?>;
	}
.w-separator.color_secondary {
	border-color: <?php echo us_get_option( 'color_content_secondary' ) ?>;
	}

/* Fade Elements Color */
.highlight_faded,
button.w-btn.color_light.style_flat,
a.w-btn.color_light.style_flat,
.l-main .w-author-url,
.l-main .w-blog-post-meta > *,
.l-main .w-profile-link.for_logout,
.l-main .w-socials.color_desaturated .w-socials-item-link,
.l-main .g-tags,
.l-main .w-testimonial-author-role,
.l-main .widget_tag_cloud,
.l-main .widget_product_tag_cloud {
	color: <?php echo us_get_option( 'color_content_faded' ) ?>;
	}
.w-btn.style_flat .ripple,
.w-btn.color_light.style_raised .ripple,
.w-iconbox.style_circle.color_light .ripple,
.l-main .w-socials.color_desaturated_inv .w-socials-item-link {
	background-color: <?php echo us_get_option( 'color_content_faded' ) ?>;
	}

/*************************** TOP FOOTER ***************************/

/* Background Color */
.color_footer-top,
.color_footer-top .wpcf7-form-control-wrap.type_select:after {
	background-color: <?php echo us_get_option( 'color_subfooter_bg' ) ?>;
	}

/* Alternate Background Color */
.color_footer-top .w-socials-item-link,
.color_footer-top .widget_shopping_cart {
	background-color: <?php echo us_get_option( 'color_subfooter_bg_alt' ) ?>;
	}

/* Border Color */
.color_footer-top,
.color_footer-top *,
.color_footer-top .w-form-row input:focus,
.color_footer-top .w-form-row textarea:focus {
	border-color: <?php echo us_get_option( 'color_subfooter_border' ) ?>;
	}
.color_footer-top .w-separator {
	color: <?php echo us_get_option( 'color_subfooter_border' ) ?>;
	}

/* Text Color */
.color_footer-top {
	color: <?php echo us_get_option( 'color_subfooter_text' ) ?>;
	}

/* Link Color */
.color_footer-top a {
	color: <?php echo us_get_option( 'color_subfooter_link' ) ?>;
	}

/* Link Hover Color */
.no-touch .color_footer-top a:hover,
.no-touch .color_footer-top a:hover + .w-blog-post-body .w-blog-post-title a,
.color_footer-top .w-form-row.focused:before,
.color_footer-top .w-form-row.focused > i {
	color: <?php echo us_get_option( 'color_subfooter_link_hover' ) ?>;
	}
.color_footer-top .w-form-row.focused .w-form-row-field-bar:before,
.color_footer-top .w-form-row.focused .w-form-row-field-bar:after {
	background-color: <?php echo us_get_option( 'color_subfooter_link_hover' ) ?>;
	}
.color_footer-top input:focus,
.color_footer-top textarea:focus {
	border-color: <?php echo us_get_option( 'color_subfooter_link_hover' ) ?>;
	box-shadow: 0 -1px 0 0 <?php echo us_get_option( 'color_subfooter_link_hover' ) ?> inset;
	}

/*************************** BOTTOM FOOTER ***************************/

/* Background Color */
.color_footer-bottom,
.color_footer-bottom .wpcf7-form-control-wrap.type_select:after {
	background-color: <?php echo us_get_option( 'color_footer_bg' ) ?>;
	}
	
/* Alternate Background Color */
.color_footer-bottom .w-socials-item-link,
.color_footer-bottom .widget_shopping_cart {
	background-color: <?php echo us_get_option( 'color_footer_bg_alt' ) ?>;
	}

/* Border Color */
.color_footer-bottom,
.color_footer-bottom,
.color_footer-bottom .w-form-row input:focus,
.color_footer-bottom .w-form-row textarea:focus {
	border-color: <?php echo us_get_option( 'color_footer_border' ) ?>;
	}
.color_footer-bottom .w-separator {
	color: <?php echo us_get_option( 'color_footer_border' ) ?>;
	}

/* Text Color */
.color_footer-bottom {
	color: <?php echo us_get_option( 'color_footer_text' ) ?>;
	}

/* Link Color */
.color_footer-bottom a {
	color: <?php echo us_get_option( 'color_footer_link' ) ?>;
	}

/* Link Hover Color */
.no-touch .color_footer-bottom a:hover,
.no-touch .color_footer-bottom a:hover + .w-blog-post-body .w-blog-post-title a,
.color_footer-bottom .w-form-row.focused:before,
.color_footer-bottom .w-form-row.focused > i {
	color: <?php echo us_get_option( 'color_footer_link_hover' ) ?>;
	}
.color_footer-bottom .w-form-row.focused .w-form-row-field-bar:before,
.color_footer-bottom .w-form-row.focused .w-form-row-field-bar:after {
	background-color: <?php echo us_get_option( 'color_footer_link_hover' ) ?>;
	}
.color_footer-bottom input:focus,
.color_footer-bottom textarea:focus {
	border-color: <?php echo us_get_option( 'color_footer_link_hover' ) ?>;
	box-shadow: 0 -1px 0 0 <?php echo us_get_option( 'color_footer_link_hover' ) ?> inset;
	}

/* Menu Dropdown Settings
   =============================================================================================================================== */
<?php
global $wpdb;
$wpdb_query = 'SELECT `id` FROM `' . $wpdb->posts . '` WHERE `post_type` = "nav_menu_item"';
$menu_items = array();
foreach ( $wpdb->get_results( $wpdb_query ) as $result ) {
	$menu_items[] = $result->id;
}
foreach ($menu_items as $menu_item_id):
	$settings = ( get_post_meta( $menu_item_id, 'us_mega_menu_settings', TRUE ) ) ? get_post_meta( $menu_item_id, 'us_mega_menu_settings', TRUE ) : array();
	if ( empty($settings) ) continue; ?>

<?php if ( $settings['columns'] != '1' AND $settings['width'] == 'full' ): ?>
.header_hor .w-nav.type_desktop .menu-item-<?php echo $menu_item_id; ?> {
	position: static;
}
.header_hor .w-nav.type_desktop .menu-item-<?php echo $menu_item_id; ?> .w-nav-list.level_2 {
	left: 0;
	right: 0;
	width: 100%;
	transform-origin: 50% 0;
}
.header_inpos_bottom .l-header.pos_fixed:not(.sticky) .w-nav.type_desktop .menu-item-<?php echo $menu_item_id; ?> .w-nav-list.level_2 {
	transform-origin: 50% 100%;
}
<?php endif; ?>

<?php if ( $settings['direction'] == 1 AND ( $settings['columns'] == '1' OR ( $settings['columns'] != '1' AND $settings['width'] == 'custom' ) ) ): ?>
.header_hor:not(.rtl) .w-nav.type_desktop .menu-item-<?php echo $menu_item_id; ?> .w-nav-list.level_2 {
	right: 0;
	transform-origin: 100% 0;
}
.header_hor.rtl .w-nav.type_desktop .menu-item-<?php echo $menu_item_id; ?> .w-nav-list.level_2 {
	left: 0;
	transform-origin: 0 0;
	}
<?php endif; ?>

.w-nav.type_desktop .menu-item-<?php echo $menu_item_id; ?> .w-nav-list.level_2 {
	padding: <?php echo $settings['padding']; ?>px;
	background-size: <?php echo $settings['bg_image_size']; ?>;
	background-repeat: <?php echo $settings['bg_image_repeat']; ?>;
	background-position: <?php echo $settings['bg_image_position']; ?>;

<?php if ( $settings['bg_image'] AND $bg_image = usof_get_image_src( $settings['bg_image'] ) ): ?>
	background-image: url(<?php echo $bg_image[0] ?>);
<?php endif;

if ( $settings['color_bg'] != '' ): ?>
	background-color: <?php echo $settings['color_bg']; ?>;
<?php endif;

if ( $settings['color_text'] != '' ): ?>
	color: <?php echo $settings['color_text']; ?>;
<?php endif;

if ( $settings['columns'] != '1' AND $settings['width'] == 'custom' ): ?>
	width: <?php echo $settings['custom_width']; ?>px;
<?php endif; ?>

}

<?php endforeach; ?>
