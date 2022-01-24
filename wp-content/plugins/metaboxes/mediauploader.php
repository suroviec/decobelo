<?php


function load_media_files() {
    wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'load_media_files' );

function mediauploader_js() {
	wp_enqueue_script( 'custom-js', plugins_url( 'metaboxes/mediauploader.js' , dirname(__FILE__) ), array( 'jquery' ) );
}
add_action('admin_enqueue_scripts', 'mediauploader_js');