<?php

add_action( 'wp_enqueue_scripts', 'decobelo_script' );

function decobelo_script() {
    if(is_product()) {
        wp_enqueue_style( 'glightbox_css', WP_PLUGIN_URL . '/decobelo/style/glightbox.min.css', false, '1.0.0' );
        wp_enqueue_script( 'glightbox_js', WP_PLUGIN_URL . '/decobelo/js/glightbox.min.js', array(), _S_VERSION, true );
    }
    wp_enqueue_script( 'decobelo-script', WP_PLUGIN_URL . '/decobelo/js/decobelo.min.js', array('jquery'), '1.0.0' , true );
    wp_localize_script( 'decobelo-script', 'my_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    wp_enqueue_style( 'decobelo-slider', WP_PLUGIN_URL . '/decobelo/style/decobelo-slider.css', false, '1.0.0' );
}

add_filter( 'woocommerce_product_variation_title_include_attributes', 'custom_product_variation_title', 10, 2 );
function custom_product_variation_title($should_include_attributes, $product){
    $should_include_attributes = false;
    return $should_include_attributes;
}

add_action('wp_head', 'custom_ajax_spinner', 1000 );
function custom_ajax_spinner() {
    ?>
    <style>
    .woocommerce .blockUI.blockOverlay {
        background-color: transparent !important;
    }
    .woocommerce .blockUI.blockOverlay:before,
    .woocommerce .loader:before {
        height: 100%;
        width: 100%;
        position: absolute;
        top: 0%;
        left: 0%;
        display: block;
        content: "";
        -webkit-animation: none;
        -moz-animation: none;
        animation: none;
        background-color: white !important;
        trasition: 1s ease;
        opacity:1 !important;
        background-position: center center;
        background-size: cover;
        line-height: 1;
        text-align: center;
        font-size: 2em;
    }
    </style>
    <?php
}

// ANCHOR skrypty selecta krajow

add_action( 'wp_enqueue_scripts', function() {
    wp_dequeue_style( 'select2' );
    wp_dequeue_script( 'select2');
    wp_dequeue_script( 'selectWoo' );
}, 11 );