<?php 


// ANCHOR akceptacja regulaminu

add_action( 'regulamin_hook', 'checkbox_regulamin' );

function checkbox_regulamin() {

    woocommerce_form_field( 'regulamin', array(
        'type'          => 'checkbox',
        'class'         => array('input-checkbox'),
        'label'         => __('Zapoznałam/em się z <b><a href="'.get_permalink(122).'">regulaminem sklepu</a></b>.', 'decobelo'),
        'required'  => true,
        ), WC()->checkout->get_value( 'regulamin' ));
}

add_action('woocommerce_checkout_process', 'check_regulamin');

function check_regulamin() {
    if ( ! $_POST['regulamin'] )
        wc_add_notice( __( 'Prosimy o zapoznanie się z regulaminem i jego akceptację.', 'decobelo' ), 'error' );
}


// TODO pole dla wyboru paczkomatu

add_action('wp_head', 'checkout_inpost_js');

function checkout_inpost_js() {

    if(!is_checkout()) {
        return;
    }   
    ob_start();
    ?>

<script async src="https://geowidget.easypack24.net/js/sdk-for-javascript.js"></script>
<link rel="stylesheet" href="https://geowidget.easypack24.net/css/easypack.css"/>
    <?php

    $output = ob_get_clean();
    echo $output;
};

add_filter( 'woocommerce_update_order_review_fragments', 'inpost_box', 10, 1 );
function inpost_box( $fragments ) {

    $check = array('flat_rate:7', 'free_shipping:10');

    if(in_array($_POST['shipping_method'][0], $check) == false) {

        $fragments['#inpost'] = '<div id="inpost"></div>';

    } else {

        ob_start();

        ?>

        <div id="inpost" style="padding-top:1rem">
        
            <span></span>
            <a href="" class="smallbtn1">Wybierz paczkomat</a>
            <input type="hidden" name="paczkomat" value="">        
        </div>

        <script type="text/javascript">
            
            var inpost = document.querySelector('#inpost a');
            var mapcon = document.querySelector('#map');
            var cover = document.querySelector('#cover');         

            inpost.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector('#map-widget').style.display = "block";
                mapcon.classList.add('active');    
                cover.classList.add('white');
                cover.classList.add('active');
            });

            /**
            document.querySelector('#map .close').addEventListener('click', ()=> {
                document.querySelector('#map-widget').style.display = "none";
            });
             */

        </script>
    
    <?php
        
    $fragments['#inpost'] = ob_get_clean();	
    }

    return $fragments;

}

add_action('wp_footer', 'inpost_update_js');

function inpost_update_js() {
    
    if(!is_checkout()) {
        return;
    }    
    ob_start();
    ?>

        <script>

            jQuery( document ).ajaxStop(function() {

                jQuery( ".wc_payment_methods li" ).on( "input", function() {
                    jQuery(document.body).trigger("update_checkout");
                });
            });

        </script>

    <?php
    $output = ob_get_clean();
    echo $output;
}

add_action('woocommerce_checkout_process', 'inpost_check');

function inpost_check() {
    
    $check = array('flat_rate:7', 'free_shipping:10');
    if ( ! $_POST['paczkomat'] && (in_array($_POST['shipping_method'][0], $check) == true)) {
        wc_add_notice( __( 'Prosimy o wybór paczkomatu.', 'decobelo' ), 'error' );
    }
        
}

add_action( 'woocommerce_checkout_update_order_meta', 'inpost_save', 10, 1 );
function inpost_save( $order_id ) {
    
    $check = array('flat_rate:7', 'free_shipping:10');
    
    if ( (! empty( $_POST['paczkomat'] )) && (in_array($_POST['shipping_method'][0], $check) == true)) {
        update_post_meta( $order_id, 'paczkomat', sanitize_text_field($_POST['paczkomat']) ) ;
    }
}

add_action( 'woocommerce_admin_order_data_after_shipping_address', 'inpost_display', 1, 1 );
function inpost_display( $order ){
    if ( $paczkomat  = $order->get_meta('paczkomat') ) {
        echo '<p><b>Paczkomat:</b> ' . $paczkomat. '.</p>';
    }
}


// ANCHOR wyłaczenie normalnego inpostu

function hide_shipping_when_free_is_available( $rates, $package ) {
	
    $subtotal = WC()->cart->get_cart_contents_total();
    if ($subtotal > 200) {
        unset( $rates['flat_rate:7'] );
    }
	return $rates;
};

add_filter( 'woocommerce_package_rates', 'hide_shipping_when_free_is_available', 10, 2 );



// ANCHOR dodatkowa oplata przy pobraniu

add_action( 'woocommerce_cart_calculate_fees', 'add_cod_fee', 20, 1 );
function add_cod_fee( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    $your_payment_id      = 'cod'; // The payment method
    $fee_amount           = 7; // The fee amount

    $chosen_payment_method_id  = WC()->session->get( 'chosen_payment_method' );
    $chosen_shipping_method_id = WC()->session->get( 'chosen_shipping_methods' )[0];
    $chosen_shipping_method    = explode( ':', $chosen_shipping_method_id )[0];

    if ( ($chosen_payment_method_id == $your_payment_id) && ($chosen_shipping_method !== "local_pickup")) {
        $fee_text = __( 'Pobranie', 'decobelo' );
        $cart->add_fee( $fee_text, $fee_amount, false );
    }
}


add_action( 'wp_footer', 'refresh_checkout_script' );
function refresh_checkout_script() {
    // Only on checkout page
    if( is_checkout() && ! is_wc_endpoint_url('order-received') ) :
    ?>
    <script type="text/javascript">
    jQuery(function($){
        // On payment method change
        $('form.woocommerce-checkout').on( 'change', 'input[name="payment_method"]', function(){
            // Refresh checkout
            $('body').trigger('update_checkout');
        });
    })
    </script>
    <?php
    endif;
}


// ANCHOR pytanie o fakture

add_action('invoice_ask', 'invoice_checkbox', 0);

function invoice_checkbox() {

    woocommerce_form_field( 'invoice', array(
        'type'          => 'checkbox',
        'class'         => array('input-checkbox'),
        'label'         => __('Proszę o wystawienie faktury.', 'decobelo'),
        'required'  => false,
        ), WC()->checkout->get_value( 'invoice' ));

    woocommerce_form_field( 'nip', array(
        'type'          => 'number',
        'class'         => array('input-checkbox'),
        'label'         => __('NIP', 'decobelo'),
        'required'  => false,
        ), WC()->checkout->get_value( 'nip' ));
}

add_action('woocommerce_checkout_process', 'check_nip');

function check_nip() {
    if ( ($_POST['invoice']) && (($_POST['nip']) == "") )
        wc_add_notice( __( 'Prosimy o podanie NIP.', 'decobelo' ), 'error' );
}

add_action( 'woocommerce_checkout_update_order_meta', 'save_invoice', 10, 1 );
function save_invoice( $order_id ) {
    if ( ! empty( $_POST['invoice'] ) ) {
        update_post_meta( $order_id, 'faktura', esc_attr('tak') ) ;
    }
    if ( ! empty( $_POST['nip'] ) ) {
        update_post_meta( $order_id, 'nip', sanitize_text_field($_POST['nip']) ) ;
    }
}

add_action( 'woocommerce_admin_order_data_after_billing_address', 'display_inovice', 1, 1 );
function display_inovice( $order ){
    if ( $invoice  = $order->get_meta('faktura') ) {
        echo '<p><b>Uwaga:</b> Klient prosi o fakturę.</p>';
        echo '<p><b>Numer NIP:</b>' . $order->get_meta('nip') . '</p>';
    }
};


// ANCHOR usuniecie opcjonalnie z inputow

add_filter( 'woocommerce_form_field' , 'opcjonalnie', 10, 4 );

function opcjonalnie( $field, $key, $args, $value ) {
    if( is_checkout() && ! is_wc_endpoint_url() ) {
        $optional = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
        $field = str_replace( $optional, '', $field );
    }
return $field;
} 
