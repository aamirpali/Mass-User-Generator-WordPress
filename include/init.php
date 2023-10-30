<?php

// Enqueue Bootstrap CSS and custom CSS for the admin area
function enqueue_admin_styles() {
    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
    wp_enqueue_style('custom-css', plugin_dir_url(__FILE__) . 'style.css');
}

// Enqueue your admin styles
add_action('admin_enqueue_scripts', 'enqueue_admin_styles');

function add_analytics_code() {
    ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=YOUR_GA_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'YOUR_GA_ID');
    </script>
    <?php
}
add_action('wp_footer', 'add_analytics_code');