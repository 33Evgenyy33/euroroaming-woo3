<?php
/*
Template Name: Full-width layout
Template Post Type: post, page, product
*/

get_header();

//do_action( 'us_before_page' );

?>
    <link rel="stylesheet" type="text/css"
          href="http://test.euroroaming.ru/wp-content/themes/Zephyr-child/js/jquery.pagepiling.min.css"/>
    <script type="text/javascript"
            src="http://test.euroroaming.ru/wp-content/themes/Zephyr-child/js/jquery.pagepiling.min.js"></script>

    <script type="text/javascript">

        jQuery(document).ready(function ($) {




            /*
             * Plugin intialization
             */
            if ($(window).width() > 768) {
                $('#pagepiling').pagepiling({
                    sectionsColor: ['white', '#ee005a', '#2C3E50', '#39C'],
                    navigation: {
                        'position': 'right',
                        'tooltips': ['Page 1', 'Page 2', 'Page 3', 'Page 4']
                    },
                    afterRender: function () {
                        $('#pp-nav').addClass('custom');
                    },
                    afterLoad: function (anchorLink, index) {
                        if (index > 1) {
                            $('#pp-nav').removeClass('custom');
                        } else {
                            $('#pp-nav').addClass('custom');
                        }
                    }
                });

                /*
                 * Internal use of the demo website
                 */
                $('#showExamples').click(function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    $('#examplesList').toggle();
                });

                $('html').click(function () {
                    $('#examplesList').hide();
                });
            }
        });
    </script>

    <style>
        /* Section 1
         * --------------------------------------- */
        #section1 h1 {
            color: #444;
        }

        #section1 p {
            color: #333;
            color: rgba(0, 0, 0, 0.3);
        }

        #section1 img {
            margin: 20px 0;
            opacity: 0.7;
        }

        /* Section 2
         * --------------------------------------- */
        #section2 h1,
        #section2 p {
            z-index: 3;
        }

        #section2 p {
            opacity: 0.8;
        }

        #section2 #colors {
            right: 60px;
            bottom: 0;
            position: absolute;
            height: 413px;
            width: 258px;
            background-image: url(http://test.euroroaming.ru/wp-content/uploads/revslider/highlight-showcase/newscarousel4.jpg);
            background-repeat: no-repeat;
        }

        /* Section 3
         * --------------------------------------- */
        #section3 #colors {
            left: 60px;
            bottom: 0;
        }

        #section3 p {
            color: #757575;
        }

        #colors2,
        #colors3 {
            position: absolute;
            height: 163px;
            width: 362px;
            z-index: 1;
            background-repeat: no-repeat;
            left: 0;
            margin: 0 auto;
            right: 0;
        }

        #colors2 {
            background-image: url(http://test.euroroaming.ru/wp-content/uploads/revslider/vimeohero/vimeobg.jpg);
            top: 0;
        }

        #colors3 {
            background-image: url(http://test.euroroaming.ru/wp-content/uploads/revslider/highlight-showcase/newscarousel4.jpg);
            bottom: 0;
        }

        /* Section 4
         * --------------------------------------- */
        #section4 p {
            opacity: 0.6;
        }

        /* Overwriting fullPage.js tooltip color
        * --------------------------------------- */
        #pp-nav.custom .pp-tooltip {
            color: #AAA;
        }

        #markup {
            display: block;
            width: 450px;
            margin: 20px auto;
            text-align: left;
        }

    </style>

    <div id="pagepiling">
        <div class="section" id="section1">
            <h1>pagePiling.js</h1>
            <p>Create an original scrolling site</p>
            <img src="http://test.euroroaming.ru/wp-content/uploads/revslider/tech-journal/watch_big-2.png"
                 alt="pagePiling"/>
            <br/>

        </div>
        <div class="section" id="section2">
            <div class="intro">
                <div id="colors"></div>
                <h1>jQuery plugin</h1>
                <p>Pile your sections one over another and access them scrolling or by URL!</p>
                <div id="markup">
                    <script src="https://gist.github.com/alvarotrigo/4a87a4b8757d87df8a72.js"></script>
                </div>
            </div>
        </div>
        <div class="section" id="section3">
            <div class="intro">
                <h1>Configurable</h1>
                <p>Plenty of options, methods and callbacks to use.</p>
                <div id="colors2"></div>
                <div id="colors3"></div>
            </div>
        </div>
        <div class="section" id="section4">
            <div class="intro">
                <h1>Compatible</h1>
                <p>Designed to work on tablet and mobile devices.</p>
                <p>Oh! And its compatible with old browsers such as IE 8 or Opera 12!</p>
            </div>
        </div>
    </div>

<?php

//do_action( 'us_after_page' );

get_footer();