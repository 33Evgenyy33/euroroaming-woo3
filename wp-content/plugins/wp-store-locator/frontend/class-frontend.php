<?php
/**
 * Frontend class
 *
 * @author Tijmen Smit
 * @since  1.0.0
 */

if (!defined('ABSPATH')) exit;

if (!class_exists('WPSL_Frontend')) {

    /**
     * Handle the frontend of the store locator
     *
     * @since 1.0.0
     */
    class WPSL_Frontend
    {

        /**
         * Keep track which scripts we need to load
         *
         * @since 2.0.0
         */
        private $load_scripts = array();

        /**
         * Keep track of the amount of maps on the page
         *
         * @since 2.0.0
         */
        private static $map_count = 0;

        /*
         * Holds the shortcode atts for the [wpsl] shortcode.
         * 
         * Used to overwrite the settings just before 
         * they are send to wp_localize_script.
         * 
         * @since 2.1.1
         */
        public $sl_shortcode_atts;

        private $store_map_data = array();


        /**
         * Class constructor
         */
        public function __construct()
        {

            $this->includes();

            add_action('wp_ajax_store_search', array($this, 'store_search'));
            add_action('wp_ajax_nopriv_store_search', array($this, 'store_search'));
            add_action('wp_enqueue_scripts', array($this, 'add_frontend_styles'));
            add_action('wp_footer', array($this, 'add_frontend_scripts'));

            add_filter('the_content', array($this, 'cpt_template'));

            add_shortcode('wpsl', array($this, 'show_store_locator'));
            add_shortcode('wpsl_simcards', array($this, 'show_store_simcards'));
            add_shortcode('wpsl_address', array($this, 'show_store_address'));
            add_shortcode('wpsl_hours', array($this, 'show_opening_hours'));
            add_shortcode('wpsl_map', array($this, 'show_store_map'));
        }

        /**
         * Include the required front-end files.
         *
         * @since  2.0.0
         * @return void
         */
        public function includes()
        {
            require_once(WPSL_PLUGIN_DIR . 'frontend/underscore-functions.php');
        }

        /**
         * Handle the Ajax search on the frontend.
         *
         * @since 1.0.0
         * @return json A list of store locations that are located within the selected search radius
         */
        public function store_search()
        {

            global $wpsl_settings;

            /* 
             * Check if auto loading the locations on page load is enabled.
             * 
             * If so then we save the store data in a transient to prevent a long loading time
             * in case a large amount of locations need to be displayed.
             * 
             * The SQL query that selects nearby locations doesn't take that long, 
             * but collecting all the store meta data in get_store_meta_data() for hunderds, 
             * or thousands of stores can make it really slow.
             */
            if ($wpsl_settings['autoload'] && isset($_GET['autoload']) && $_GET['autoload'] && !$wpsl_settings['debug'] && !isset($_GET['skip_cache'])) {
                $transient_name = $this->create_transient_name();

                if (false === ($store_data = get_transient('wpsl_autoload_' . $transient_name))) {
                    $store_data = $this->find_nearby_locations();

                    if ($store_data) {
                        set_transient('wpsl_autoload_' . $transient_name, $store_data, 0);
                    }
                }
            } else {
                $store_data = $this->find_nearby_locations();
            }

            do_action('wpsl_store_search');

            wp_send_json($store_data);

            exit();
        }

        /**
         * Create the name used in the wpsl autoload transient.
         *
         * @since 2.1.1
         * @return string $transient_name The transient name.
         */
        public function create_transient_name()
        {

            global $wpsl, $wpsl_settings;

            $name_section = array();

            // Include the set autoload limit.
            if ($wpsl_settings['autoload'] && $wpsl_settings['autoload_limit']) {
                $name_section[] = absint($wpsl_settings['autoload_limit']);
            }

            /* 
             * Check if we need to include the cat id(s) in the transient name.
             * 
             * This can only happen if the user used the 
             * 'category' attr on the wpsl shortcode.
             */
            if (isset($_GET['filter']) && $_GET['filter']) {
                $name_section[] = absint(str_replace(',', '', $_GET['filter']));
            }

            // Include the lat value from the start location.
            if (isset($_GET['lat']) && $_GET['lat']) {
                $name_section[] = absint(str_replace('.', '', $_GET['lat']));
            }

            /*
             * If a multilingual plugin ( WPML or qTranslate X ) is active then we have 
             * to make sure each language has his own unique transient. We do this by 
             * including the lang code in the transient name. 
             * 
             * Otherwise if the language is for example set to German on page load, 
             * and the user switches to Spanish, then he would get the incorrect 
             * permalink structure ( /de/.. instead or /es/.. ) and translated 
             * store details.
             */
            $lang_code = $wpsl->i18n->check_multilingual_code();

            if ($lang_code) {
                $name_section[] = $lang_code;
            }

            $transient_name = implode('_', $name_section);

            /*
            * If the distance unit filter ( wpsl_distance_unit ) is used to change the km / mi unit based on
            * the location of the IP, then we include the km / mi in the transient name. This is done to
            * prevent users from seeing the wrong distances from the cached data.
            *
            * This way one data set can include the distance in km, and the other one the distance in miles.
            */
            if (has_filter('wpsl_distance_unit')) {
                $transient_name = $transient_name . '_' . wpsl_get_distance_unit();
            }

            return $transient_name;
        }

        /**
         * Find store locations that are located within the selected search radius.
         *
         * This happens by calculating the distance between the
         * latlng of the searched location, and the latlng from
         * the stores in the db.
         *
         * @since 2.0.0
         * @return void|array $store_data The list of stores that fall within the selected range.
         */
        public function find_nearby_locations()
        {

            global $wpdb, $wpsl, $wpsl_settings;

            $store_data = array();

            /* 
             * Set the correct earth radius in either km or miles. 
             * We need this to calculate the distance between two coordinates. 
             */
            $radius = (wpsl_get_distance_unit() == 'km') ? 6371 : 3959;

            // The placeholder values for the prepared statement in the sql query.
            $placeholder_values = array(
                $radius,
                $_GET['lat'],
                $_GET['lng'],
                $_GET['lat']
            );

            // Check if we need to filter the results by category.
            if (isset($_GET['filter']) && $_GET['filter']) {
                $filter_ids = array_map('absint', explode(',', $_GET['filter']));
                $cat_filter = "INNER JOIN $wpdb->term_relationships AS term_rel ON posts.ID = term_rel.object_id
                               INNER JOIN $wpdb->term_taxonomy AS term_tax ON term_rel.term_taxonomy_id = term_tax.term_taxonomy_id
                                      AND term_tax.taxonomy = 'wpsl_store_category'
                                      AND term_tax.term_id IN (" . implode(',', $filter_ids) . ")";
            } else {
                $cat_filter = '';
            }

            /*
             * If WPML is active we include 'GROUP BY lat' in the sql query
             * to prevent duplicate locations from showing up in the results.
             *  
             * This is a problem when a store location for example 
             * exists in 4 different languages. They would all fall within
             * the selected radius, but we only need one store ID for the 'icl_object_id' 
             * function to get the correct store ID for the current language.         
             */
            if ($wpsl->i18n->wpml_exists()) {
                $group_by = 'GROUP BY lat';
            } else {
                $group_by = 'GROUP BY posts.ID';
            }

            /* 
             * If autoload is enabled we need to check if there is a limit to the 
             * amount of locations we need to show.
             * 
             * Otherwise include the radius and max results limit in the sql query. 
             */
            if (isset($_GET['autoload']) && $_GET['autoload']) {
                $limit = '';

                if ($wpsl_settings['autoload_limit']) {
                    $limit = 'LIMIT %d';
                    $placeholder_values[] = $wpsl_settings['autoload_limit'];
                }

                $sql_sort = 'ORDER BY distance ' . $limit;
            } else {
                array_push($placeholder_values, $this->check_store_filter('radius'), $this->check_store_filter('max_results'));
                $sql_sort = 'HAVING distance < %d ORDER BY distance LIMIT 0, %d';
            }

            $placeholder_values = apply_filters('wpsl_sql_placeholder_values', $placeholder_values);

            /* 
             * The sql that will check which store locations fall within 
             * the selected radius based on the lat and lng values. 
             */
            $sql = apply_filters('wpsl_sql',
                "SELECT post_lat.meta_value AS lat,
                           post_lng.meta_value AS lng,
                           posts.ID, 
                           ( %d * acos( cos( radians( %s ) ) * cos( radians( post_lat.meta_value ) ) * cos( radians( post_lng.meta_value ) - radians( %s ) ) + sin( radians( %s ) ) * sin( radians( post_lat.meta_value ) ) ) ) 
                        AS distance
                      FROM $wpdb->posts AS posts
                INNER JOIN $wpdb->postmeta AS post_lat ON post_lat.post_id = posts.ID AND post_lat.meta_key = 'wpsl_lat'
                INNER JOIN $wpdb->postmeta AS post_lng ON post_lng.post_id = posts.ID AND post_lng.meta_key = 'wpsl_lng'
                    $cat_filter
                     WHERE posts.post_type = 'wpsl_stores' 
                       AND posts.post_status = 'publish' $group_by $sql_sort"
            );

            $stores = $wpdb->get_results($wpdb->prepare($sql, $placeholder_values));

            if ($stores) {
                $store_data = apply_filters('wpsl_store_data', $this->get_store_meta_data($stores));
            } else {
                $store_data = apply_filters('wpsl_no_results_sql', '');
            }

            return $store_data;
        }

        /**
         * Get the post meta data for the selected stores.
         *
         * @since  2.0.0
         * @param  object $stores
         * @return array  $all_stores The stores that fall within the selected range with the post meta data.
         */
        public function get_store_meta_data($stores)
        {

            global $wpsl_settings, $wpsl;

            $all_stores = array();

            // Get the list of store fields that we need to filter out of the post meta data.
            $meta_field_map = $this->frontend_meta_fields();

            foreach ($stores as $store_key => $store) {

                // If WPML is active try to get the id of the translated page.
                if ($wpsl->i18n->wpml_exists()) {
                    $store->ID = $wpsl->i18n->maybe_get_wpml_id($store->ID);

                    if (!$store->ID) {
                        continue;
                    }
                }

                // Get the post meta data for each store that was within the range of the search radius.
                $custom_fields = get_post_custom($store->ID);

                foreach ($meta_field_map as $meta_key => $meta_value) {

                    if (isset($custom_fields[$meta_key][0])) {
                        if ((isset($meta_value['type'])) && (!empty($meta_value['type']))) {
                            $meta_type = $meta_value['type'];
                        } else {
                            $meta_type = '';
                        }

                        // If we need to hide the opening hours, and the current meta type is set to hours we skip it.
                        if ($wpsl_settings['hide_hours'] && $meta_type == 'hours') {
                            continue;
                        }

                        // Make sure the data is safe to use on the frontend and in the format we expect it to be.
                        switch ($meta_type) {
                            case 'numeric':
                                $meta_data = (is_numeric($custom_fields[$meta_key][0])) ? $custom_fields[$meta_key][0] : 0;
                                break;
                            case 'email':
                                $meta_data = sanitize_email($custom_fields[$meta_key][0]);
                                break;
                            case 'url':
                                $meta_data = esc_url($custom_fields[$meta_key][0]);
                                break;
                            case 'hours':
                                $meta_data = $this->get_opening_hours($custom_fields[$meta_key][0], apply_filters('wpsl_hide_closed_hours', false));
                                break;
                            case 'wp_editor':
                            case 'textarea':
                                $meta_data = wp_kses_post(wpautop($custom_fields[$meta_key][0]));
                                break;
                            case 'text':
                            default:
                                $meta_data = sanitize_text_field(stripslashes($custom_fields[$meta_key][0]));
                                break;
                        }

                        $store_meta[$meta_value['name']] = $meta_data;
                    } else {
                        $store_meta[$meta_value['name']] = '';
                    }

                    /* 
                     * Include the post content if the "More info" option is enabled on the settings page,
                     * or if $include_post_content is set to true through the 'wpsl_include_post_content' filter.
                     */
                    if (($wpsl_settings['more_info'] && $wpsl_settings['more_info_location'] == 'store listings') || apply_filters('wpsl_include_post_content', false)) {
                        $page_object = get_post($store->ID);
                        $store_meta['description'] = apply_filters('the_content', strip_shortcodes($page_object->post_content));
                    }

                    $store_meta['store'] = get_the_title($store->ID);
                    $store_meta['thumb'] = $this->get_store_thumb($store->ID, $store_meta['store']);
                    $store_meta['id'] = $store->ID;

                    if (!$wpsl_settings['hide_distance']) {
                        $store_meta['distance'] = round($store->distance, 1);
                    }

                    if ($wpsl_settings['permalinks']) {
                        $store_meta['permalink'] = get_permalink($store->ID);
                    }
                }

                $all_stores[] = apply_filters('wpsl_store_meta', $store_meta, $store->ID);
            }

            return $all_stores;
        }

        /**
         * The store meta fields that are included in the json output.
         *
         * The wpsl_ is the name in db, the name value is used as the key in the json output.
         *
         * The type itself is used to determine how the value should be sanitized.
         * Text will go through sanitize_text_field, email through sanitize_email and so on.
         *
         * If no type is set it will default to sanitize_text_field.
         *
         * @since 2.0.0
         * @return array $store_fields The names of the meta fields used by the store
         */
        public function frontend_meta_fields()
        {

            $store_fields = array(
                'wpsl_address' => array(
                    'name' => 'address'
                ),
                'wpsl_address2' => array(
                    'name' => 'address2'
                ),
                'wpsl_city' => array(
                    'name' => 'city'
                ),
                'wpsl_state' => array(
                    'name' => 'state'
                ),
                'wpsl_zip' => array(
                    'name' => 'zip'
                ),
                'wpsl_country' => array(
                    'name' => 'country'
                ),
                'wpsl_lat' => array(
                    'name' => 'lat',
                    'type' => 'numeric'
                ),
                'wpsl_lng' => array(
                    'name' => 'lng',
                    'type' => 'numeric'
                ),
                'wpsl_phone' => array(
                    'name' => 'phone'
                ),
                'wpsl_fax' => array(
                    'name' => 'fax'
                ),
                'wpsl_email' => array(
                    'name' => 'email',
                    'type' => 'email'
                ),
                'wpsl_hours' => array(
                    'name' => 'hours',
                    'type' => 'hours'
                ),
                'wpsl_url' => array(
                    'name' => 'url',
                    'type' => 'url'
                )
            );

            return apply_filters('wpsl_frontend_meta_fields', $store_fields);
        }

        /**
         * Get the store thumbnail.
         *
         * @since 2.0.0
         * @param string $post_id The post id of the store
         * @param string $store_name The name of the store
         * @return void|string $thumb      The html img tag
         */
        public function get_store_thumb($post_id, $store_name)
        {

            $attr = array(
                'class' => 'wpsl-store-thumb',
                'alt' => $store_name
            );

            $thumb = get_the_post_thumbnail($post_id, $this->get_store_thumb_size(), apply_filters('wpsl_thumb_attr', $attr));

            return $thumb;
        }

        /**
         * Get the store thumbnail size.
         *
         * @since 2.0.0
         * @return array $size The thumb format
         */
        public function get_store_thumb_size()
        {

            $size = apply_filters('wpsl_thumb_size', array(45, 45));

            return $size;
        }

        /**
         * Get the opening hours in the correct format.
         *
         * Either convert the hour values that are set through
         * a dropdown to a table, or wrap the textarea input in a <p>.
         *
         * Note: The opening hours can only be set in the textarea format by users who upgraded from 1.x.
         *
         * @since 2.0.0
         * @param  array|string $hours The opening hours
         * @param  boolean $hide_closed Hide the days were the location is closed
         * @return string       $hours       The formated opening hours
         */
        public function get_opening_hours($hours, $hide_closed)
        {

            $hours = maybe_unserialize($hours);

            /* 
             * If the hours are set through the dropdown then we create a table for the opening hours.
             * Otherwise we output the data entered in the textarea. 
             */
            if (is_array($hours)) {
                $hours = $this->create_opening_hours_tabel($hours, $hide_closed);
            } else {
                $hours = wp_kses_post(wpautop($hours));
            }

            return $hours;
        }

        /**
         * Create a table for the opening hours.
         *
         * @since  2.0.0
         * @todo   add schema.org support.
         * @param  array $hours The opening hours
         * @param  boolean $hide_closed Hide the days where the location is closed
         * @return string  $hour_table  The opening hours sorted in a table
         */
        public function create_opening_hours_tabel($hours, $hide_closed)
        {

            $opening_days = wpsl_get_weekdays();

            // Make sure that we have actual opening hours, and not every day is empty.
            if ($this->not_always_closed($hours)) {
                $hour_table = '<table class="wpsl-opening-hours">';
                $hour_table .= '<span class="wpsl-location-address-label" >Режим работы:</span>';

                foreach ($opening_days as $index => $day) {
                    $i = 0;
                    $hour_count = count($hours[$index]);

                    // If we need to hide days that are set to closed then skip them.
                    if ($hide_closed && !$hour_count) {
                        continue;
                    }

                    $hour_table .= '<tr>';
                    $hour_table .= '<td>' . esc_html($day) . '</td>';

                    // If we have opening hours we show them, otherwise just show 'Closed'.
                    if ($hour_count > 0) {
                        $hour_table .= '<td>';

                        while ($i < $hour_count) {
                            $hour = explode(',', $hours[$index][$i]);
                            $hour_table .= '<time>' . esc_html($hour[0]) . ' - ' . esc_html($hour[1]) . '</time>';

                            $i++;
                        }

                        $hour_table .= '</td>';
                    } else {
                        $hour_table .= '<td>' . __('Closed', 'wpsl') . '</td>';
                    }

                    $hour_table .= '</tr>';
                }

                $hour_table .= '</table>';

                return $hour_table;
            }
        }

        /**
         * Create the wpsl post type output.
         *
         * If you want to create a custom template you need to
         * create a single-wpsl_stores.php file in your theme folder.
         * You can see an example here https://wpstorelocator.co/document/create-custom-store-page-template/
         *
         * @since  2.0.0
         * @param  string $content
         * @return string $content
         */
        public function cpt_template($content)
        {

            global $wpsl_settings, $post;

            if (isset($post->post_type) && $post->post_type == 'wpsl_stores' && is_single() && in_the_loop()) {
                array_push($this->load_scripts, 'wpsl_base');

                $content .= '<div class="wpsl-page-ta-simcards">';
                $content .= '<h3 style="text-align: center">Сим-карты</h3>';
                $content .= '[wpsl_simcards]';
                $content .= '</div>';
                $content .= '<div class="wpsl-page-ta-content">';
                $content .= '<div class="wpsl-page-ta-details">';
                $content .= '<h3 style="text-align: center">Информация о пункте</h3>';
                //$content .= '[wpsl_simcards]';
                $content .= '[wpsl_address]';
                if (!$wpsl_settings['hide_hours']) {
                    $content .= '[wpsl_hours]';
                    $content .= '</div>';
                } else {
                    $content .= '</div>';
                }
                $content .= '<div class="wpsl-page-ta-map">';
                $content .= '[wpsl_map]';


                $content .= '</div>';
            }

            return $content;
        }

        /**
         * Handle the [wpsl] shortcode attributes.
         *
         * @since 2.1.1
         * @param array $atts Shortcode attributes
         */
        public function check_sl_shortcode_atts($atts)
        {

            // Change the category slugs into category ids.
            if (isset($atts['category']) && $atts['category']) {
                $term_ids = array();
                $cats = explode(',', $atts['category']);

                foreach ($cats as $key => $cat_slug) {
                    $term_data = get_term_by('slug', $cat_slug, 'wpsl_store_category');

                    if (isset($term_data->term_id) && $term_data->term_id) {
                        $term_ids[] = $term_data->term_id;
                    }
                }

                if ($term_ids) {
                    $this->sl_shortcode_atts['js']['categoryIds'] = implode(',', $term_ids);
                }
            }

            /*
             * Use a custom start location? 
             * 
             * If the provided location fails to geocode, 
             * then the start location from the settings page is used.
             */
            if (isset($atts['start_location']) && $atts['start_location']) {
                $name_section = explode(',', $atts['start_location']);
                $transient_name = 'wpsl_' . trim(strtolower($name_section[0])) . '_latlng';

                /*
                 * Check if we still need to geocode the start location, 
                 * or if a transient with the start latlng already exists.
                 */
                if (false === ($start_latlng = get_transient($transient_name))) {
                    $start_latlng = wpsl_get_address_latlng($atts['start_location']);
                    set_transient($transient_name, $start_latlng, 0);
                }

                if (isset($start_latlng) && $start_latlng) {
                    $this->sl_shortcode_atts['js']['startLatlng'] = $start_latlng;
                }
            }

            if (isset($atts['category_filter_type']) && in_array($atts['category_filter_type'], array('dropdown', 'checkboxes'))) {
                $this->sl_shortcode_atts['category_filter_type'] = $atts['category_filter_type'];
            }

            if (isset($atts['checkbox_columns']) && is_numeric($atts['checkbox_columns'])) {
                $this->sl_shortcode_atts['checkbox_columns'] = $atts['checkbox_columns'];
            }
        }

        /**
         * Handle the [wpsl] shortcode.
         *
         * @since 1.0.0
         * @param  array $atts Shortcode attributes
         * @return string $output The wpsl template
         */
        public function show_store_locator($atts)
        {

            global $wpsl_settings;

            $atts = shortcode_atts(array(
                'template' => $wpsl_settings['template_id'],
                'category' => '',
                'category_filter_type' => '',
                'start_location' => '',
                'checkbox_columns' => '3'
            ), $atts);

            $this->check_sl_shortcode_atts($atts);

            // Make sure the required scripts are included for the wpsl shortcode.
            array_push($this->load_scripts, 'wpsl_store_locator');

            $template_list = wpsl_get_templates();
            $template_path = '';

            // Loop over the template list and look for a matching id with the one set on the settings page.
            foreach ($template_list as $template) {
                if ($atts['template'] == $template['id']) {
                    $template_path = $template['path'];
                    break;
                }
            }

            // Check if we have a template path and the file exists, otherwise we use the default template.
            if (!$template_path || (!file_exists($template_path))) {
                $template_path = WPSL_PLUGIN_DIR . 'frontend/templates/default.php';
            }

            $output = include($template_path);

            return $output;
        }

        public function check_orange_format($num = '', $format_type = '')
        {
            $orange_combo_check = array("6050", "6051", "6052");
            $orange_nano_check = array("615", "625", "635", "692", "6053", "6054", "6055", "6056", "6057", "6058", "6059");
            $format_array = array();

            if ($format_type == 'combo') {
                $format_array = $orange_combo_check;
            } elseif ($format_type == 'nano') {
                $format_array = $orange_nano_check;
            }

            $check_num = substr($num, 0, 4);
            foreach ($format_array as $format) {
                if (strpos($check_num, $format) || $check_num == $format) {
                    return true;
                }
            }
            return false;
        }

        /**
         * Handle the [wpsl_simcards] shortcode.
         *
         * @since 2.0.0
         * @todo   add schema.org support.
         * @param  array $atts Shortcode attributes
         * @return void|string $output The store simcards
         */
        public function show_store_simcards($atts)
        {

            global $post, $wpsl_settings, $wpsl;

            $atts = wpsl_bool_check(shortcode_atts(apply_filters('wpsl_simcards_shortcode_defaults', array(
                'id' => '',
                'ta_id' => true,
            )), $atts));

            if (get_post_type() == 'wpsl_stores') {
                if (empty($atts['id'])) {
                    if (isset($post->ID)) {
                        $atts['id'] = $post->ID;
                    } else {
                        return;
                    }
                }
            } else if (empty($atts['id'])) {
                return __('If you use the [wpsl_simcards] shortcode outside a store page you need to set the ID attribute.', 'wpsl');
            }

            $content = '<div class="wpsl-page-ta-simcards-grid">'; //Начало сетки сим-карт

            // Проверка на наличие id кабинета ТА (если есть на селлере)
            if ($atts['ta_id'] && $store_address = get_post_meta($atts['id'], 'wpsl_ta_id', true)) {


                $ta_id = intval(str_replace(" ", "", get_post_meta($atts['id'], 'wpsl_ta_id', true)));

                $url = "http://seller.sgsim.ru/euroroaming_order_submit?operation=get_simcards&ta=$ta_id";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Устанавливаем параметр, чтобы curl возвращал данные, вместо того, чтобы выводить их в браузер.
                curl_setopt($ch, CURLOPT_URL, $url);
                $data = curl_exec($ch);
                curl_close($ch);
                $array_of_simcard = (array)json_decode($data);
                krsort($array_of_simcard);

                //$content .= '<pre>' . print_r($array_of_simcard, true) . '</pre>';

                $orange_img = '<div class="pricing-deco" style="background:url(data:image/png;base64,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) no-repeat center;height: 90px;"></div>';
                $globalsim_img = '<div class="pricing-deco" style="background:url(data:image/png;base64,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) no-repeat center;height: 90px;"></div>';
                $europasim_img = '<div class="pricing-deco" style="background:url(data:image/png;base64,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) no-repeat center;height: 90px;"></div>';
                $ortel_img = '<div class="pricing-deco" style="background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIIAAAA/CAMAAAAbieBkAAACjlBMVEVHcEztHCTtHCPuGyPtHCTtHCTlNj7tHCTtGyP0cnbuHCPuGSHvIynuGSHuGyTuGyPuGyTuHCTuHiXtGyPuGyL+6OjuGSDtHCTuHSXuHCXuHCTuHybtGiJ/h53uGyT////tHCT////rCw/zbG/vJSv////////lsLX6v8DsExf/9vf3mJzuLTHtGiHtFx71i4z7wMLwREfyYWX///8IHUTxS0/7yMmRmar5r7LvRUjsLjD7zM3////3n6H7xMT////yY2jg4OT////////zd3gPK1Hr7O/z9ff3lpcLJE0PLFL///8QLFP5rLD1jY75rrH////6xcb4oqTxT1L96eryc3X///////9HT28IHUjzYmcAEj8OK1IOIElLUHH0en385ue+v8b0dXf+6en0eHv/9PP///+aobL////tHCTtGiLrBAjtHibtHCUPK1PqAQT//v4ADjvsDRXrBw3sFBz19vgBF0PsCBD4+fruICjtFx4ABzUAASsAEj/95+gAAiUEHEbsERkGIUrxTlDw8fRsdpBcaYXAw88iLlUNFT/T198JDzQ3RGcCBi77/Pz82tv7xcbe4OfsERf/+vr+7O3uLzL/9fYYIUsRLlZBTG3+/v76trd3gJfuJyy4vMkwNVX1g4Xj5evr7fGVmqzX2+L4nqD6v8AqOV/+8fFKUG+DjKHDx9MKJk/wQUX1eHtTY4BUWnj94OFkcIsNKVEyPWHR09vvNzrpAADzYWQoKknb3uRqboOjrL2iprU8P1r5pqhTV3KMkqbM0Nr7zM1FSWJeYXrn6e6AgpfyWFoVHET80dL3l5rHy9WqrbywtMLJytT809QSJU0cHzn5ra9GVnSrssC0usf2j5AeJ0gAAA8r7u6aAAAAaHRSTlMA6qkM/tgG8/kCFWIhNZBXK8Cf+z8Let/kbsqF0v5P87Xm9P5ISKsUSO1uLtWuuvxayqtYIrSl1b/g5isZeTW+e8+U1JcxK4Rl9jUx6pTqXpjs9Pjhv52Jn7wvdbC14lG56ovEys3F/vgo3mEAAAuTSURBVHhetZkFcxtJHsUtGWRbZo4piZ14A5tkMbeUpVu8Wz5mvvTMiJmZ0czMzMwcZv421z1jy/ZKivYS5ZWSUo9V/X79+t89U9MRYVZcTGbme788/vqpzz/+bUnJB//96GdIH/3nX38/f/7rC9+eePelmJhw+sW88YtPP/3FGzvmccj71O9KPvj50cOHD3vq6wEAvMW7Tp3TfhNUVGs2Ghp++OGbf7/24d8SYuLChfDGn7/86qsv/wTtMz87furjEuQNnffrxhX+IF/3ENFUVFRUV2uuNpwrfvtMVmJkXHgQ/lDLmeD//rNT332AzEEAPaiR1OruNAOfYB6VV6/96uRZZnw4IF76i4Fj4BccRe7wEwxB4kPwYVQ2GIvymZGhSysuJiYuOOp7xz//q5JjUK+BA6r3eGSe+mAIPmkeYUX0xKcDxCcn0Y8cOZPETNi9UJgMBVuRGczC91//7qN/XHRJOIO6kZs3byytA0rimwWWkZERc0dnfXAESt1VRP6xpwBEMnMZsRhULCP6EBVYRjaDxqAlRjBTc4po3x+uty47OUh37ty54jQDJNmNuhq7GsluKxCHQAAbVUR0RvAI8hiYT4y0eBKBgYgOJdEwtrRpA4DmOfcEQjBIdHy3BUCZLtn5BolSqeQbBtXOBVMIBNBQLshNCIaQFcVG3tHRDDbyTUIVkUhjwa+0WDabtYOgGqxFCGqJUoUQZGtOyYROd99sdkkmOBJnhycEQm8xoUgKslMdi2ZhOBadnJCQnIpB0QrhxT+eVGAYS0RwudwZfSkAZUuNPTrOoOF+R8faGjKx1vBrdTUd22Jx55Z6olY5WRYCAWwohDmnAyMksdkYnsOkKgDD2Hp6RNzx8+kCDMNF05dnZ43G22gZtvXxYQb9wCOTwQGbzMpag70AfoN4NsmEQXIzFELlELfow4AEKUdQ/HSyCuOTIIIg+1TJ0auYAFeUt3orKys1mgoARSEUAEqOSRTCxXmHw9HpqJNMDPLXZCEQeou5wjcDImTkY9A3i2owMTZbkP49AKUQQV/eDaB4PIA0X4cQGqkGsHIknAlODyXbBNw3G8UhEKpbCf3LAREKs1EtHtqpCz3OKi+/RyGIyhsAqf0Iu7uQwcCp5aiVSHyJQSdxm02hEIYJRXQohMzX3xLpIcLKLsK9oAiLOgO5TfjkbAyF0DtL4NFBFgS5AyCAkqMNehFKITTCw0Edx1CzZm3e0cPm+foQCJppQhR4IhJz2RhLmBdxvOQoqHhE4GwBVhoa4boN3hTtHQc8QiCUKgTSwOUYmcbCWMTL548CAMaMBAsnhnqDIQzyzYBSWZ+ydkIyeQvsaOqSNUQKqBQE/wx8i2QWseR6ffEYAJXDUpFcJGzlBUDgoUXJUfc8BLKpKQ8AHUpD7aB68mKnDIBta/+keylECt4moZ5xIsiDQBqhaBIJhoaHjeXlcpw7PQYCpeCx8AdrJ3Q94xYbiqJtVQWb6isui3n8vk2iVD/wIQRbkgri1ZcCEmQef2t6plyukEKJ5OUzTd3kxOFwXxDsRwCLV5RwHeokfJWFjL5PyR+ETVI6tdKXwsNglSAq+iLgA8m7X/+6vtRIcAkh/Ac/RpIAdAtniBnuCtgncb9dyefzlSotiQA6zTbYVPMptQwAsHRXpVbxHwRcDkYCCxhCXOYrb3X38oCm6/I0S6HAqi5f8+7MXPFs8ezsBtiv9hsLda5JV93qDao6ZNb+UVePzWab7BtfWhcDcGvBMm4Znwo8DaLyk18EiuCTb0qrye40G90NDd0bmr0HPqQKcFCmzqn1KUebzFcfbY4Bq9W6Pm+qR01ZO5LMn4C3Ui7AhW9m+mdw4jfnvOB5VQ9Cq4FFsATRp/1XwoW3H1UCkt4kRh3Vi00y3g52u8PRTnZOXReLxR7f0GFjt8kT+wZNdiOWwb/4l2IVl42dZPoRnP5k6Fo1IHVzdHQR3f1GRzsoW9PF0ZaWvo52QKqtcWRkdXT8hoky6ti0jK9u3aJ+2L/a4bNcGhkZXV3tu+FHMM3FWbH+T0zvvzm9sjvVjSrtcifY3tKq7pMIJrNT1dLido5TDI4Wla7GrrxykULot8MFsGwlG9uuJyO+GCwqvs5ud6+Bg+qensHlUfT4HxOceLVq707cr1by10CzXSkZQQiei273+Lqj0anqQN3Xd06qCjof9DxZEAPUbF9scY87qEja72stPgSzyvXgurW57EB18BqquLgcO5LyY4JXXm6CBD4EyQR/VHZJyaEQ2vu0rjaYRZ22bhtAQYQO2UCPfQlQcixr+wHYRRjfQ3C7HjqsaHXuqbqrCRKwUxP9CN4R7d/4+vl1fbbxSdeokkRo61GZYb+eRretjERwSWzLj92jqIG03qItCIgg0dVccT62gj1VtooIthzzJ/j2HWEXbz+Ce2vJprItmVUWCsFNIlxy2+Z9CJucx5faDiD4T4T6Tt/q5ug68Mk7K5SyWKxAGcy0VoP9CKrRtoUnfdsLWhJh26W9D9OUjWgn23YmYk1s6nc6F30TETgFbV8ZkHn2JuEeuvmwoo74EZx4e8aoAQcR+sTNdYumLSoFcYHKftEkXryibRRTKbg7PPsQBibdjWLPDoLbMl/WOS+maqFuvb1s3gQoaVoVMyI5Fkv3q8TTr86ISsEBNT5Z7qw3gbK+J9SinK/T2re27FpX5+6itK1u2lWj5LTIlpbtyscL1L6wXaet2dxc3iRbFpV9eXO5ZYnso7rbSAhhGTCSEvz2xNcINA0H1HF3qwz1t3DXTC2oqYU5p3POsjOr86u2lsdzLWYH2RJfutvjerzcTKWwMNcyN3d3jizBSy22OdggtzfNORaaBIyW5bcfxF0ommm6fZCA19Y8JUPjczR3UghANrC4aN1dXeJ16/WBAcdOk1dmHbhlvUXF7XFYB65fH7hOtubJ7wOwfnobjASBy7Go3GT/J4T3cwguDCGYeCAMqrg6rOCiCBiBXmtkvkYI8FLwQjX2qIorhVXAzoaT4K8TJ6WEUfNCAbqGCK5CzsYYZzLiIgKFIMSFw9UvEsAo5erlLBhBXuD3GadpAoX0WsULBBBwy1ksDKPRYQQB9WGRQkQ9FodfFV6UAAJgM1KZ8RFBlCbCXxBC9e1HQ0IqgfRsuBsFVaoCU0i7wj4RPE3pcBVB1gCG5aRlPO3ddzTGxohwl2O1d2UW4xIiEoBGL3z6a9ZoHMOEQ5qwBrDROiTgCnE5mwRIDvWeNxf+LIzFwOu93XWZRVAlgEXlIIBQokdhbBYx5A2Pv/decZWUK8VZKIDY7DQ4BaF1CL1TxbnFlc+9Aiuvds1WwQlQkP4YIzqJLMLQSsmBP5frucOa5xq+pvTc5SYhVyhis5B/es7ZrJSfet4QkxWLGATcy1ef0R6etKwUDykINH7SPwoGcCwy4qcrPhVDDCJuVZeG9/+G3wvth41yIZcQwPmn/LPpTL8AQiglF4Ni4UKhcWWs4qcPvnKs+1rxUJOUC+PHdv1zjmQlPsO5XiLJwGbpCcFQa6mmmhfKvLfS2901fHkah6OXinC2b/zQ/xmPAFPoDMTAZosIrmKouKt0rLK3ouIgCTphg97whK2rddZYhdyFAgXOhu7wEwVPTtIOpUD/Zz3zimHmp2NkX7iAIIT4tLG49dq97o3bXu/Y2JjXexu+7Vi51jo8a5xuEkkJgpBCdzI6cv0zcs7mFSbEPd+JW1xKXnRsFOoSx3CFQEgQaIzypqYqpKYmOa4XCKmremSOo8wwcvTZqUnMlEjo//xKOZRKoygwHIcfhV4gkEqlQqEQ/i8Q6EUK8jKO7bnT8ulZyWGyp5KIz8hLzWFQGKQNvifK2WfOoOXS85iJ8TE++7BRRKYk59FzaQxGbHoURNmxpQo+Kh1aw5GfTcs7dCwlHp5hRoQdgKKIiYxPLGTmpaWdSc3Nz8+PhsrPz009Qk+D1snQOzIGuYdP/wOjyTX5ELvTWQAAAABJRU5ErkJggg==) no-repeat center;height: 90px;"></div>';
                $vodafone_img = '<div class="pricing-deco" style="background:url(data:image/png;base64,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) no-repeat center;height: 90px;"></div>';
                $travelchat_img = '<div class="pricing-deco" style="background:url(data:image/png;base64,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) no-repeat center;height: 90px;"></div>';

                $store_phone = get_post_meta($atts['id'], 'wpsl_phone', true);

                $in_stock = "wpsl-sim-card-in_stock";
                $out_of_stock = "wpsl-sim-card-out_of_stock";

                // Если массив сим-карт не пуст, то выводим
                if (!empty($array_of_simcard)) {



                    foreach ($array_of_simcard as $key => $oper) {

                        //Если Vodafone Red или TravelChat пропускаем
                        if ($key == 'unknown' || $key == 'globalsim--travelchat') continue;

                        //Если Globalsim Internet то делаем клон для TravelChat
                        if ($key == 'globalsim--gsim_internet') {

                            $content .= '<div class="wpsl-page-ta-simcard">';
                            $content .= $globalsim_img;
                            $content .= '<h4 class="wpsl-operator-header">Globalsim «Internet»</h4>';
                            $content .= '<div class="wpsl-operator-format_grid">';
                            $content .= '<div class="wpsl-operator-format-type">';
                            $content .= '<p class="wpsl-operator-format-type_label">Кол-во:</p>';
                            $content .= '</div>';
                            $content .= '<div class="wpsl-operator-format-count">';
                            $content .= '<p class="'.(count($oper) > 0 ? $in_stock: $out_of_stock).'">' . count($oper) . ' шт.</p>';
                            $content .= '</div>';
                            $content .= '</div>';
                            $content .= '<div class="wpsl-operator-format_contact">';
                            $content .= '<p style="margin-bottom: 4px;font-weight: 300;color: #000;">Размер уточняйте по телефону:</p>';
                            $content .= '<p style="color: #000;">' . $store_phone . '</p>';
                            $content .= '</div>';
                            $content .= '</div>';

                            $content .= '<div class="wpsl-page-ta-simcard">';
                            $content .= $travelchat_img;
                            $content .= '<h4 class="wpsl-operator-header">TravelChat</h4>';
                            $content .= '<div class="wpsl-operator-format_grid">';
                            $content .= '<div class="wpsl-operator-format-type">';
                            $content .= '<p class="wpsl-operator-format-type_label">Кол-во:</p>';
                            $content .= '</div>';
                            $content .= '<div class="wpsl-operator-format-count">';
                            $content .= '<p class="'.(count($oper) > 0 ? $in_stock: $out_of_stock).'">' . count($oper) . ' шт.</p>';
                            $content .= '</div>';
                            $content .= '</div>';
                            $content .= '<div class="wpsl-operator-format_contact">';
                            $content .= '<p style="margin-bottom: 4px;font-weight: 300;color: #000;">Размер уточняйте по телефону:</p>';
                            $content .= '<p style="color: #000;">' . $store_phone . '</p>';
                            $content .= '</div>';
                            $content .= '</div>';

                            continue;
                        }

                        $content .= '<div class="wpsl-page-ta-simcard">';

                        $simcard = '';

                        switch ($key) {
                            case 'orange':
                                $content .= $orange_img;
                                $simcard = "Orange";
                                break;
                            case 'globalsim--classic':
                                $content .= $globalsim_img;
                                $simcard = "Globalsim";
                                break;
                            case 'globalsim--tariff_usa':
                                $content .= $globalsim_img;
                                $simcard = "Globalsim «США»";
                                break;
                            case 'globalsim--europasim':
                                $content .= $europasim_img;
                                $simcard = "Europasim";
                                break;
                            case 'ortel':
                                $content .= $ortel_img;
                                $simcard = "Ortel Mobile";
                                break;
                            case 'vodafone':
                                $content .= $vodafone_img;
                                $simcard = "Vodafone";
                                break;
                            case 'unknown':
                                $content .= $vodafone_img;
                                $simcard = "Vodafone «Red»";
                                break;
                        }


                        $content .= '<h4 class="wpsl-operator-header">' . $simcard . '</h4>';
                        if ($key == 'orange') {
                            $combo = 0;
                            $nano = 0;
                            foreach ($oper as $num) {
                                if ($this->check_orange_format($num, 'combo'))
                                    $combo++;
                                if ($this->check_orange_format($num, 'nano'))
                                    $nano++;
                            }

                            $content .= '<div class="wpsl-operator-format_grid">';
                            $content .= '<div class="wpsl-operator-format-type">';
                            $content .= '<p class="wpsl-operator-format-type_label">Комбо</p>';
                            $content .= '<p class="wpsl-operator-format-type_description">(стандарт+микро)</p>';
                            $content .= '</div>';
                            $content .= '<div class="wpsl-operator-format-count">';
                            $content .= '<p class="'.($combo > 0 ? $in_stock: $out_of_stock).'">' . $combo . ' шт.</p>';
                            $content .= '</div>';
                            $content .= '</div>';

                            $content .= '<div class="wpsl-operator-format_grid">';
                            $content .= '<div class="wpsl-operator-format-type">';
                            $content .= '<p class="wpsl-operator-format-type_label">3 в 1</p>';
                            $content .= '<p class="wpsl-operator-format-type_description">(стандарт+микро+нано)</p>';
                            $content .= '</div>';
                            $content .= '<div class="wpsl-operator-format-count">';
                            $content .= '<p class="'.($nano > 0 ? $in_stock: $out_of_stock).'">' . $nano . ' шт.</p>';
                            $content .= '</div>';
                            $content .= '</div>';

                            $content .= '</div>';
                            continue;
                        }

                        if ($key == 'vodafone' || $key == 'ortel') {
                            $content .= '<div class="wpsl-operator-format_grid">';
                            $content .= '<div class="wpsl-operator-format-type">';
                            $content .= '<p class="wpsl-operator-format-type_label">3 в 1</p>';
                            $content .= '<p class="wpsl-operator-format-type_description">(стандарт+микро+нано)</p>';
                            $content .= '</div>';
                            $content .= '<div class="wpsl-operator-format-count">';
                            $content .= '<p class="'.(count($oper) > 0 ? $in_stock: $out_of_stock).'">' . count($oper) . ' шт.</p>';
                            $content .= '</div>';
                            $content .= '</div>';

                            $content .= '</div>';
                            continue;
                        }

                        $content .= '<div class="wpsl-operator-format_grid">';
                        $content .= '<div class="wpsl-operator-format-type">';
                        $content .= '<p class="wpsl-operator-format-type_label">Кол-во:</p>';
                        $content .= '</div>';
                        $content .= '<div class="wpsl-operator-format-count">';
                        $content .= '<p class="'.(count($oper) > 0 ? $in_stock: $out_of_stock).'">' . count($oper) . ' шт.</p>';
                        $content .= '</div>';
                        $content .= '</div>';
                        $content .= '<div class="wpsl-operator-format_contact">';
                        $content .= '<p style="margin-bottom: 4px;font-weight: 300;color: #000;">Размер уточняйте по телефону:</p>';
                        $content .= '<p style="color: #000;">' . $store_phone . '</p>';
                        $content .= '</div>';

                        $content .= '</div>';
                    }
                } else {
                    $content .= '<h4>Сим-карт нет в наличие</h4>';
                }

            } else {
                $content .= '<h4>Наличие сим-карт уточняйте по телефону: <span style="font-weight: 500">' . get_post_meta($atts['id'], 'wpsl_phone', true) . '</span></h4>';
            }

            $content .= '</div>'; //Конец сетки сим-карт

            return $content;
        }

        /**
         * Handle the [wpsl_address] shortcode.
         *
         * @since 2.0.0
         * @todo   add schema.org support.
         * @param  array $atts Shortcode attributes
         * @return void|string $output The store address
         */
        public function show_store_address($atts)
        {

            global $post, $wpsl_settings, $wpsl;

            $atts = wpsl_bool_check(shortcode_atts(apply_filters('wpsl_address_shortcode_defaults', array(
                'id' => '',
                'name' => true,
                'address' => true,
                'address2' => true,
                'city' => true,
                'state' => true,
                'zip' => true,
                'country' => true,
                'phone' => true,
                'fax' => true,
                'email' => true,
                'url' => true
            )), $atts));

            if (get_post_type() == 'wpsl_stores') {
                if (empty($atts['id'])) {
                    if (isset($post->ID)) {
                        $atts['id'] = $post->ID;
                    } else {
                        return;
                    }
                }
            } else if (empty($atts['id'])) {
                return __('If you use the [wpsl_address] shortcode outside a store page you need to set the ID attribute.', 'wpsl');
            }

            $content = '<div class="wpsl-locations-details">';

//            if ( $atts['name'] && $store_name = get_the_title( $atts['id'] ) ) {
//                $content .= '<span><strong>' . esc_html( $store_name ) . '</strong></span>';
//            }

            $content .= '<div class="wpsl-location-address">';

            $address_format = explode('_', $wpsl_settings['address_format']);
            $count = count($address_format);
            $i = 1;

            // Loop over the address parts to make sure they are shown in the right order.
            foreach ($address_format as $address_part) {

                // Make sure the shortcode attribute is set to true for the $address_part, and it's not the 'comma' part.
                if ($address_part != 'comma' && $atts[$address_part]) {
                    $post_meta = get_post_meta($atts['id'], 'wpsl_' . $address_part, true);

                    if ($post_meta) {

                        /*
                         * Check if the next part of the address is set to 'comma'.
                         * If so add the, after the current address part, otherwise just show a space
                         */
                        if (isset($address_format[$i]) && ($address_format[$i] == 'comma')) {
                            $punctuation = ', ';
                        } else {
                            $punctuation = ' ';
                        }

                        // If we have reached the last item add a <br /> behind it.
                        $br = ($count == $i) ? '<br />' : '';

                        $content .= '<span><span class="wpsl-location-address-label" >Город: </span>' . esc_html($post_meta) . $punctuation . '</span><br/>' . $br;
                    }
                }

                $i++;
            }

            if ($atts['address'] && $store_address = get_post_meta($atts['id'], 'wpsl_address', true)) {
                $content .= '<span><span class="wpsl-location-address-label" >Адрес: </span>' . esc_html($store_address) . '</span><br/>';
            }

            /*if ($atts['address2'] && $store_address2 = get_post_meta($atts['id'], 'wpsl_address2', true)) {
                $content .= '<span><span class="wpsl-location-address-label" >Дополнительный адрес: </span>' . esc_html($store_address2) . '</span><br/>';
            }*/


            /*if ($atts['country'] && $store_country = get_post_meta($atts['id'], 'wpsl_country', true)) {
                $content .= '<span>' . esc_html($store_country) . '</span>';
            }*/

            $content .= '</div>';

            // If either the phone, fax, email or url is set to true, then add the wrap div for the contact details.
            if ($atts['phone'] || $atts['fax'] || $atts['email'] || $atts['url']) {
                $content .= '<div class="wpsl-contact-details">';

                if ($atts['phone'] && $store_phone = get_post_meta($atts['id'], 'wpsl_phone', true)) {
                    $content .= '<span class="wpsl-location-address-label" >' . esc_html($wpsl->i18n->get_translation('phone_label', __('Phone', 'wpsl'))) . '</span>: <span>' . esc_html($store_phone) . '</span><br/>';
                }

                if ($atts['fax'] && $store_fax = get_post_meta($atts['id'], 'wpsl_fax', true)) {
                    $content .= '<span class="wpsl-location-address-label" >' . esc_html($wpsl->i18n->get_translation('fax_label', __('Fax', 'wpsl'))) . '</span>: <span>8-800-555-2834</span><br/>';
                }

                if ($atts['email'] && $store_email = get_post_meta($atts['id'], 'wpsl_email', true)) {
                    $content .= '<span class="wpsl-location-address-label" >' . esc_html($wpsl->i18n->get_translation('email_label', __('Email', 'wpsl'))) . '</span>: <span>' . sanitize_email($store_email) . '</span><br/>';
                }

                if ($atts['url'] && $store_url = get_post_meta($atts['id'], 'wpsl_url', true)) {
                    $new_window = ($wpsl_settings['new_window']) ? 'target="_blank"' : '';
                    $content .= '<span class="wpsl-location-address-label" >' . esc_html($wpsl->i18n->get_translation('url_label', __('Url', 'wpsl'))) . '</span>: <a ' . $new_window . ' href="' . esc_url($store_url) . '" rel="nofollow">' . esc_url($store_url) . '</a><br/>';
                }

                $content .= '</div>';
            }

            $content .= '</div>';

            return $content;
        }

        /**
         * Handle the [wpsl_hours] shortcode.
         *
         * @since 2.0.0
         * @param  array $atts Shortcode attributes
         * @return void|string $output The opening hours
         */
        public function show_opening_hours($atts)
        {

            global $post;

            $hide_closed = apply_filters('wpsl_hide_closed_hours', false);

            $atts = wpsl_bool_check(shortcode_atts(apply_filters('wpsl_hour_shortcode_defaults', array(
                'id' => '',
                'hide_closed' => $hide_closed
            )), $atts));

            if (get_post_type() == 'wpsl_stores') {
                if (empty($atts['id'])) {
                    if (isset($post->ID)) {
                        $atts['id'] = $post->ID;
                    } else {
                        return;
                    }
                }
            } else if (empty($atts['id'])) {
                return __('If you use the [wpsl_hours] shortcode outside a store page you need to set the ID attribute.', 'wpsl');
            }

            $opening_hours = get_post_meta($atts['id'], 'wpsl_hours');

            if ($opening_hours) {
                $output = $this->get_opening_hours($opening_hours[0], $atts['hide_closed']);

                return $output;
            }
        }

        /**
         * Handle the [wpsl_map] shortcode.
         *
         * @since 2.0.0
         * @param  array $atts Shortcode attributes
         * @return string $output The html for the map
         */
        public function show_store_map($atts)
        {

            global $wpsl_settings, $post;

            $atts = shortcode_atts(apply_filters('wpsl_map_shortcode_defaults', array(
                'id' => '',
                'category' => '',
                'width' => '',
                'height' => $wpsl_settings['height'],
                'zoom' => $wpsl_settings['zoom_level'],
                'map_type' => $wpsl_settings['map_type'],
                'map_type_control' => $wpsl_settings['type_control'],
                'map_style' => '',
                'street_view' => $wpsl_settings['streetview'],
                'scrollwheel' => $wpsl_settings['scrollwheel'],
                'control_position' => $wpsl_settings['control_position']
            )), $atts);

            array_push($this->load_scripts, 'wpsl_base');

            if (get_post_type() == 'wpsl_stores') {
                if (empty($atts['id'])) {
                    if (isset($post->ID)) {
                        $atts['id'] = $post->ID;
                    } else {
                        return;
                    }
                }
            } else if (empty($atts['id']) && empty($atts['category'])) {
                return __('If you use the [wpsl_map] shortcode outside a store page, then you need to set the ID or category attribute.', 'wpsl');
            }

            if ($atts['category']) {
                $store_ids = get_posts(array(
                    'numberposts' => -1,
                    'post_type' => 'wpsl_stores',
                    'post_status' => 'publish',
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'wpsl_store_category',
                            'field' => 'slug',
                            'terms' => explode(',', sanitize_text_field($atts['category']))
                        ),
                    ),
                    'fields' => 'ids'
                ));
            } else {
                $store_ids = array_map('absint', explode(',', $atts['id']));
                $id_count = count($store_ids);
            }

            /*
             * The location url is included if:
             * 
             * - Multiple ids are set.
             * - The category attr is set.
             * - The shortcode is used on a post type other then 'wpsl_stores'. No point in showing a location
             * url to the user that links back to the page they are already on.
             */
            if ($atts['category'] || isset($id_count) && $id_count > 1 || get_post_type() != 'wpsl_stores' && !empty($atts['id'])) {
                $incl_url = true;
            } else {
                $incl_url = false;
            }

            $store_meta = array();
            $i = 0;

            foreach ($store_ids as $store_id) {
                $lat = get_post_meta($store_id, 'wpsl_lat', true);
                $lng = get_post_meta($store_id, 'wpsl_lng', true);

                // Make sure the latlng is numeric before collecting the other meta data.
                if (is_numeric($lat) && is_numeric($lng)) {
                    $store_meta[$i] = apply_filters('wpsl_cpt_info_window_meta_fields', array(
                        'store' => get_the_title($store_id),
                        'address' => get_post_meta($store_id, 'wpsl_address', true),
                        'address2' => get_post_meta($store_id, 'wpsl_address2', true),
                        'city' => get_post_meta($store_id, 'wpsl_city', true),
                        'state' => get_post_meta($store_id, 'wpsl_state', true),
                        'zip' => get_post_meta($store_id, 'wpsl_zip', true),
                        'country' => get_post_meta($store_id, 'wpsl_country', true)
                    ), $store_id);

                    // Grab the permalink / url if necessary.
                    if ($incl_url) {
                        if ($wpsl_settings['permalinks']) {
                            $store_meta[$i]['permalink'] = get_permalink($store_id);
                        } else {
                            $store_meta[$i]['url'] = get_post_meta($store_id, 'wpsl_url', true);
                        }
                    }

                    $store_meta[$i]['lat'] = $lat;
                    $store_meta[$i]['lng'] = $lng;
                    $store_meta[$i]['id'] = $store_id;

                    $i++;
                }
            }

            $output = '<div id="wpsl-base-gmap_' . self::$map_count . '" class="wpsl-gmap-canvas"></div>' . "\r\n";

            // Make sure the shortcode attributes are valid.
            $map_styles = $this->check_map_shortcode_atts($atts);

            if ($map_styles) {
                if (isset($map_styles['css']) && !empty($map_styles['css'])) {
                    $output .= '<style>' . $map_styles['css'] . '</style>' . "\r\n";
                    unset($map_styles['css']);
                }

                if ($map_styles) {
                    $store_data['shortCode'] = $map_styles;
                }
            }

            $store_data['locations'] = $store_meta;

            $this->store_map_data[self::$map_count] = $store_data;

            self::$map_count++;

            return $output;
        }

        /**
         * Make sure the map style shortcode attributes are valid.
         *
         * The values are send to wp_localize_script in add_frontend_scripts.
         *
         * @since 2.0.0
         * @param  array $atts The map style shortcode attributes
         * @return array $map_atts Validated map style shortcode attributes
         */
        public function check_map_shortcode_atts($atts)
        {

            $map_atts = array();

            if (isset($atts['width']) && is_numeric($atts['width'])) {
                $width = 'width:' . $atts['width'] . 'px;';
            } else {
                $width = '';
            }

            if (isset($atts['height']) && is_numeric($atts['height'])) {
                $height = 'height:' . $atts['height'] . 'px;';
            } else {
                $height = '';
            }

            if ($width || $height) {
                $map_atts['css'] = '#wpsl-base-gmap_' . self::$map_count . ' {' . $width . $height . '}';
            }

            if (isset($atts['zoom']) && !empty($atts['zoom'])) {
                $map_atts['zoomLevel'] = wpsl_valid_zoom_level($atts['zoom']);
            }

            if (isset($atts['map_type']) && !empty($atts['map_type'])) {
                $map_atts['mapType'] = wpsl_valid_map_type($atts['map_type']);
            }

            if (isset($atts['map_type_control'])) {
                $map_atts['mapTypeControl'] = $this->shortcode_atts_boolean($atts['map_type_control']);
            }

            if (isset($atts['map_style']) && $atts['map_style'] == 'default') {
                $map_atts['mapStyle'] = '';
            }

            if (isset($atts['street_view'])) {
                $map_atts['streetView'] = $this->shortcode_atts_boolean($atts['street_view']);
            }

            if (isset($atts['scrollwheel'])) {
                $map_atts['scrollWheel'] = $this->shortcode_atts_boolean($atts['scrollwheel']);
            }

            if (isset($atts['control_position']) && !empty($atts['control_position']) && ($atts['control_position'] == 'left' || $atts['control_position'] == 'right')) {
                $map_atts['controlPosition'] = $atts['control_position'];
            }

            return $map_atts;
        }

        /**
         * Set the shortcode attribute to either 1 or 0.
         *
         * @since 2.0.0
         * @param  string $att The shortcode attribute val
         * @return int    $att_val Either 1 or 0
         */
        public function shortcode_atts_boolean($att)
        {

            if ($att === 'true' || absint($att)) {
                $att_val = 1;
            } else {
                $att_val = 0;
            }

            return $att_val;
        }

        /**
         * Make sure the filter contains a valid value, otherwise use the default value.
         *
         * @since 2.0.0
         * @return string $filter_value The filter value
         */
        public function check_store_filter($filter)
        {

            if (isset($_GET[$filter]) && absint($_GET[$filter])) {
                $filter_value = $_GET[$filter];
            } else {
                $filter_value = $this->get_default_filter_value($filter);
            }

            return $filter_value;
        }

        /**
         * Get the default selected value for a dropdown.
         *
         * @since 1.0.0
         * @param  string $type The request list type
         * @return string $response The default list value
         */
        public function get_default_filter_value($type)
        {

            $settings = get_option('wpsl_settings');
            $list_values = explode(',', $settings[$type]);

            foreach ($list_values as $k => $list_value) {

                // The default radius has a [] wrapped around it, so we check for that and filter out the [].
                if (strpos($list_value, '[') !== false) {
                    $response = filter_var($list_value, FILTER_SANITIZE_NUMBER_INT);
                    break;
                }
            }

            return $response;
        }

        /**
         * Check if we have a opening day that has an value, if not they are all set to closed.
         *
         * @since 2.0.0
         * @param  array $opening_hours The opening hours
         * @return boolean True if a day is found that isn't empty
         */
        public function not_always_closed($opening_hours)
        {

            foreach ($opening_hours as $hours => $hour) {
                if (!empty($hour)) {
                    return true;
                }
            }
        }

        /**
         * Create the css rules based on the height / max-width that is set on the settings page.
         *
         * @since 1.0.0
         * @return string $css The custom css rules
         */
        public function get_custom_css()
        {

            global $wpsl_settings;

            $thumb_size = $this->get_store_thumb_size();

            $css = '<style>' . "\r\n";

            if (isset($thumb_size[0]) && is_numeric($thumb_size[0]) && isset($thumb_size[1]) && is_numeric($thumb_size[1])) {
                $css .= "\t" . "#wpsl-stores .wpsl-store-thumb {height:" . esc_attr($thumb_size[0]) . "px !important; width:" . esc_attr($thumb_size[1]) . "px !important;}" . "\r\n";
            }

            if ($wpsl_settings['template_id'] == 'below_map' && $wpsl_settings['listing_below_no_scroll']) {
                $css .= "\t" . "#wpsl-gmap {height:" . esc_attr($wpsl_settings['height']) . "px !important;}" . "\r\n";
                $css .= "\t" . "#wpsl-stores, #wpsl-direction-details {height:auto !important;}";
            } else {
                $css .= "\t" . "#wpsl-stores, #wpsl-direction-details, #wpsl-gmap {height:" . esc_attr($wpsl_settings['height']) . "px !important;}" . "\r\n";
            }

            /* 
             * If the category dropdowns are enabled then we make it 
             * the same width as the search input field. 
             */
            if ($wpsl_settings['category_filter'] && $wpsl_settings['category_filter_type'] == 'dropdown' || isset($this->sl_shortcode_atts['category_filter_type']) && $this->sl_shortcode_atts['category_filter_type'] == 'dropdown') {
                $cat_elem = ',#wpsl-category .wpsl-dropdown';
            } else {
                $cat_elem = '';
            }

            $css .= "\t" . "#wpsl-gmap .wpsl-info-window {max-width:" . esc_attr($wpsl_settings['infowindow_width']) . "px !important;}" . "\r\n";
            $css .= "\t" . ".wpsl-input label, #wpsl-radius label, #wpsl-category label {width:" . esc_attr($wpsl_settings['label_width']) . "px;}" . "\r\n";
            $css .= "\t" . "#wpsl-search-input " . $cat_elem . " {width:" . esc_attr($wpsl_settings['search_width']) . "px;}" . "\r\n";
            $css .= '</style>' . "\r\n";

            return $css;
        }

        /**
         * Collect the CSS classes that are placed on the outer store locator div.
         *
         * @since 2.0.0
         * @return string $classes The custom CSS rules
         */
        public function get_css_classes()
        {

            global $wpsl_settings;

            $classes = array();

            if ($wpsl_settings['category_filter'] && $wpsl_settings['results_dropdown'] && !$wpsl_settings['radius_dropdown']) {
                $classes[] = 'wpsl-cat-results-filter';
            } else if ($wpsl_settings['category_filter'] && ($wpsl_settings['results_dropdown'] || $wpsl_settings['radius_dropdown'])) {
                $classes[] = 'wpsl-filter';
            }
            // checkboxes class toevoegen?
            if (!$wpsl_settings['category_filter'] && !$wpsl_settings['results_dropdown'] && !$wpsl_settings['radius_dropdown']) {
                $classes[] = 'wpsl-no-filters';
            }

            if ($wpsl_settings['category_filter'] && $wpsl_settings['category_filter_type'] == 'checkboxes') {
                $classes[] = 'wpsl-checkboxes-enabled';
            }

            if ($wpsl_settings['results_dropdown'] && !$wpsl_settings['category_filter'] && !$wpsl_settings['radius_dropdown']) {
                $classes[] = 'wpsl-results-only';
            }

            $classes = apply_filters('wpsl_template_css_classes', $classes);

            if (!empty($classes)) {
                return join(' ', $classes);
            }
        }

        /**
         * Create a dropdown list holding the search radius or
         * max search results options.
         *
         * @since 1.0.0
         * @param  string $list_type The name of the list we need to load data for
         * @return string $dropdown_list A list with the available options for the dropdown list
         */
        public function get_dropdown_list($list_type)
        {

            global $wpsl_settings;

            $dropdown_list = '';
            $settings = explode(',', $wpsl_settings[$list_type]);

            // Only show the distance unit if we are dealing with the search radius.
            if ($list_type == 'search_radius') {
                $distance_unit = ' ' . esc_attr(wpsl_get_distance_unit());
            } else {
                $distance_unit = '';
            }

            foreach ($settings as $index => $setting_value) {

                // The default radius has a [] wrapped around it, so we check for that and filter out the [].
                if (strpos($setting_value, '[') !== false) {
                    $setting_value = filter_var($setting_value, FILTER_SANITIZE_NUMBER_INT);
                    $selected = 'selected="selected" ';
                } else {
                    $selected = '';
                }

                $dropdown_list .= '<option ' . $selected . 'value="' . absint($setting_value) . '">' . absint($setting_value) . $distance_unit . '</option>';
            }

            return $dropdown_list;
        }

        /**
         * Create the category filter.
         *
         * @todo create another func that accepts a meta key param to generate
         * a dropdown with unique values. So for example create_filter( 'restaurant' ) will output a
         * filter with all restaurant types. This can be used in a custom theme template.
         *
         * @since 2.0.0
         * @return string|void $category The HTML for the category dropdown, or nothing if no terms exist.
         */
        public function create_category_filter()
        {

            global $wpsl, $wpsl_settings;

            /*
             * If the category attr is set on the wpsl shortcode, then
             * there is no need to ouput an extra category dropdown.
             */
            if (isset($this->sl_shortcode_atts['js']['categoryIds'])) {
                return;
            }

            $terms = get_terms('wpsl_store_category');

            if (count($terms) > 0) {

                // Either use the shortcode atts filter type or the one from the settings page.
                if (isset($this->sl_shortcode_atts['category_filter_type'])) {
                    $filter_type = $this->sl_shortcode_atts['category_filter_type'];
                } else {
                    $filter_type = $wpsl_settings['category_filter_type'];
                }

                // Check if we need to show the filter as checkboxes or a dropdown list
                if ($filter_type == 'checkboxes') {
                    if (isset($this->sl_shortcode_atts['checkbox_columns'])) {
                        $checkbox_columns = absint($this->sl_shortcode_atts['checkbox_columns']);
                    }

                    if (isset($checkbox_columns) && $checkbox_columns) {
                        $column_count = $checkbox_columns;
                    } else {
                        $column_count = 3;
                    }

                    $category = '<ul id="wpsl-checkbox-filter" class="wpsl-checkbox-' . $column_count . '-columns">';

                    foreach ($terms as $term) {
                        $category .= '<li>';
                        $category .= '<label>';
                        $category .= '<input type="checkbox" value="' . esc_attr($term->term_id) . '" ' . $this->set_selected_category($filter_type, $term->term_id) . ' />';
                        $category .= esc_html($term->name);
                        $category .= '</label>';
                        $category .= '</li>';
                    }

                    $category .= '</ul>';
                } else {
                    $category = '<div id="wpsl-category">' . "\r\n";
                    $category .= '<label for="wpsl-category-list">' . esc_html($wpsl->i18n->get_translation('category_label', __('Category', 'wpsl'))) . '</label>' . "\r\n";

                    $args = apply_filters('wpsl_dropdown_category_args', array(
                            'show_option_none' => $wpsl->i18n->get_translation('category_default_label', __('Any', 'wpsl')),
                            'option_none_value' => '0',
                            'orderby' => 'NAME',
                            'order' => 'ASC',
                            'echo' => 0,
                            'selected' => $this->set_selected_category($filter_type),
                            'hierarchical' => 1,
                            'name' => 'wpsl-category',
                            'id' => 'wpsl-category-list',
                            'class' => 'wpsl-dropdown',
                            'taxonomy' => 'wpsl_store_category',
                            'hide_if_empty' => true
                        )
                    );

                    $category .= wp_dropdown_categories($args);

                    $category .= '</div>' . "\r\n";
                }

                return $category;
            }
        }

        /**
         * Set the selected category item.
         *
         * @since 2.1.2
         * @todo maybe add support in the future to make it check a query string for set cat?
         * @return string|void $category The ID of the selected option.
         */
        public function set_selected_category($filter_type, $id = '')
        {

            $selected_id = isset($_REQUEST['wpsl-widget-categories']) ? (absint($_REQUEST['wpsl-widget-categories'])) : '';

            if ($selected_id) {

                /* 
                 * Based on the filter type, either return the ID of the selected category, 
                 * or check if the checkbox needs to be set to checked="checked.
                 */
                if ($filter_type == 'dropdown') {
                    return $selected_id;
                } else {
                    return checked($selected_id, $id, false);
                }
            }
        }

        /**
         * Create a filename with @2x in it for the selected marker color.
         *
         * So when a user selected green.png in the admin panel. The JS on the front-end will end up
         * loading green@2x.png to provide support for retina compatible devices.
         *
         * @since 1.0.0
         * @param  string $filename The name of the seleted marker
         * @return string $filename The filename with @2x added to the end
         */
        public function create_retina_filename($filename)
        {

            $filename = explode('.', $filename);
            $filename = $filename[0] . '@2x.' . $filename[1];

            return $filename;
        }

        /**
         * Get the default values for the max_results and the search_radius dropdown.
         *
         * @since 1.0.2
         * @return array $output The default dropdown values
         */
        public function get_dropdown_defaults()
        {

            global $wpsl_settings;

            $required_defaults = array(
                'max_results',
                'search_radius'
            );

            // Strip out the default values that are wrapped in [].
            foreach ($required_defaults as $required_default) {
                preg_match_all('/\[([0-9]+?)\]/', $wpsl_settings[$required_default], $match, PREG_PATTERN_ORDER);
                $output[$required_default] = (isset($match[1][0])) ? $match[1][0] : '25';
            }

            return $output;
        }

        /**
         * Load the required css styles.
         *
         * @since 2.0.0
         * @return void
         */
        public function add_frontend_styles()
        {

            global $wpsl_settings;

            /**
             * Check if we need to deregister other Google Maps scripts loaded
             * by other plugins, or the current theme?
             *
             * This in some cases can break the store locator map.
             */
            if ($wpsl_settings['deregister_gmaps']) {
                wpsl_deregister_other_gmaps();
            }

            $min = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

            wp_enqueue_style('wpsl-styles', WPSL_URL . 'css/styles' . $min . '.css', '', WPSL_VERSION_NUM);
        }

        /**
         * Get the HTML for the map controls.
         *
         * The '&#xe800;' and '&#xe801;' code is for the icon font from fontello.com
         *
         * @since 2.0.0
         * @return string The HTML for the map controls
         */
        public function get_map_controls()
        {

            global $wpsl_settings, $is_IE;

            $classes = array();

            if ($wpsl_settings['reset_map']) {
                $reset_button = '<div class="wpsl-icon-reset"><span>&#xe801;</span></div>';
            } else {
                $reset_button = '';
            }

            /* 
             * IE messes up the top padding for the icon fonts from fontello >_<.
             * 
             * Luckily it's the same in all IE version ( 8-11 ),
             * so adjusting the padding just for IE fixes it.
             */
            if ($is_IE) {
                $classes[] = 'wpsl-ie';
            }

            // If the street view option is enabled, then we need to adjust the right margin for the map control div.
            if ($wpsl_settings['streetview']) {
                $classes[] = 'wpsl-street-view-exists';
            }

            if (!empty($classes)) {
                $class = 'class="' . join(' ', $classes) . '"';
            } else {
                $class = '';
            }

            $map_controls = '<div id="wpsl-map-controls" ' . $class . '>' . $reset_button . '<div class="wpsl-icon-direction"><span>&#xe800;</span></div></div>';

            return apply_filters('wpsl_map_controls', $map_controls);
        }

        /**
         * The different geolocation errors.
         *
         * They are shown when the Geolocation API returns an error.
         *
         * @since 2.0.0
         * @return array $geolocation_errors
         */
        public function geolocation_errors()
        {

            $geolocation_errors = array(
                'denied' => __('The application does not have permission to use the Geolocation API.', 'wpsl'),
                'unavailable' => __('Location information is unavailable.', 'wpsl'),
                'timeout' => __('The geolocation request timed out.', 'wpsl'),
                'generalError' => __('An unknown error occurred.', 'wpsl')
            );

            return $geolocation_errors;
        }

        /**
         * Get the used marker properties.
         *
         * @since 2.1.0
         * @link https://developers.google.com/maps/documentation/javascript/3.exp/reference#Icon
         * @return array $marker_props The marker properties.
         */
        public function get_marker_props()
        {

            $marker_props = array(
                'scaledSize' => '24,35', // 50% of the normal image to make it work on retina screens.
                'origin' => '0,0',
                'anchor' => '12,35'
            );

            /*
             * If this is not defined, the url path will default to
             * the url path of the WPSL plugin folder + /img/markers/
             * in the wpsl-gmap.js.
             */
            if (defined('WPSL_MARKER_URI')) {
                $marker_props['url'] = WPSL_MARKER_URI;
            }

            return apply_filters('wpsl_marker_props', $marker_props);

        }

        /**
         * Get the URL to the admin-ajax.php
         *
         * @since 2.2.3
         * @return string $ajax_url URL to the admin-ajax.php possibly with the WPML lang param included.
         */
        public function get_ajax_url()
        {

            global $wpsl;

            $param = '';

            if ($wpsl->i18n->wpml_exists()) {
                $param = '?lang=' . ICL_LANGUAGE_CODE;
            }

            $ajax_url = admin_url('admin-ajax.php' . $param);

            return $ajax_url;
        }

        /**
         * Get the used travel direction mode.
         *
         * @since 2.2.8
         * @return string $travel_mode The used travel mode for the travel direcions
         */
        public function get_directions_travel_mode()
        {

            $default = 'driving';

            $travel_mode = apply_filters('wpsl_direction_travel_mode', $default);
            $allowed_modes = array('driving', 'bicycling', 'transit', 'walking');

            if (!in_array($travel_mode, $allowed_modes)) {
                $travel_mode = $default;
            }

            return strtoupper($travel_mode);
        }

        /**
         * Load the required JS scripts.
         *
         * @since 1.0.0
         * @return void
         */
        public function add_frontend_scripts()
        {

            global $wpsl_settings, $wpsl;

            // Only load the required js files on the store locator page or individual store pages.
            if (empty($this->load_scripts)) {
                return;
            }

            $min = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

            $dropdown_defaults = $this->get_dropdown_defaults();

            /**
             * Check if we need to deregister other Google Maps scripts loaded
             * by other plugins, or the current theme?
             *
             * This in some cases can break the store locator map.
             */
            if ($wpsl_settings['deregister_gmaps']) {
                wpsl_deregister_other_gmaps();
            }

            wp_enqueue_script('wpsl-gmap', ('https://maps.google.com/maps/api/js' . wpsl_get_gmap_api_params('browser_key') . ''), '', null, true);

            $base_settings = array(
                'storeMarker' => $this->create_retina_filename($wpsl_settings['store_marker']),
                'mapType' => $wpsl_settings['map_type'],
                'mapTypeControl' => $wpsl_settings['type_control'],
                'zoomLevel' => $wpsl_settings['zoom_level'],
                'startLatlng' => $wpsl_settings['start_latlng'],
                'autoZoomLevel' => $wpsl_settings['auto_zoom_level'],
                'scrollWheel' => $wpsl_settings['scrollwheel'],
                'controlPosition' => $wpsl_settings['control_position'],
                'url' => WPSL_URL,
                'markerIconProps' => $this->get_marker_props(),
                'storeUrl' => $wpsl_settings['store_url'],
                'maxDropdownHeight' => apply_filters('wpsl_max_dropdown_height', 300),
                'enableStyledDropdowns' => apply_filters('wpsl_enable_styled_dropdowns', true),
                'mapTabAnchor' => apply_filters('wpsl_map_tab_anchor', 'wpsl-map-tab'),
                'mapTabAnchorReturn' => apply_filters('wpsl_map_tab_anchor_return', false),
                'gestureHandling' => apply_filters('wpsl_gesture_handling', 'auto'),
                'directionsTravelMode' => $this->get_directions_travel_mode()
            );

            $locator_map_settings = array(
                'startMarker' => $this->create_retina_filename($wpsl_settings['start_marker']),
                'markerClusters' => $wpsl_settings['marker_clusters'],
                'streetView' => $wpsl_settings['streetview'],
                'autoComplete' => $wpsl_settings['autocomplete'],
                'autoLocate' => $wpsl_settings['auto_locate'],
                'autoLoad' => $wpsl_settings['autoload'],
                'markerEffect' => $wpsl_settings['marker_effect'],
                'markerStreetView' => $wpsl_settings['marker_streetview'],
                'markerZoomTo' => $wpsl_settings['marker_zoom_to'],
                'newWindow' => $wpsl_settings['new_window'],
                'resetMap' => $wpsl_settings['reset_map'],
                'directionRedirect' => $wpsl_settings['direction_redirect'],
                'phoneUrl' => $wpsl_settings['phone_url'],
                'moreInfoLocation' => $wpsl_settings['more_info_location'],
                'mouseFocus' => $wpsl_settings['mouse_focus'],
                'templateId' => $wpsl_settings['template_id'],
                'maxResults' => $dropdown_defaults['max_results'],
                'searchRadius' => $dropdown_defaults['search_radius'],
                'distanceUnit' => wpsl_get_distance_unit(),
                'geoLocationTimout' => apply_filters('wpsl_geolocation_timeout', 5000),
                'ajaxurl' => $this->get_ajax_url(),
                'mapControls' => $this->get_map_controls()
            );

            /*
             * If no results are found then by default it will just show the
             * "No results found" text. This filter makes it possible to show
             * a custom HTML block instead of the "No results found" text.
             */
            $no_results_msg = apply_filters('wpsl_no_results', '');

            if ($no_results_msg) {
                $locator_map_settings['noResults'] = $no_results_msg;
            }

            /*
             * If enabled, include the component filter settings.
             * @todo see https://developers.google.com/maps/documentation/javascript/releases#327
             * See https://developers.google.com/maps/documentation/javascript/geocoding#ComponentFiltering
             */
            if ($wpsl_settings['api_region'] && $wpsl_settings['api_geocode_component']) {
                $locator_map_settings['geocodeComponents'] = apply_filters('wpsl_geocode_components', array(
                    'country' => strtoupper($wpsl_settings['api_region'])
                ));
            }

            // If the marker clusters are enabled, include the js file and marker settings.
            if ($wpsl_settings['marker_clusters']) {
                wp_enqueue_script('wpsl-cluster', WPSL_URL . 'js/markerclusterer' . $min . '.js', '', WPSL_VERSION_NUM, true); //not minified version is in the /js folder

                $base_settings['clusterZoom'] = $wpsl_settings['cluster_zoom'];
                $base_settings['clusterSize'] = $wpsl_settings['cluster_size'];
            }

            // Check if we need to include the infobox script and settings.
            if ($wpsl_settings['infowindow_style'] == 'infobox') {
                wp_enqueue_script('wpsl-infobox', WPSL_URL . 'js/infobox' . $min . '.js', array('wpsl-gmap'), WPSL_VERSION_NUM, true); // Not minified version is in the /js folder

                $base_settings['infoWindowStyle'] = $wpsl_settings['infowindow_style'];
                $base_settings = $this->get_infobox_settings($base_settings);
            }

            // Include the map style.
            if (!empty($wpsl_settings['map_style'])) {
                $base_settings['mapStyle'] = strip_tags(stripslashes(json_decode($wpsl_settings['map_style'])));
            }

            wp_enqueue_script('wpsl-js', apply_filters('wpsl_gmap_js', WPSL_URL . 'js/wpsl-gmap' . $min . '.js'), array('jquery'), WPSL_VERSION_NUM, true);
            wp_enqueue_script('underscore');

            // Check if we need to include all the settings and labels or just a part of them.
            if (in_array('wpsl_store_locator', $this->load_scripts)) {
                $settings = wp_parse_args($base_settings, $locator_map_settings);
                $template = 'wpsl_store_locator';
                $labels = array(
                    'preloader' => $wpsl->i18n->get_translation('preloader_label', __('Searching...', 'wpsl')),
                    'noResults' => $wpsl->i18n->get_translation('no_results_label', __('No results found', 'wpsl')),
                    'moreInfo' => $wpsl->i18n->get_translation('more_label', __('More info', 'wpsl')),
                    'generalError' => $wpsl->i18n->get_translation('error_label', __('Something went wrong, please try again!', 'wpsl')),
                    'queryLimit' => $wpsl->i18n->get_translation('limit_label', __('API usage limit reached', 'wpsl')),
                    'directions' => $wpsl->i18n->get_translation('directions_label', __('Directions', 'wpsl')),
                    'noDirectionsFound' => $wpsl->i18n->get_translation('no_directions_label', __('No route could be found between the origin and destination', 'wpsl')),
                    'startPoint' => $wpsl->i18n->get_translation('start_label', __('Start location', 'wpsl')),
                    'back' => $wpsl->i18n->get_translation('back_label', __('Back', 'wpsl')),
                    'streetView' => $wpsl->i18n->get_translation('street_view_label', __('Street view', 'wpsl')),
                    'zoomHere' => $wpsl->i18n->get_translation('zoom_here_label', __('Zoom here', 'wpsl'))
                );

                wp_localize_script('wpsl-js', 'wpslLabels', $labels);
                wp_localize_script('wpsl-js', 'wpslGeolocationErrors', $this->geolocation_errors());
            } else {
                $template = '';
                $settings = $base_settings;
            }

            // Check if we need to overwrite JS settings that are set through the [wpsl] shortcode.
            if ($this->sl_shortcode_atts && isset($this->sl_shortcode_atts['js'])) {
                foreach ($this->sl_shortcode_atts['js'] as $shortcode_key => $shortcode_val) {
                    $settings[$shortcode_key] = $shortcode_val;
                }
            }

            wp_localize_script('wpsl-js', 'wpslSettings', apply_filters('wpsl_js_settings', $settings));

            wpsl_create_underscore_templates($template);

            if (!empty($this->store_map_data)) {
                $i = 0;

                foreach ($this->store_map_data as $map) {
                    wp_localize_script('wpsl-js', 'wpslMap_' . $i, $map);

                    $i++;
                }
            }
        }

        /**
         * Get the infobox settings.
         *
         * @since 2.0.0
         * @see http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/docs/reference.html
         * @param  array $settings The plugin settings used on the front-end in js
         * @return array $settings The plugin settings including the infobox settings
         */
        public function get_infobox_settings($settings)
        {

            $infobox_settings = apply_filters('wpsl_infobox_settings', array(
                'infoBoxClass' => 'wpsl-infobox',
                'infoBoxCloseMargin' => '2px', // The margin can be written in css style, so 2px 2px 4px 2px for top, right, bottom, left
                'infoBoxCloseUrl' => '//www.google.com/intl/en_us/mapfiles/close.gif',
                'infoBoxClearance' => '40,40',
                'infoBoxDisableAutoPan' => 0,
                'infoBoxEnableEventPropagation' => 0,
                'infoBoxPixelOffset' => '-52,-45',
                'infoBoxZindex' => 1500
            ));

            foreach ($infobox_settings as $infobox_key => $infobox_setting) {
                $settings[$infobox_key] = $infobox_setting;
            }

            return $settings;
        }
    }

    new WPSL_Frontend();
}