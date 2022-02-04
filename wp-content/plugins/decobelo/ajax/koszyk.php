<?php
add_filter( 'woocommerce_add_to_cart_fragments', 'cart_count_update');
function cart_count_update($fragments){
    ob_start();
    ?>
    <div class="cart-count">
        <?php echo WC()->cart->get_cart_contents_count(); ?>
    </div>
    <?php
        $fragments['.cart-count'] = ob_get_clean();
    return $fragments;
}


add_filter( 'woocommerce_add_to_cart_fragments', 'mobile_cart_count_update');
function mobile_cart_count_update($fragments){
    ob_start();
    ?>
    <div class="cart-count-mobile">
        <?php echo WC()->cart->get_cart_contents_count(); ?>
    </div>
    <?php
        $fragments['.cart-count-mobile'] = ob_get_clean();
return $fragments;
}

remove_action('woocommerce_widget_shopping_cart_total', 'woocommerce_widget_shopping_cart_subtotal', 10 );

add_filter('woocommerce_add_to_cart_fragments', 'cart_list_update');

function cart_list_update($fragments) {
    ob_start();
    ?>
    <div class="cart-list">
        <?php woocommerce_mini_cart(); ?>
    </div>
    <?php $fragments['.cart-list'] = ob_get_clean();
        return $fragments;
}

add_filter('woocommerce_add_to_cart_fragments', 'mini_update');

function mini_update($fragments) {
    ob_start(); ?>

    <?php if(WC()->cart->get_cart_contents_count() > 0) : ?>

        <div class="mini-cart-bottom">
            <div>
                <p><b><?php _e('Kwota łącznie: ', 'decobelo') ;?></b><span class="mini-total"><?php echo WC()->cart->get_cart_total(); ?></span></p>
            </div>
            <div>
                <a href="<?php echo wc_get_checkout_url(); ?>" class="mainbtn"><?php _e('Złóż zamówienie', 'decobelo'); ?></a>
                <button class="mainbtn"><?php _e('Kontynuuj zakupy', 'decobelo'); ?></button>
            </div>
        </div>

    <?php else : ?>

        <div class="mini-cart-bottom"></div>

    <?php 
    
    endif;

    $fragments['.mini-cart-bottom'] = ob_get_clean();
    return $fragments;
}


// DODANIE NAZWY PRODUKTU DO PRZYCISKU

add_action( 'woocommerce_loop_add_to_cart_link', 'filter_wc_loop_add_to_cart_link', 10, 3 );
function filter_wc_loop_add_to_cart_link( $button_html, $product, $args ) {
    if( $product->supports( 'ajax_add_to_cart' ) ) {
        $search_string  = 'data-product_sku';
        $replace_string = sprintf(
            'data-product_name="%s" %s',
            $product->get_name(),
            $search_string
        );

        $button_html = str_replace($search_string, $replace_string, $button_html);
    }
    return $button_html;
}

add_action('wp_footer','custom_jquery_add_to_cart_script');
function custom_jquery_add_to_cart_script(){
        ?>
            <script type="text/javascript">

                    jQuery( document.body ).on( 'added_to_cart', function(event, fragments, cart_hash, button){

                  
                        if(!button) {
                            var name = document.querySelector('h1.product_title').textContent;
                        } else {
                            var name = button.data('product_name');
                        }
                        var minicart = document.querySelector('.cart-container');
                        var cover = document.querySelector('#cover');
                        minicart.classList.add('active');
                        cover.classList.add('active');
                    
                        let cartmsg = document.querySelector('#cart-msg');
                        cartmsg.classList.add('active');
                        cartmsg.innerHTML = "<span>Dodano " + name + " do koszyka</span>";
                        document.querySelector('.minicart-cont').style.gridTemplateRows = "auto 1fr 7rem 3rem";
                    });

                    jQuery('document').ready(function(){
                        document.querySelector('#cart-msg').classList.remove('active');
                    });
             
            </script>
        <?php
}