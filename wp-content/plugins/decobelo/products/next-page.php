<?php

add_filter('woocommerce_after_shop_loop', 'load_products');

function load_products() {
    
    $total = wc_get_loop_prop( 'total' );
    $products_per_page = apply_filters('loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());
    
    if(($products_per_page >= $total) || ($total == 0)) { // drugi warunek dla promocji, promocja ma wbudowany loadmore w szablon
        return;
    }

    ob_start();

    ?>
    
    <div id="load-more">
        <button class="mainbtn">Więcej produktów</button>
    </div>

    <?php
    echo ob_get_clean();
}

// NOTE paginacja woocommerce usunieta w functions.php