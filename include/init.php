<?php
// Block direct access
if (!defined('ABSPATH')) {
    exit;
}

// Scoped Admin Styles
function enqueue_mass_user_admin_styles($hook) {
    if ($hook !== 'toplevel_page_user-generator') {
        return;
    }

    wp_enqueue_style(
        'mass-user-generator-css', 
        plugin_dir_url(dirname(__FILE__)) . 'style.css', 
        array(), 
        '1.1'
    );
}
add_action('admin_enqueue_scripts', 'enqueue_mass_user_admin_styles');

// Dynamically injected Analytics
function add_analytics_code() {
    $ga_id = get_option('mug_ga_tracking_id', '');
    
    if (empty($ga_id)) {
        return;
    }
    ?>
    <!-- Mass User Generator Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($ga_id); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', '<?php echo esc_js($ga_id); ?>');
    </script>
    <?php
}
add_action('wp_footer', 'add_analytics_code');
