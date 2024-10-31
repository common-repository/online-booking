<?php
/**
 * Plugin Name: Online Booking and Appointment Scheduling by CozyCal
 * Plugin URI: https://www.cozycal.com/?utm_source=wordpress
 * Description: This plugin adds CozyCal's online scheduling to your website.
 * Version: 2.9.8
 * Author: CozyCal
 * Author URI: https://www.cozycal.com/?utm_source=wordpress
 * License: GPL2
 */


register_deactivation_hook(__FILE__, 'cozycal_remove');
add_action('admin_init', 'cozycal_admin_settings');
add_action('admin_menu', 'cozycal_options_menu');
add_action('admin_bar_menu', 'cozycal_admin_bar_menu', 1000);
add_action('admin_enqueue_scripts', 'cozycal_admin_notice');
add_action('wp_head', 'cozycal_embed');
$plugin = plugin_basename( __FILE__ );
add_filter("plugin_action_links_$plugin", 'cozycal_add_settings_link');
add_shortcode('cozycal_button', 'cozycal_button_code');


function cozycal_settings_page_url() {
    // Note: we don't use menu_page_url() as it is undefined
    // within cozycal_admin_bar_menu().
    // 
    // For more details, see:
    // https://plugintests.com/plugins/online-booking/2.6
    return admin_url('options-general.php?page=online-booking');
}


function cozycal_add_settings_link($links) {
    // Add settings link to plugin list page
    $settings_link = '<a href="' . cozycal_settings_page_url() . '">Settings</a>';
    array_push($links, $settings_link);
    return $links;
}


function cozycal_remove() {
    // Remove database entries
    delete_option('cozycal_embed_code');
    delete_option('cozycal_hide_notice');

    // Legacy (< 2.0) options
    delete_option('cozycal_installed');
    delete_option('cozycal_page_id');
    delete_option('cozycal_widget_enabled');
}


function cozycal_admin_settings() {
    // Creates database entries
    add_option('cozycal_embed_code');
    add_option('cozycal_hide_notice');
    
    // For form use
    register_setting('cozycal', 'cozycal_page_id'); // leave in for < 2.0 migrations
    register_setting('cozycal', 'cozycal_embed_code');
    register_setting('cozycal', 'cozycal_hide_notice');
}


function cozycal_options_menu() {
    add_options_page(
        'CozyCal Settings', // Page title
        'CozyCal', // Menu title
        'activate_plugins', // Capability
        'online-booking', // Menu slug
        'cozycal_settings_page' // Callback
    );
}


function cozycal_admin_bar_menu($wp_admin_bar) {
    // https://kb.detlus.com/articles/wordpress/add-admin-bar-menu-with-icon/
    $svg_logo = '<svg style="width: 17px; height: 17px;" 
        viewBox="0 0 196 196" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <path d="M186.8125,196 L9.1875,196 C4.134375,196 0,191.865625 0,186.8125 L0,9.1875 C0,4.134375 4.134375,0 9.1875,0 L186.8125,0 C191.865625,0 196,4.134375 196,9.1875 L196,186.8125 C196,191.865625 191.865625,196 186.8125,196 L186.8125,196 Z" fill="#C66470"></path>
            <path d="M56.3005604,74.6812746 C59.1676148,67.8428231 63.0585592,61.9851671 67.9735098,57.1081319 C72.8884608,52.2310963 78.6224836,48.4938593 85.1757516,45.8963077 C91.7290191,43.2987562 98.6917616,42 106.064188,42 C111.900691,42 117.09715,42.583115 121.653719,43.7493627 C126.210288,44.9156102 130.12683,46.2408718 133.403464,47.7251869 C137.192072,49.4215468 140.468656,51.2769128 143.233316,53.2913408 L127.106215,82.7124396 C125.467898,81.4401696 123.676016,80.220929 121.730515,79.0546812 C119.989803,78.2065014 117.916339,77.3848392 115.510061,76.5896704 C113.103783,75.7945015 110.364763,75.3969231 107.292919,75.3969231 C104.11868,75.3969231 101.200472,75.980038 98.5382068,77.1462858 C95.8759417,78.3125332 93.5464951,79.9028471 91.5497963,81.9172746 C89.5530975,83.9317025 87.9916,86.2906677 86.8652571,88.9942419 C85.7389145,91.6978157 85.1757516,94.5868857 85.1757516,97.6615385 C85.1757516,100.736191 85.7645127,103.625261 86.942053,106.328835 C88.1195933,109.032409 89.7322874,111.391374 91.7801833,113.405802 C93.8280795,115.42023 96.2343213,117.010544 98.9989812,118.176791 C101.763641,119.343039 104.784242,119.926154 108.060876,119.926154 C111.33751,119.926154 114.255718,119.50207 116.815588,118.65389 C119.375459,117.80571 121.525717,116.851522 123.266429,115.791297 C125.314325,114.625049 127.106207,113.246777 128.64213,111.65644 L144.769231,141.077538 C142.004571,143.410034 138.727986,145.477441 134.939378,147.279824 C131.662745,148.764139 127.720604,150.142411 123.112838,151.414681 C118.505071,152.686952 113.231818,153.323077 107.292919,153.323077 C99.3061242,153.323077 91.9338087,151.97131 85.1757516,149.267736 C78.417694,146.564162 72.5812779,142.773914 67.6663269,137.896879 C62.7513764,133.019844 58.9116285,127.162188 56.146969,120.323736 C53.3823091,113.485285 52,105.931294 52,97.6615385 C52,89.1797376 53.4335057,81.5197265 56.3005604,74.6812746 L56.3005604,74.6812746 Z" fill="#FFFFFF"></path>
        </g>
    </svg>';

    if (current_user_can('manage_options')) {
        $wp_admin_bar->add_menu(array(
            'id'    => 'online-booking',
            'title' => '<span class="ab-icon">' . $svg_logo . '</span>' . 'CozyCal',
            'href'  => cozycal_settings_page_url()
        ));
    }
}


function cozycal_admin_notice($hook_suffix) {
    $hide_notice = get_option('cozycal_hide_notice');
    $installed = get_option('cozycal_embed_code');
    $on_cozycal_page = strpos($hook_suffix, 'online-booking');
    
    if ($hide_notice) {
        return;
    }

    // Show notice when plugin is first installed
    if (!$installed && !$on_cozycal_page) {
        add_action('admin_notices', 'cozycal_admin_notice_html');
    }
}


function cozycal_admin_notice_html() {
    ?>
    <div class="updated notice" style="position:relative">
        <form method="post" action="options.php">
            <?php settings_fields('cozycal'); ?>
            <input type="hidden" name="cozycal_hide_notice" value="1"/>
            <p>
                Online Booking by CozyCal installed. 
                &nbsp;&nbsp;
                <a class="button button-primary" href="<?php echo cozycal_settings_page_url() ?>">
                    Setup plugin
                </a> 
            </p>
            <button type="submit" class="notice-dismiss">
                <span class="screen-reader-text">Dismiss this notice.</span>
            </button>
        </form>
    </div>
    <?php
}


function cozycal_settings_page() {
    ?>
    <h2>CozyCal Scheduling</h2>

    <form method="POST" action="options.php" class="wrap">
        <?php settings_fields('cozycal'); ?>

        <?php if (
            get_option('cozycal_page_id') != '' && 
            get_option('cozycal_embed_code') == ''): ?>
            <script>
                // Migrate legacy (version < 2.0) `page_id` based embed code to new embed code
                jQuery(function($) {
                    var url = 'https://cozycal.com/api/install/wordpress-code/' + 
                        '<?php echo get_option('cozycal_page_id') ?>';

                    // Use CozyCal's script migration endpoint to update embed code
                    $.get(url)
                        .done(function(response) {
                            $('textarea[name="cozycal_embed_code"]')
                                .val(response)
                                .closest('form')
                                .submit();
                        });
                });
            </script>
            <input type="hidden" name="cozycal_page_id" value="">
        <?php endif; ?>

        <div class="card" style="
            <?php echo get_option('cozycal_embed_code') ? '' : 'display:none'; ?>">

            <h3>💡Plugin Enabled!</h3>

            <p>
                CozyCal has been enabled on your site. 
                You can customize your buttons here:
            </p>
            <a class="button-primary"
                href="https://cozycal.com/app/buttons?utm_source=wordpress"
                target="_blank">
                Customize My Buttons
            </a>

            <p>
                ❤️ Happy with CozyCal? Please take a moment to 
                <a target="_blank" href="https://wordpress.org/support/plugin/online-booking/reviews/">leave us a review</a>.
            </p>
        </div>

        <div class="card">
            <h2>🛠 Plugin Setup</h2>

            <p class="help">
                A  <a href="https://www.cozycal.com/?utm_source=wordpress" target="_blank">CozyCal account</a> 
                is required to use this plugin.
            </p>

            <p>
                To install this plugin, paste the
                <a href="https://cozycal.com/app/welcome/wordpress-code"
                    target="_blank">Javascript code snippet</a> from CozyCal here:
            </p>

            <textarea 
                name="cozycal_embed_code"
                class="large-text code"
                style="width: 100%; height: 80px"><?php echo get_option('cozycal_embed_code'); ?></textarea>

            <p>
                <input class="button-primary" type="submit" value="<?php _e('Save Changes'); ?>" />
            </p>

            <p>
                🤔 Have questions?
                <a target="_blank" href="mailto:support@cozycal.com">Contact us here</a>.
            </p>
        </div>
    </form>
    <?php
}


function cozycal_embed() {
    echo "<!-- CozyCal WordPress Plugin -->\n";
    echo get_option('cozycal_embed_code');
    echo "<!-- end CozyCal WordPress Plugin -->\n";
}


function cozycal_button_code($atts) {
    $a = shortcode_atts(array(
        'class' => 'cozycal_button',
        'title' => 'Schedule an Appointment'
    ), $atts);
    return '<div class="js-cozycal-modal ' . $a['class'] . '">'
        . $a['title']
        . '</div>';
}


?>
