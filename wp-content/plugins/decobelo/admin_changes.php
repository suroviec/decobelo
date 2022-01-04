<?php
add_action( 'admin_enqueue_scripts', 'load_admin_style' );
function load_admin_style() {
    wp_enqueue_style( 'admin_css', WP_PLUGIN_URL . '/decobelo/style/admin.css', false, '1.0.0' );
}