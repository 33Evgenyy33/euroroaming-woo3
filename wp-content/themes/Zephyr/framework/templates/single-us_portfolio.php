<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * The template for displaying portfolio pages
 */

// iframe param
global $us_iframe;
$us_iframe = ( isset( $_GET['us_iframe'] ) AND $_GET['us_iframe'] == 1 ) ? TRUE : FALSE;
if ( $us_iframe ) {
	add_filter('show_admin_bar', '__return_false');
	remove_action( 'wp_head', '_admin_bar_bump_cb' );
}
$us_layout = US_Layout::instance();
get_header();
if ( ! $us_iframe ) {
	us_load_template( 'templates/titlebar' );
}
$default_portfolio_sidebar_id = us_get_option( 'portfolio_sidebar_id', 'default_sidebar' );
?>
<div class="l-main">
	<div class="l-main-h i-cf">

		<main class="l-content"<?php echo ( us_get_option( 'schema_markup' ) ) ? ' itemprop="mainContentOfPage"' : ''; ?>>

			<?php do_action( 'us_before_us_portfolio' ); ?>

			<?php
			while ( have_posts() ) {
				the_post();

				$the_content = apply_filters( 'the_content', get_the_content() );

				// The page may be paginated itself via <!--nextpage--> tags
				$pagination = us_wp_link_pages(
					array(
						'before' => '<div class="w-blog-pagination"><nav class="navigation pagination">',
						'after' => '</nav></div>',
						'next_or_number' => 'next_and_number',
						'nextpagelink' => '>',
						'previouspagelink' => '<',
						'link_before' => '<span>',
						'link_after' => '</span>',
						'echo' => 0,
					)
				);

				// If content has no sections, we'll create them manually
				$has_own_sections = ( strpos( $the_content, ' class="l-section' ) !== FALSE );
				if ( ! $has_own_sections ) {
					$the_content = '<section class="l-section"><div class="l-section-h i-cf">' . $the_content . $pagination . '</div></section>';
				} elseif ( ! empty( $pagination ) ) {
					$the_content .= '<section class="l-section"><div class="l-section-h i-cf">' . $pagination . '</div></section>';
				}

				echo $the_content;

				// Post comments
				$show_comments = us_get_option( 'portfolio_comments', FALSE );
				if ( $show_comments AND ( comments_open() OR get_comments_number() != '0' ) ) {
					?>
					<section class="l-section for_comments">
					<div class="l-section-h i-cf"><?php
						wp_enqueue_script( 'comment-reply' );
						comments_template();
						?></div>
					</section><?php
				}
			}
			?>

			<?php do_action( 'us_after_us_portfolio' ); ?>

		</main>

		<?php if ( $us_layout->sidebar_pos == 'left' OR $us_layout->sidebar_pos == 'right' ): ?>
			<aside class="l-sidebar at_<?php echo $us_layout->sidebar_pos . ' ' . us_dynamic_sidebar_id( $default_portfolio_sidebar_id ); ?>"<?php echo ( us_get_option( 'schema_markup' ) ) ? ' itemscope itemtype="https://schema.org/WPSideBar"' : ''; ?>>
				<?php us_dynamic_sidebar( $default_portfolio_sidebar_id ); ?>
			</aside>
		<?php endif; ?>

	</div>
</div>
<?php
if ( ! $us_iframe AND us_get_option( 'portfolio_nav', 0 ) ) {
	$prevnext = us_get_post_prevnext();
	$nav_inv = 'false';
	if ( us_get_option( 'portfolio_nav_invert', 0 ) == 1 ) {
		$nav_inv = 'true';
	}
	if ( ! empty( $prevnext ) ) {
		?>
		<div class="l-navigation inv_<?php echo $nav_inv ?>">
			<?php
			global $us_template_directory_uri;
			$placeholder_url = $us_template_directory_uri . '/framework/img/us-placeholder-square.jpg';
			foreach ( $prevnext as $key => $item ) {
				if ( isset( $prevnext[$key] ) ) {
					$tnail_id = get_post_thumbnail_id( $item['id'] );
					if ( $tnail_id ) {
						$image = wp_get_attachment_image( $tnail_id, 'thumbnail', FALSE, array( 'class' => 'l-navigation-item-image' ) );
					}
					if ( ! $tnail_id OR empty( $image ) ) {
						$image = '<img src="' . $placeholder_url . '" alt="">';
					}
					?>
					<a class="l-navigation-item to_<?php echo $key; ?>" href="<?php echo $item['link']; ?>">
						<?php echo $image ?>
						<div class="l-navigation-item-arrow"></div>
						<div class="l-navigation-item-title">
							<span><?php echo $item['title']; ?></span>
						</div>
					</a>
					<?php
				}
			}
			?>
		</div>
		<?php
	}
}
?>

<?php get_footer() ?>
