<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The template for displaying all single posts and attachments
 */

$us_layout = US_Layout::instance();
if ( us_get_option( 'forum_sidebar', 0 ) == 1 ) {
	$us_layout->sidebar_pos = us_get_option( 'forum_sidebar_pos', 'right' );
	$default_forum_sidebar_id = us_get_option( 'forum_sidebar_id', 'default_sidebar' );
} else {
	$us_layout->sidebar_pos = 'none';
	$default_forum_sidebar_id = NULL;
}

get_header();

us_load_template( 'templates/titlebar' );
?>
<div class="l-main">
	<div class="l-main-h i-cf">

		<main class="l-content"<?php echo ( us_get_option( 'schema_markup' ) ) ? ' itemprop="mainContentOfPage"' : ''; ?>>
			<section class="l-section for_forum">
				<div class="l-section-h i-cf">
					<?php do_action( 'us_before_single' ) ?>

					<?php
					while ( have_posts() ) {
						the_post();

						the_content();
					}
					?>

					<?php do_action( 'us_after_single' ) ?>
				</div>
			</section>
		</main>

		<?php if ( $us_layout->sidebar_pos == 'left' OR $us_layout->sidebar_pos == 'right' ): ?>
			<aside class="l-sidebar at_<?php echo $us_layout->sidebar_pos . ' ' . us_dynamic_sidebar_id( $default_forum_sidebar_id ); ?>"<?php echo ( us_get_option( 'schema_markup' ) ) ? ' itemscope itemtype="https://schema.org/WPSideBar"' : ''; ?>>
				<?php us_dynamic_sidebar( $default_forum_sidebar_id ); ?>
			</aside>
		<?php endif; ?>

	</div>
</div>

<?php get_footer(); ?>
