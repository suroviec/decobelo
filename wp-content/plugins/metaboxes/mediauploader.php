<?php
function mediaploader_js() {
	wp_enqueue_script( 'custom-js', plugins_url( 'metaboxes/mediauploader.js' , dirname(__FILE__) ), array( 'jquery' ) );
}
add_action('admin_enqueue_scripts', 'mediaploader_js');