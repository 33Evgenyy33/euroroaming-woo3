<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The template for displaying archive pages
 */

$us_layout = US_Layout::instance();

get_header();

$curauth = ( get_query_var( 'author_name' ) ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );

// Creating .l-titlebar
$titlebar_vars = array(
	'title' => get_the_archive_title(),
);

us_load_template( 'templates/titlebar', $titlebar_vars );

$template_vars = array(
	'layout' => us_get_option( 'archive_layout', 'smallcircle' ),
	'type' => us_get_option( 'archive_type', 'grid' ),
	'columns' => us_get_option( 'archive_cols', 1 ),
	'img_size' => us_get_option( 'archive_img_size', 'default' ),
	'metas' => (array) us_get_option( 'archive_meta', array() ),
	'content_type' => us_get_option( 'archive_content_type', 'excerpt' ),
	'show_read_more' => in_array( 'read_more', us_get_option( 'archive_meta', array() ) ),
	'pagination' => us_get_option( 'archive_pagination', 'regular' ),
);

$default_archive_sidebar_id = us_get_option( 'archive_sidebar_id', 'default_sidebar' );

$author_avatar = get_avatar( $curauth->ID );
global $wpdb;
$author_comments_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) AS total FROM " . $wpdb->comments . " WHERE comment_approved = 1 AND user_id = %s", $curauth->ID ) );

?>
	<div class="l-main">
		<div class="l-main-h i-cf">

			<main class="l-content" itemprop="mainContentOfPage">
				<section class="l-section">
					<div class="l-section-h i-cf">

						<div class="w-author" itemscope="itemscope" itemtype="https://schema.org/Person" itemprop="author">
							<div class="w-author-img">
								<?php echo $author_avatar ?>
							</div>
							<div class="w-author-meta">
								<?php echo sprintf( _n( '%s post', '%s posts', count_user_posts( $curauth->ID ), 'us' ), count_user_posts( $curauth->ID ) ) . ', ' . sprintf( us_translate_n( '%s <span class="screen-reader-text">Comment</span>', '%s <span class="screen-reader-text">Comments</span>', $author_comments_count ), $author_comments_count ) ?>
							</div>
							<div class="w-author-url" itemprop="url">
								<?php if ( get_the_author_meta( 'url' ) ) { ?>
									<a href="<?php echo esc_url( get_the_author_meta( 'url' ) ); ?>"><?php echo esc_url( get_the_author_meta( 'url' ) ); ?></a>
								<?php } ?>
							</div>
							<div class="w-author-desc" itemprop="description"><?php the_author_meta( 'description' ) ?></div>
						</div>

						<?php do_action( 'us_before_archive' ) ?>

						<?php us_load_template( 'templates/blog/listing', $template_vars ) ?>

						<?php do_action( 'us_after_archive' ) ?>

					</div>
				</section>
			</main>

			<?php if ( $us_layout->sidebar_pos == 'left' OR $us_layout->sidebar_pos == 'right' ): ?>
				<aside class="l-sidebar at_<?php echo $us_layout->sidebar_pos . ' ' . us_dynamic_sidebar_id( $default_archive_sidebar_id ); ?>" itemscope="itemscope" itemtype="https://schema.org/WPSideBar">
					<?php us_dynamic_sidebar( $default_archive_sidebar_id ); ?>
				</aside>
			<?php endif; ?>

		</div>
	</div>


<?php
get_footer();
