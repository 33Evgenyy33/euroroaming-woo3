<?php
/**
 * WooCommerce POS CSS Settings
 *
 * @author    Actuality Extensions
 * @package   WoocommercePointOfSale/Classes/settings
 * @category    Class
 * @since     0.1
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WC_POS_Admin_System_Status')) :

    /**
     * WC_POS_Admin_Settings_CSS
     */
    class WC_POS_Admin_System_Status extends WC_Settings_Page
    {
        private $last_update = array(
            'date' => '',
            'log' => array()
        );
        private $force_updates = array(
            '3.2.1' => 'wp-content/plugins/woocommerce-point-of-sale/includes/updates/wc_pos-update-3.2.1.php',
        );

        /**
         * Constructor.
         */
        public function __construct()
        {
            $this->id = 'system_status';
            $this->label = __('Status', 'wc_point_of_sale');

            add_filter('wc_pos_settings_tabs_array', array($this, 'add_settings_page'), 20);
            add_action('woocommerce_sections_' . $this->id, array($this, 'output_sections'));
            add_action('wc_pos_settings_' . $this->id, array($this, 'output'));
            add_action('wc_pos_settings_save_' . $this->id, array($this, 'save'));

        }

        /**
         * Get settings array
         *
         * @return array
         */
        public function get_settings()
        {
            $GLOBALS['hide_save_button'] = true;
            $update_status = __('OK', 'wc_point_of_sale');
            $last_update = get_option('wc_pos_last_force_db_update');
            $this->last_update = ($last_update) ? $last_update : $this->last_update;
            ?>
            <table class="widefat striped" style="margin-bottom: 1em;">
                <thead>
                <tr>
                    <th colspan="2">
                        <b><?php _e('WordPress Environment', 'wc_point_of_sale') ?></b>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td style="width: 30%;">
                        <?php _e('Site URL:', 'wc_point_of_sale') ?>
                    </td>
                    <td>
                        <?php echo get_site_url(); ?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%;">
                        <?php _e('WooCommerce Version:', 'wc_point_of_sale') ?>
                    </td>
                    <td>
                        <?php echo WC()->version; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php _e('WordPress Version:', 'wc_point_of_sale') ?>
                    </td>
                    <td>
                        <?php echo get_bloginfo('version'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php _e('Language:', 'wc_point_of_sale') ?>
                    </td>
                    <td>
                        <?php echo get_locale(); ?>
                    </td>
                </tr>
            </table>
            <table class="widefat striped" style="margin-bottom: 1em;">
                <thead>
                <tr>
                    <th colspan="2">
                        <b><?php _e('Server Environment', 'wc_point_of_sale') ?></b>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td style="width: 30%;">
                        <?php _e('Server Info:', 'wc_point_of_sale') ?>
                    </td>
                    <td>
                        <?php echo esc_html($_SERVER['SERVER_SOFTWARE']); ?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%;">
                        <?php _e('PHP Version:', 'wc_point_of_sale') ?>
                    </td>
                    <td>
                        <?php echo $php_version = phpversion(); ?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%;">
                        <?php _e('PHP Post Max Size:', 'wc_point_of_sale') ?>
                    </td>
                    <td>
                        <?php echo size_format(wc_let_to_num(ini_get('post_max_size'))); ?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%;">
                        <?php _e('PHP Time Limit:', 'wc_point_of_sale') ?>
                    </td>
                    <td>
                        <?php echo ini_get('max_execution_time'); ?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%;">
                        <?php _e('PHP Max Input Vars:', 'wc_point_of_sale') ?>
                    </td>
                    <td>
                        <?php echo ini_get('max_input_vars'); ?>
                    </td>
                </tr>
                <tr>
                    <td style="width: 30%;">
                        <?php _e('Max Upload Size:', 'wc_point_of_sale') ?>
                    </td>
                    <td>
                        <?php echo size_format(wp_max_upload_size()); ?>
                    </td>
                </tr>
            </table>
            <table class="widefat striped" style="margin-bottom: 1em;">
                <thead>
                <tr>
                    <th colspan="2">
                        <b><?php _e('Database', 'wc_point_of_sale') ?></b>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td style="width: 30%;">
                        <?php _e('Last Forced Update: ', 'wc_point_of_sale') ?>
                    </td>
                    <td>
                        <?php echo $this->last_update['date'] ?>
                    </td>
                </tr>
                <?php foreach ($this->last_update['log'] as $version => $result) { ?>
                    <tr>
                        <td>
                            <?php echo __('POS Database Version: ', 'wc_point_of_sale') ?>
                        </td>
                        <td>
                            <?php echo $version ?>
                        </td>
                    </tr>
                    <?php foreach ($result as $res) {
                        if ($res) {
                            $update_status = __('Updated', 'wc_point_of_sale');
                            break;
                        }
                    } ?>
                    <tr>
                        <td>
                            <?php echo __('Result:', 'wc_point_of_sale') ?>
                        </td>
                        <td>
                            <?php echo $update_status ?>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td>
                        <?php echo __('Database Update: ', 'wc_point_of_sale') ?>
                    </td>
                    <td>
                        <input name="save" class="button" type="submit"
                               value="<?php _e('Force Update', 'woocommerce'); ?>"/><br><span class="description"
                                                                                              style="margin-top: .5em; display: inline-block;"><?php echo __('This tool will update the database to the latest version - useful when settings are not being applied as per configured in settings, registers, receipts and outlets.', 'wc_point_of_sale') ?></span>
                    </td>
                </tr>
            </table>
            <table class="widefat striped api_settings" style="margin-bottom: 1em;">
                <thead>
                <tr>
                    <th colspan="2">
                        <b><?php _e('API', 'wc_point_of_sale') ?></b>
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td style="width: 30%;">
                        <?php _e('API Enabled:', 'wc_point_of_sale') ?>
                    </td>
                    <td>
                        <?php echo 'yes' === get_option('woocommerce_api_enabled') ? '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>' : '<mark class="no">&ndash;</mark>'; ?>
                    </td>
                </tr>
            </table>
            <input type="hidden" class="update-log" value="<?php var_export($this->last_update) ?>">
            <?php return $this->last_update;
        }

        /**
         * Save settings
         */
        public function save()
        {
            $last_update['date'] = date('Y-m-d H:i');
            foreach ($this->force_updates as $version => $update) {
                include(ABSPATH . $update);
                $last_update['log'][$version] = $result;
            }
            update_option('wc_pos_last_force_db_update', $last_update);
        }

    }

endif;

return new WC_POS_Admin_System_Status();
