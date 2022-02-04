<?php
/**
 * Plugin Name: Decobelo dodatki
 * Description: Dodatkowe opcje strony głównej, ...
 * Version:     1.0.0.
 * Author:      Marcin Surowiec
 * License:     GPLv2 or later
 * Text Domain: decobelo-dodatki
 */
/*
add_action( 'add_meta_boxes', function() {
    add_meta_box( 'start_cat_metabox', 'Kolekcje na stronie głównej', 'start_cat_metabox', 'page', 'advanced', 'high' );
});
*/

require('addons.php');
require('admin_changes.php');
require('ajax/koszyk.php');
require('ajax/lista.php');
require('ajax/single-product.php');
require('ajax/variable-product.php');
require('products/products-list.php');
require('products/next-page.php');
require('kolekcje.php');
require('klasy.php');
require('frontpage.php');
require('contactpage.php');
require('seo.php');
require('cart.php');
require('checkout.php');
require('single-product/single-product.php');
require('dodatkowe.php');
require('ikony.php');
require('email.php');
require('tracking_number.php');
require('hide_shipping.php');