<?php

class pricing_tables_shortcode
{
    static $add_script;

    static function init()
    {
        add_shortcode('price', array(__CLASS__, 'price_code'));
        add_shortcode('plan', array(__CLASS__, 'plan_code'));
        add_shortcode('option', array(__CLASS__, 'option_code'));
        add_action('init', array(__CLASS__, 'register_script'));
        add_action('wp_footer', array(__CLASS__, 'print_script'));
    }

    static function foobar_func($atts)
    {
        self::$add_script = true;
        return "foo and bar";
    }

    static function register_script()
    {
        wp_register_style( 'my-pricing-tables', get_theme_file_uri() . '/css/pricingtable.css' );
        wp_register_script('matchHeight', get_theme_file_uri() . '/js/jquery.matchHeight-min.js');
    }

    static function print_script()
    {
        if (!self::$add_script) return;
        wp_print_styles('my-pricing-tables');
        wp_print_scripts('matchHeight');
    }

    static function price_code($atts, $content)
    {
        self::$add_script = true;

        extract(shortcode_atts(array(
            'viber' => 0,
            'width' => 0// Image URL
        ), $atts));
        // инициализация глобальных переменных для прайс планов
        $GLOBALS['plan-count'] = 0;
        $GLOBALS['plans'] = array();
        // чтение контента и выполнение внутренних шорткодов
        do_shortcode($content);
        // подготовка HTML кода
        //$output = '<script>jQuery(document).ready(function(a){a(".pricing-item:first-child .pricing-feature").each(function(){a("."+a(this)[0].classList[1]).matchHeight({property:"height"})})});</script>';
        if ($viber == 1) {
            $output = '<div class="pricing pricing-palden viber">';
        } else{
            $output = '<div class="pricing pricing-palden">';
        }
        $planContentStyle = '';
        if ($width !== 0){
            $planContentStyle = '<div style="flex: 0 1 '.(string)$width.'px;" class="pricing-item">';
        } else {
            $planContentStyle = '<div class="pricing-item">';
        }
        if (is_array($GLOBALS['plans'])) {
            foreach ($GLOBALS['plans'] as $plan) {
                $planContent = $planContentStyle;
                $planContent .= $plan;
                $planContent .= '</div>';
                $output .= $planContent;
            }
        }
        $output .= '</div>';
        // вывод HTML кода
        return $output;
    }

    static function plan_code($atts, $content)
    {
        // получаем параметры шорткода
        extract(shortcode_atts(array(
            'operator' => '',          // Image URL
        ), $atts));
        // Подоготавливаем HTML: заголовок плана
        $image = '';
        $buy_btn_url = '';
        $descrip_btn_url = '';
        $plan_title = '';
        $plan_logo = '';
        $operator = strtolower($operator);
        switch ($operator) {
            case 'orange':
                $plan_logo = '<div class="pricing-deco" style="background:url(data:image/png;base64,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) no-repeat center"></div>';
//                $plan_logo = '<div class="pricing-deco orange"></div>';
                $plan_title = '<h3 class="pricing-plan-title">Сим-карта Orange<br><span style="font-size:18px;">с тарифом Go Europe</span></h3>';
                $buy_btn_url = 'https://euroroaming.ru/shop/orange-s-tarifom-go-europe/';
                $descrip_btn_url = 'https://euroroaming.ru/orange-go-europe/';
                break;
            case 'orange mundo':
                $plan_logo = '<div class="pricing-deco" style="background:url(data:image/png;base64,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) no-repeat center"></div>';
//                $plan_logo = '<div class="pricing-deco orange"></div>';
                $plan_title = '<h3 class="pricing-plan-title">Сим-карта Orange<br><span style="font-size:18px;">с тарифом Mundo</span></h3>';
                $buy_btn_url = 'https://euroroaming.ru/shop/orange-s-tarifom-go-europe/';
                $descrip_btn_url = 'https://euroroaming.ru/orange-go-europe/';
                break;
            case 'vodafone':
                $plan_logo = '<div class="pricing-deco" style="background:url(data:image/png;base64,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) no-repeat center"></div>';
//                $plan_logo = '<div class="pricing-deco vodafone"></div>';
                $plan_title = '<h3 class="pricing-plan-title">Сим-карта Vodafone<br><span style="font-size:18px;">с тарифом Smart Passport</span></h3>';
                $buy_btn_url = 'https://euroroaming.ru/shop/vodafone-s-tarifom-smart-passport/';
                $descrip_btn_url = 'https://euroroaming.ru/vodafone-smart-passport/';
                break;
            case 'ortel':
                $plan_logo = '<div class="pricing-deco" style="background:url(data:image/png;base64,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) no-repeat center"></div>';
//                $plan_logo = '<div class="pricing-deco ortel"></div>';
                $plan_title = '<h3 class="pricing-plan-title">Сим-карта Ortel Mobile<br><span style="font-size:18px;">с тарифом Cross</span></h3>';
                $buy_btn_url = 'https://euroroaming.ru/shop/ortel-s-tarifom-cross/';
                $descrip_btn_url = 'https://euroroaming.ru/ortel-s-tarifom-cross/';
                break;
            case 'globalsim':
                $plan_logo = '<div class="pricing-deco" style="background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAALQAAAAjCAMAAAAKcND7AAABU1BMVEVHcEweDwAeDwAeDwDzV0ceDwAeDwAeDwDzV0ceDwBSIBMeDwAeDwAeDwDzV0fzV0ceDwAeDwAeDwAeDwDzV0ceDwAeDwAeDwAeDwDzV0ceDwAeDwDzV0ceDwAeDwAeDwAeDwAeDwAeDwDzV0fzV0fzV0fzV0ceDwDzV0ceDwAeDwDzV0ceDwAeDwAeDwAeDwAeDwAeDwAeDwAeDwAeDwAeDwAeDwAeDwDzV0fzV0fzV0ceDwAeDwAeDwAeDwAeDwDzV0fzV0ceDwAeDwDzV0fzV0fzV0fzV0fzV0ceDwAeDwAeDwAeDwDzV0fzV0fzV0fzV0fzV0ceDwDzV0fzV0fzV0fzV0fzV0fzV0fzV0ceDwDzV0fzV0fzV0fzV0fzV0fzV0ceDwDzV0fzV0fzV0fzV0ceDwAeDwAeDwDzV0fzV0fzV0fzV0fzV0fzV0ceDwDzV0dVyZQ5AAAAb3RSTlMAHxCgD4BgsKDwAXBAL3CwWfv3kJBNdsDnL9FUUCpH9CQ0xF/88R8MQOECBpgV/glRZAXYO5277PbcA3oYioVp7EqUydGdFDIJqKVd3bnFi+dUo77Wp4ZqeSjM4jg9WQyarH7LrEWzbbYYdBpjtZSuPCbWAAAI9UlEQVR4Xu2X6VcaTRaHfw1C2w1EoIMQCGuMiLIrYsQ17ks0iTF5NWYxi9lz+f/XqVvV3dIz8GbOfHg/zXNOyKX6dtVT1VW3ES73vo+fvHi0fbZz5x6Yn+OCt/Dy7ufE635/6tH5sznYXNw5PZ84O/l49uS9vNNl+f04s+NmQn6fhody8HA+ZaTSB60aBE2/4AsYv6INxYL6GoXL3KsX/VteyhE4uguPxp0NN+f1Myju9Ac4uYLLG7ttEw59xjMxs2WQQ571AhzpcjakyEHxnCRVOOxJZZdnw6XHnniy7poeacXUWzic2k3vR0ubfrolCY+0RgoLkrY9uwPY3F/qe9gdKj130vdy6pVWLLmP31mIp8sjpVs0wKRXOkuKY0hypPgKxZupvuTR+w93Pvw+W9rGUOnfMmnjr72LzQn7wbvST/Yxd/VJNn6A4kJ2yR9Xo6RnUyTIrzZzR6uXFPFKL5BNFkyVFCVIxl73me03ULzbGyq9J7Ne/ADzSs1y2ZGegMA8UaHiO2+Wn/KJjJJWXguQZE2vdIIjntWkM794l4jSkHzoM+dzcBkqLddx6gKKHbX5PdJqF3+Cgmdw8k3ObZR0VC40HLzSRxzx6Us681usEFEFzNxT2fE+/l56bMpzqL5NSUGvtDyo3yHZ5YRTtbEvRkhniGkOl/7CEW/6mA/AKi/5MRF1wcz0mQf4g/Qzmaa2kCu4MSi9/1Ieu11IHqhNf5f/+2uEtEZM7PHKEGkVyYwMgHkiiqQ5G8xf8rGP/UlaGk3NQeJuql1b+unExNmSnMYeFBOc/g6fufHjCGksktLWc7P/IW1x1GDZx2p6KTMtm5Scu+2mH9q8/U/pczvNu/IXStplZxeKfZ7CGTD3kJt/jJDWumQzn/l3aT9HqPI1YF2+VqR0TS2J4ERJ920eeKXdtG24bMrEK3ill36PDVz+7tz3aoQ0ssfkcNDwSut8SNVZXcGNfDUmSVDw2vxZ+gVc7isDlvbw6BuY946fLHpPRkmjsZYnm6+mR5oNu1ghQXTW4Le8ko7AkdtY/pP0jkyDwq3UP2zpX2Nj0/ef9F1BU5akccGEOjKjpAFfaJ4URx7ptKpvfNEqEtG1WnwKq4PIyPOzPD09/WmEtEqb9k7i4bKn5N11ra76Xt6OlGYWSsQkPdIVtZsfE1FpkpcbuCZB4rbkjcNmfIS0SnsFm+WnalU90rtuzsu+l09/K43ZryQwPNK8bbbUizG+qKq1Xz0Pd/ipN3+QntuQO3YOigfKzys913eq8nbfy9LcaGn3NRMblJ7lYBEox4hUqFad1gcq7sbM30m7i3cKybScwsMxr/SmOhDO4bg/w6iDujlUegU2PRKkBqVrTpAkclxX3R+DePe6Lzn/vA/sT3ikf9232cPuhqrE3wDz82t7TT3SV+qZOYdz263YfJsrfcfpche4TPeKbaDRipPg+aB0gQM/gEmSrDgX1yC54m4lDx96qscA4+p1wlIfz+xZniy70q937o5vu5k4G3gomHDLU9/DDHx13hTdSoyYenFQukjqXYgIMVsQhDiyoNhkV8VoaS4gHrZ3oaS9rWPOj6vPUPyU7W+GSCdokFUMSuecVTVTHPUgWFerbzP967+RxtvBye28wxDp813O42hpH4pv6jAMkV6lW2Jrpkc646r63b8Eguqiy97dF84WOTnd3B8ujd2XT+1qcL4HxiO99PH03u3vlF9w+CjrzhDpWm+rTpJuNQt4pFschCDI6breAdNUZWSQdxczmzN7P2CzPzaIs2zmxbNXr97uLXvu25v5/PnNvWnTbVF3eHsCMOZB9tEoNoPRXAEODZ+gDKDNQRse2uriP8j/aUcD6ytoBwtALlwOHgFIBFcAZINAJgFEogCiwaOiaA8Gs9CCDSiaGQgKQQ3AwlGQ8zSREo0ucGtBRCsI57ixbTYDTdMnU5rc+0ITkmhExAkZiTvLxQzgC/oQDhy1IcYS/wqinZMCk0UADTnYSrpeiefDPgqiGNM1oiJ8MQrLqogoJYAt0QQjlqdAKU7GOqoiVVKrE/dwQBawSvG4wfM1KF7xp9rQj8OUj+UjehKzW+QL1OepF+EqsFKnMMzjugbGCEAz0jKKG4ZmVbi/yBHNUwcUAigUJMPomVXqdrkCHlGHh8wXUSslhXQ7TUI6vopW3JbWjFUgQqkqYFjmYYrngXI+VYJkPW6EgEY+lZqdjVVnA4ajEaGgVg+FKVIzVoV0j4R0COmbCOV7+JKnMMKUWrOzzUVS0iEAQjpDFFlfhT7vSvsgGifNWauSRSmVLwPxKhghvZZKC2n9GF91W7q01QYeH4dEnnFYLKWldCvWpCyYxZvDLe4vQwum8di8lcZiei3uC1M0EZvUk9nYIvlQfB5vRkiolG4ojIOttVRbZbfiJSV9fB0SXrVukiIodPJfQFt+P0tXrfBhygSTpUxsHaiv2tLVWCIppFuxRCxD4UP9S4hS10DDqC5QCwZRNyelL5MFw1K7o5UhDddps1LFeswwbqVzFO8gTEQln1661IMs3YkHIhSt5+pRCtdiawlqymx/PuRX0snn1LIq1+kcS1eNQ9BxMsnSpWTm+hgSyygkL4FkpQZzMuAj8oOlmzepeY3Ca1Y0RAlhG1RvfqOjtcHSRRIYDQDr9Vo5PlmO92B1Z2HlD2+lkaaIkM7VAL3eXRHSoQV0UhEKL1bS4jNEghuZTUnTlg4hZVn1WCRMkVYCPSoPbI8Q5YCcVTBIUEQxX6lu0bqPUj4lHaWA5hzETjxbutS0SYoYFlRT1ShoCToCsseG39+t9KReOEHrgQHprSQgBgegUxRi3MuuZdwI3RZNis/5RU1bk0fRyGuwpS9LFLWox/cdxB9X5k1H+qsebVzGDg9ii0eU0wr5KlDolPQEynoOWOvV9GJDL9T0LICEjvKBpXMR0qOHLcgm83kIgL/HTl91XadOB5g9aPktM3oI5jAKrHBvWZ2teqtAWC+vdEpWWdOzPl3T9Bxf1vQEZx8BXyxwpFczaHXafF/ZKnU0cIKeCItRoij3kou9Rq8DYPI5/mfC5ANATfzz/As2eBhIoBuCcwAAAABJRU5ErkJggg==) no-repeat center"></div>';
//                $plan_logo = '<div class="pricing-deco globalsim"></div>';
                $plan_title = '<h3 class="pricing-plan-title">Сим-карта Globalsim</h3>';
                $buy_btn_url = 'https://euroroaming.ru/shop/globalsim/';
                $descrip_btn_url = 'https://euroroaming.ru/globalsim/';
                break;
            case 'globalsim lnternet':
                $plan_logo = '<div class="pricing-deco" style="background:url(data:image/png;base64,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) no-repeat center"></div>';
//                $plan_logo = '<div class="pricing-deco globalsim"></div>';
                $plan_title = '<h3 class="pricing-plan-title">Сим-карта GlobalSim Internet</h3>';
                $buy_btn_url = 'https://euroroaming.ru/shop/globalsim-internet/';
                $descrip_btn_url = 'https://euroroaming.ru/all-globalsim/globalsim-internet/';
                break;
            case 'globalsim usa':
                $plan_logo = '<div class="pricing-deco" style="background:url(data:image/png;base64,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) no-repeat center"></div>';
//                $plan_logo = '<div class="pricing-deco globalsim"></div>';
                $plan_title = '<h3 class="pricing-plan-title">Сим-карта GlobalSim<br><span style="font-size:18px;">с тарифом США</span></h3>';
                $buy_btn_url = 'https://euroroaming.ru/shop/globalsim/';
                $descrip_btn_url = 'https://euroroaming.ru/globalsim-tarif-usa/';
                break;
            case 'europasim':
                $plan_logo = '<div class="pricing-deco" style="background:url(data:image/png;base64,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) no-repeat center"></div>';
//                $plan_logo = '<div class="pricing-deco europasim"></div>';
                $plan_title = '<h3 class="pricing-plan-title">Сим-карта EuropaSim</h3>';
                $buy_btn_url = 'https://euroroaming.ru/shop/europasim/';
                $descrip_btn_url = 'https://euroroaming.ru/europasim/';
                break;
        }

        $pricing_buttons = '<div class="pricing-buttons-block">';
        $pricing_buttons .= '<a class="w-btn style_raised pricing-action" href="' . $buy_btn_url . '" rel="nofollow"><i class="material-icons" style="left: 25%;">shopping_cart</i> Купить<span class="ripple-container"></span></a>';
        $pricing_buttons .= '<a class="w-btn style_raised pricing-action" href="' . $descrip_btn_url . '" rel="nofollow"><i class="material-icons" style="left: 17%;">description</i> Подробнее<span class="ripple-container"></span></a>';
        $pricing_buttons .= '</div>';

        // инициализация глобальных переменных для опций
        $GLOBALS['plan-options-count'] = 0;
        $GLOBALS['plan-options'] = array();
        // читаем контент и выполняем внутренние шорткоды
        do_shortcode($content);
        // Подоготавливаем HTML: опции
        $plan_options = '<ul class="pricing-feature-list">';
        /*$plan_options .= '<li class="pricing-feature row-item-0">';
        $plan_options .= $operator_title;
        $plan_options .= '</li>';*/
        if (is_array($GLOBALS['plan-options'])) {
            $int = 1;
            foreach ($GLOBALS['plan-options'] as $option) {
                $plan_options .= '<li class="pricing-feature row-item-' . $int . '">';
                $plan_options .= $option;
                $plan_options .= '</li>';
                $int++;
            }
        }
        // Подоготавливаем HTML: компонуем контент
        $plan_div = $plan_logo;
        $plan_div .= $plan_title;
        $plan_div .= $plan_options;
        $plan_div .= $pricing_buttons;
        // сохраняем полученные данные
        $i = $GLOBALS['plan-count'] + 1;
        $GLOBALS['plans'][$i] = $plan_div;
        $GLOBALS['plan-count'] = $i;
        // ничего не выводим
        return true;
    }

    static function option_code($atts, $content)
    {
        // Подоготавливаем HTML
        $plan_option = do_shortcode($content);
        // сохраняем полученные данные
        $i = $GLOBALS['plan-options-count'] + 1;
        $GLOBALS['plan-options'][$i] = $plan_option;
        $GLOBALS['plan-options-count'] = $i;
        // ничего не выводим
        return true;
    }
}

pricing_tables_shortcode::init();


class owl_slider_shortcode
{
    static $add_script;

    static function init()
    {
        add_shortcode('owl_slider', array(__CLASS__, 'owl_slider_code'));
        add_shortcode('owl_slide', array(__CLASS__, 'owl_slide_code'));
        add_action('init', array(__CLASS__, 'register_script'));
        add_action('wp_footer', array(__CLASS__, 'print_script'));
    }

    static function foobar_func($atts)
    {
        self::$add_script = true;
        return "foo and bar";
    }

    static function register_script()
    {
        wp_register_style( 'owl-base-style', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.0/assets/owl.carousel.min.css');
        wp_register_script('owl-script', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.0/owl.carousel.min.js');
    }

    static function print_script()
    {
        if (!self::$add_script) return;
        wp_print_styles('owl-base-style');
        wp_print_scripts('owl-script');
    }

    static function owl_slider_code($atts, $content)
    {
        // получаем параметры шорткода
        extract(shortcode_atts(array(
            'pc' => 4,
            'laptop' => 3,
            'phone' => 1// Image URL
        ), $atts));

        self::$add_script = true;

        // инициализация глобальных переменных для прайс планов
        $GLOBALS['slide-count'] = 0;
        $GLOBALS['slide'] = array();
        // чтение контента и выполнение внутренних шорткодов
        do_shortcode($content);
        // подготовка HTML кода

        $output = '<div class="owl-carousel owl-theme">';

        $slideContent = '<div>';

        if (is_array($GLOBALS['slide'])) {
            foreach ($GLOBALS['slide'] as $plan) {
                $planContent = $slideContent;
                $planContent .= $plan;
                $planContent .= '</div>';
                $output .= $planContent;
            }
        }
        $output .= '</div>';
        $output .= '<script>jQuery(document).ready(function(a){a(".owl-carousel").owlCarousel({items:8,lazyLoad:!0,loop:!0,margin:10,stagePadding:6,autoplay:!0,smartSpeed:1200,slideSpeed:1200,autoplayHoverPause:!0,responsiveClass:!0,responsive:{0:{items:'.$phone.',nav:!0},600:{items:'.$laptop.',nav:!0},1e3:{items:'.$pc.',nav:!0,loop:!0}}})});</script>';
        // вывод HTML кода
        return $output;
    }

    static function owl_slide_code($atts, $content)
    {
        do_shortcode($content);
        // сохраняем полученные данные
        $i = $GLOBALS['slide-count'] + 1;
        $GLOBALS['slide'][$i] = $content;
        $GLOBALS['slide-count'] = $i;
        // ничего не выводим
        return true;
    }
}

owl_slider_shortcode::init();