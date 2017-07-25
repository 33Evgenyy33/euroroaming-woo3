<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Index template (used for front page blog listing)
 */

$us_layout = US_Layout::instance();
get_header();

$template_vars = array(
	'layout' => us_get_option( 'blog_layout', 'classic' ),
	'type' => us_get_option( 'blog_type', 'grid' ),
	'columns' => us_get_option( 'blog_cols', 1 ),
	'img_size' => us_get_option( 'blog_img_size', 'default' ),
	'metas' => us_get_option( 'blog_meta', array() ),
	'content_type' => us_get_option( 'blog_content_type', 'excerpt' ),
	'show_read_more' => in_array( 'read_more', us_get_option( 'blog_meta', array() ) ),
	'pagination' => us_get_option( 'blog_pagination', 'regular' ),
);

$default_blog_sidebar_id = us_get_option( 'blog_sidebar_id', 'default_sidebar' );

?>
	<div class="l-main">
		<div class="l-main-h i-cf">

			<main class="l-content" itemprop="mainContentOfPage">
				<section class="l-section">
					<div class="l-section-h i-cf">

						<?php do_action( 'us_before_index' ) ?>

						<?php us_load_template( 'templates/blog/listing', $template_vars ) ?>

						<?php do_action( 'us_after_index' ) ?>

					</div>
				</section>
			</main>

			<?php if ( $us_layout->sidebar_pos == 'left' OR $us_layout->sidebar_pos == 'right' ): ?>
				<aside class="l-sidebar at_<?php echo $us_layout->sidebar_pos . ' ' . us_dynamic_sidebar_id( $default_blog_sidebar_id ); ?>" itemscope="itemscope" itemtype="https://schema.org/WPSideBar">
					<?php us_dynamic_sidebar( $default_blog_sidebar_id ); ?>
				</aside>
			<?php endif; ?>

		</div>
	</div>
<?php

get_footer();
