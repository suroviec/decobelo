<?php
add_action('woocommerce_before_single_product', 'remove_woocommerce_template_single_add_to_cart');

function remove_woocommerce_template_single_add_to_cart()
{
    global $product;

    if ($product->is_type('simple')) {
        remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
    }
}

add_action('woocommerce_before_single_product', 'add_woocommerce_template_loop_add_to_cart');

function add_woocommerce_template_loop_add_to_cart()
{
    global $product;

    if ($product->is_type('simple')) {
        add_action('woocommerce_single_product_summary', 'woocommerce_template_loop_add_to_cart', 30);
    }
}
