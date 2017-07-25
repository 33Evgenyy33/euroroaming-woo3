<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Outputs one single post.
 *
 * (!) Should be called after the current $wp_query is already defined
 *
 * @var $metas     array Meta data that should be shown: array('date', 'author', 'categories', 'comments')
 * @var $show_tags boolean Should we show tags?
 *
 * @action Before the template: 'us_before_template:templates/blog/single-post'
 * @action After the template: 'us_after_template:templates/blog/single-post'
 * @filter Template variables: 'us_template_vars:templates/blog/single-post'
 */

$us_layout = US_Layout::instance();

// Filling and filtering parameters
$default_metas = array( 'date', 'author', 'categories', 'comments' );
$metas = ( isset( $metas ) AND is_array( $metas ) ) ? array_intersect( $metas, $default_metas ) : $default_metas;
if ( ! isset( $show_tags ) ) {
    $show_tags = TRUE;
}

$post_format = get_post_format() ? get_post_format() : 'standard';

// Note: it should be filtered by 'the_content' before processing to output
$the_content = get_the_content();

$preview_type = usof_meta( 'us_post_preview_layout' );
if ( $preview_type == '' ) {
    $preview_type = us_get_option( 'post_preview_layout', 'basic' );
}

$preview_html = '';
$preview_bg = '';
$preview_size = us_get_option( 'post_preview_img_size', 'large' );
if ( $preview_type != 'none' AND ! post_password_required() ) {
    $post_thumbnail_id = get_post_thumbnail_id();
    if ( $preview_type == 'basic' ) {
        if ( in_array( $post_format, array( 'video', 'gallery', 'audio' ) ) ) {
            $preview_html = us_get_post_preview( $the_content, TRUE );
            if ( $preview_html == '' AND $post_thumbnail_id ) {
                $preview_html = wp_get_attachment_image( $post_thumbnail_id, $preview_size );
            }
        } else {
            if ( $post_thumbnail_id ) {
                $preview_html = wp_get_attachment_image( $post_thumbnail_id, $preview_size );
            } else {
                // Retreiving preview HTML from the post content
                $preview_html = us_get_post_preview( $the_content, TRUE );
            }
        }
    } elseif ( $preview_type == 'modern' OR 'trendy' ) {
        if ( $post_thumbnail_id ) {
            $image = wp_get_attachment_image_src( $post_thumbnail_id, $preview_size );
            $preview_bg = $image[0];
        } elseif ( $post_format == 'image' ) {
            // Retreiving image from post content to use it as preview background
            $preview_bg_html = us_get_post_preview( $the_content, TRUE );
            if ( preg_match( '~src=\"([^\"]+)\"~u', $preview_bg_html, $matches ) ) {
                $preview_bg = $matches[1];
            }
        }
    }
}

if ( ! post_password_required() ) {
    $the_content = apply_filters( 'the_content', $the_content );
}

// The post itself may be paginated via <!--nextpage--> tags
$pagination = us_wp_link_pages(
    array(
        'before' => '<div class="g-pagination"><nav class="navigation pagination">',
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
    $the_content = '<section class="l-section"><div class="l-section-h i-cf" itemprop="text">' . $the_content . $pagination . '</div></section>';
} elseif ( ! empty( $pagination ) ) {
    $the_content .= '<section class="l-section"><div class="l-section-h i-cf" itemprop="text">' . $pagination . '</div></section>';
}

// Meta => certain html in a proper order
$meta_html = array_fill_keys( $metas, '' );

// Preparing post metas separately because we might want to order them inside the .w-blog-post-meta in future
$meta_html['date'] = '<time class="w-blog-post-meta-date date updated';
if ( ! in_array( 'date', $metas ) ) {
    // Hiding from users but not from search engines
    $meta_html['date'] .= ' hidden';
}
$meta_html['date'] .= '" itemprop="datePublished" datetime="' . get_the_date( 'Y-m-d H:i:s' ) . '">' . get_the_date() . '</time>';

$meta_html['author'] = '<span class="w-blog-post-meta-author vcard author';
if ( ! in_array( 'author', $metas ) ) {
    $meta_html['author'] .= ' hidden';
}
$meta_html['author'] .= '">';
$meta_html['author'] .= '<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ) . '" class="fn">' . get_the_author() . '</a>';
$meta_html['author'] .= '</span>';

if ( in_array( 'categories', $metas ) ) {
    $meta_html['categories'] = get_the_category_list( ', ' );
    if ( ! empty( $meta_html['categories'] ) ) {
        $meta_html['categories'] = '<span class="w-blog-post-meta-category">' . $meta_html['categories'] . '</span>';
    }
}

$comments_number = get_comments_number();
if ( in_array( 'comments', $metas ) AND ! ( $comments_number == 0 AND ! comments_open() ) ) {
    $meta_html['comments'] .= '<span class="w-blog-post-meta-comments">';
    // TODO Replace with get_comments_popup_link() when https://core.trac.wordpress.org/ticket/17763 is resolved
    ob_start();
    $comments_label = sprintf( us_translate_n( '%s <span class="screen-reader-text">Comment</span>', '%s <span class="screen-reader-text">Comments</span>', $comments_number ), $comments_number );
    comments_popup_link( us_translate( 'No Comments' ), $comments_label, $comments_label );
    $meta_html['comments'] .= ob_get_clean();
    $meta_html['comments'] .= '</span>';
}

if ( us_get_option( 'post_nav' ) ) {
    $prevnext = us_get_post_prevnext();
}

if ( $show_tags ) {
    $the_tags = get_the_tag_list( '', ', ', '' );
}

$meta_html = apply_filters( 'us_single_post_meta_html', $meta_html, get_the_ID() );

?>
<article <?php post_class( 'l-section for_blogpost preview_' . $preview_type ) ?>>
    <div class="l-section-h i-cf">
        <div class="w-blog">
            <?php if ( ! empty( $preview_bg ) ): ?>
                <div class="w-blog-post-preview" style="background-image: url(<?php echo $preview_bg ?>)"></div>
            <?php elseif ( ! empty( $preview_html ) OR $preview_type == 'modern' ): ?>
                <div class="w-blog-post-preview">
                    <?php echo $preview_html ?>
                </div>
            <?php endif; ?>
            <div class="w-blog-post-body">
                <h1 class="w-blog-post-title entry-title" itemprop="headline"><?php the_title() ?></h1>

                <div class="w-blog-post-meta<?php echo empty( $metas ) ? ' hidden' : '' ?>">
                    <?php echo implode( '', $meta_html ) ?>
                </div>
            </div>
        </div>

        <?php
        if ( $preview_type == 'trendy' AND $us_layout->sidebar_pos == 'none' AND us_get_option( 'titlebar_post' ) == 0 AND usof_meta( 'us_titlebar' ) != 'custom' ) {
            add_action( 'wp_footer', 'us_trendy_preview_parallax', 99 );
            function us_trendy_preview_parallax() { ?>
                <script>
                    (function($){
                        var $window = $(window),
                            windowWidth = $window.width();

                        $.fn.trendyPreviewParallax = function(){
                            var $this = $(this),
                                $postBody = $this.siblings('.w-blog-post-body');

                            function update(){
                                if (windowWidth > 900) {
                                    var scrollTop = $window.scrollTop(),
                                        thisPos = scrollTop * 0.3,
                                        postBodyPos = scrollTop * 0.4,
                                        postBodyOpacity = Math.max(0, 1 - scrollTop / 450);
                                    $this.css('transform', 'translateY(' + thisPos + 'px)');
                                    $postBody.css('transform', 'translateY(' + postBodyPos + 'px)');
                                    $postBody.css('opacity', postBodyOpacity);
                                } else {
                                    $this.css('transform', '');
                                    $postBody.css('transform', '');
                                    $postBody.css('opacity', '');
                                }
                            }

                            function resize(){
                                windowWidth = $window.width();
                                update();
                            }

                            $window.bind({scroll: update, load: resize, resize: resize});
                            resize();
                        };

                        $('.l-section.for_blogpost.preview_trendy .w-blog-post-preview').trendyPreviewParallax();

                    })(jQuery);
                </script>
                <?php
            }
        }
        ?>
    </div>
</article>

<?php echo $the_content ?>

<?php if ( $show_tags AND ! empty( $the_tags ) ): ?>
    <section class="l-section for_tags">
        <div class="l-section-h i-cf">
            <div class="g-tags">
                <span class="g-tags-title"><?php us_translate( 'Tags' ) ?>:</span>
                <?php echo $the_tags ?>
            </div>
        </div>
    </section>
<?php endif;

if (is_singular('post')) {
// Код, который будет работать только на отдельных страницах с типом записи post
echo '<div style="padding: 20px 0px 3px 0;margin-bottom: 12px;background: #4c75a3;background-image: url(https://euroroaming.ru/wp-content/uploads/2016/08/bg-hero6-7.svg);background-position: bottom left;background-repeat: repeat;">';
    echo '<h3 style="padding-left: 20px;color: #fff;text-align: center;font-weight: 400;">Не забудьте взять с собой в путешествие</h3>';

    wp_register_style( 'owl-base-style', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.0/assets/owl.carousel.min.css');
    wp_register_script('owl-script', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.0/owl.carousel.min.js');
    wp_print_styles('owl-base-style');
    wp_print_scripts('owl-script');
    ?>

    <div class="owl-carousel owl-theme">
        <div class="item"><img src="https://euroroaming.ru/wp-content/uploads/2017/03/orange-min.png" alt=""></div>
        <div class="item"><img src="https://euroroaming.ru/wp-content/uploads/2017/03/Globalsim-min.png" alt=""></div>
        <div class="item"><img src="https://euroroaming.ru/wp-content/uploads/2017/03/EuropaSim-min.png" alt=""></div>
        <div class="item"><img src="https://euroroaming.ru/wp-content/uploads/2017/03/Ortel-min.png" alt=""></div>
        <div class="item"><img src="https://euroroaming.ru/wp-content/uploads/2017/03/vodafone-min.png" alt=""></div>
        <div class="item"><img src="https://euroroaming.ru/wp-content/uploads/2017/03/vodafone-internet-min.png" alt=""></div>
    </div>

    <script>jQuery(document).ready(function(a){a(".owl-carousel").owlCarousel({items:8,lazyLoad:!0,loop:!0,margin:0,stagePadding:6,autoplay:!0,smartSpeed:1200,slideSpeed:1200,autoplayHoverPause:!0,responsiveClass:!0,responsive:{0:{items:1,nav:!0},600:{items:2,nav:!0},1e3:{items:2,nav:!0,loop:!0}}})});</script>
    <style>.owl-carousel{background:rgba(255,255,255,.8);padding:12px 0 12px 0}</style>
    <?php
    echo do_shortcode('[vc_row height="auto" css=".vc_custom_1477837569113{padding-bottom: 20px !important;}"][vc_column el_class="buy-btn"][us_btn text="подробнее" link="url:https%3A%2F%2Feuroroaming.ru||target:%20_blank|"][us_btn text="купить" link="url:https%3A%2F%2Feuroroaming.ru%2Fshop||target:%20_blank|" color="secondary"][/vc_column][/vc_row]');
    echo '<style>div#gform_wrapper_5{max-width:700px;margin:0 auto;margin-bottom:20px}form#gform_5{background-image:url(https://euroroaming.ru/wp-content/uploads/2016/10/img-0-min.png);background-repeat:no-repeat;box-shadow:0 1px 3px rgba(0,0,0,.1),0 3px 8px rgba(0,0,0,.1);background-color: #fff;background-size: contain;}#gform_5 h3.gform_title{font-weight:500;padding-top: 10px;}#gform_5 label.gfield_label{font-weight:400}#gform_5 .gform_footer.top_label{background:#fec947}#gform_5 .ginput_container input{background:rgba(242,242,242,.52)!important}.buy-btn{text-align: center;}.slick-slider{background: rgba(255, 255, 255, 0.8);padding: 15px 0 12px 0;}.ult-carousel-wrapper{ margin-bottom: 0!important;}</style>';
    gravity_form(5, true, false, false, '', true);
    echo '</div>';
    }
    if (is_singular('wpsl_stores')) {
        // Код, который будет работать только на отдельных страницах с типом записи wpsl_stores
        echo '<style>div#gform_wrapper_5{max-width:700px;margin:0 auto;margin-bottom:20px}form#gform_5{background-image:url(https://euroroaming.ru/wp-content/uploads/2016/10/img-0-min.png);background-repeat:no-repeat;box-shadow:0 1px 3px rgba(0,0,0,.1),0 3px 8px rgba(0,0,0,.1);background-color: #fff;background-size: contain;}#gform_5 h3.gform_title{font-weight:500;padding-top: 10px;}#gform_5 label.gfield_label{font-weight:400}#gform_5 .gform_footer.top_label{background:#fec947}#gform_5 .ginput_container input{background:rgba(242,242,242,.52)!important}    ul#gform_fields_5 {display: flex;/* display: block; */margin: 0 auto!important;width: 100%;padding-left: 5%;}#gform_fields_5 input{width: 100%;}@media only screen and (max-width: 641px){ul#gform_fields_5 {display: block;padding-left: 0;padding-right: 16px;}.gform_validation_error ul#gform_fields_5 {padding-right: 12px!important;} }.gform_validation_error ul#gform_fields_5 {padding-right: 33px;}</style>';
        gravity_form(5, true, false, false, '', true);
    }

    /*$shorti = '[ultimate_carousel slide_to_scroll="single" title_text_typography="" slides_on_desk="2" speed="1000" autoplay_speed="1200" arrow_style="circle-bg" arrow_bg_color="#ffffff" dots="off"][us_single_image image="33920" link="||target:%20_blank|"][us_single_image image="34144" link="||target:%20_blank|"][us_single_image image="33919" link="||target:%20_blank|"][us_single_image image="34143" link="||target:%20_blank|"][us_single_image image="33921" link="||target:%20_blank|"][us_single_image image="34145" link="||target:%20_blank|"][/ultimate_carousel]';
    echo do_shortcode($shorti);*/

    ?>
    <style>
        #gform_wrapper_5 {
            padding: 10px;
        }

        #gform_5 {
            text-align: center;
        }
    </style>

<?php if ( us_get_option( 'post_sharing' ) ) : ?>
    <section class="l-section for_sharing">
        <div class="l-section-h i-cf">
            <?php if (is_singular('post')) {  ?>
                <h3 style="padding-left: 20px;color: #124572;text-align: center;font-weight: 400;"><i
                            class="fa fa-share-alt" style="color: #1e73be;"></i> Забирайте статью себе, чтобы не потерять :)
                </h3>
            <?php } ?>
            <?php
            $sharing_providers = (array) us_get_option( 'post_sharing_providers' );
            $us_sharing_atts = array(
                'type' => us_get_option( 'post_sharing_type', 'simple' ),
                'align' => ( is_rtl() ) ? 'right' : 'left',
            );
            foreach ( array( 'email', 'facebook', 'twitter', 'linkedin', 'gplus', 'pinterest', 'vk' ) as $provider ) {
                $us_sharing_atts[$provider] = in_array( $provider, $sharing_providers );
            }
            us_load_template( 'shortcodes/us_sharing', array( 'atts' => $us_sharing_atts ) );
            ?>
        </div>
    </section>
<?php endif; ?>

<?php if ( us_get_option( 'post_author_box' ) ): ?>
    <?php us_load_template( 'templates/blog/single-post-author' ) ?>
<?php endif;
if ( us_get_option( 'post_nav' ) AND ! empty( $prevnext ) ) {
    $nav_inv = 'false';
    if ( us_get_option( 'post_nav_invert', 0 ) == 1 ) {
        $nav_inv = 'true';
    }
    if ( us_get_option( 'post_nav_layout' ) == 'sided' ) {
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
    } else {
        ?>
        <section class="l-section for_blognav">
            <div class="l-section-h i-cf">
                <div class="w-blognav inv_<?php echo $nav_inv ?>">
                    <?php foreach ( $prevnext as $key => $item ): ?>
                        <a class="w-blognav-item to_<?php echo $key ?>" href="<?php echo $item['link'] ?>">
                            <span class="w-blognav-meta"><?php echo $item['meta'] ?></span>
                            <span class="w-blognav-title"><?php echo $item['title'] ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <?php
    }
} ?>

<?php if ( us_get_option( 'post_related', TRUE ) ): ?>
    <?php us_load_template( 'templates/blog/single-post-related' ) ?>
<?php endif; ?>

<?php if ( comments_open() OR get_comments_number() != '0' ): ?>
    <section class="l-section for_comments">
        <div class="l-section-h i-cf">
            <?php wp_enqueue_script( 'comment-reply' ) ?>
            <?php comments_template() ?>
        </div>
    </section>
<?php endif; ?>
