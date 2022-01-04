<?php 

add_action( 'wp_footer', 'variation_script' );

function variation_script() {
    ob_start();

    ?>

        <script>

            // ANCHOR Ajax variable product

            jQuery('document').ready(function(){

                const btn = document.querySelector('.woocommerce-variation-add-to-cart button');

                if(!btn) {
                    return;
                }

                const data = {
                    productid: "",
                    variationid: "",
                    quantity: "",
                    attrs: []
                }

                btn.addEventListener('click', (e)=> {
                    
                    e.preventDefault();

                    data.productid = document.querySelector('input[name=product_id]').value;
                    data.variationid = document.querySelector('input[name=variation_id]').value;
                    data.quantity = document.querySelector('input.qty').value;

                    const attrBtns = document.querySelectorAll('.custom_option');

                    data.attrs = [];

                    attrBtns.forEach(function(btn){
                        if(btn.classList.contains('on')) {
                            data.attrs.push(
                            {
                                term: 'attributes_' + btn.getAttribute('data-parent-id'),
                                value: btn.getAttribute('data-value')
                            }
                            )
                        }
                    })

                    jQuery.ajax({
                        type : "post",
                        dataType : "json",
                        url : my_ajax.ajax_url,
                        data : {action: "variable", data: data},
                        
                        success: function(response) {
                            if(response == "success") {
                                setTimeout(function(){
                                    jQuery(document.body).trigger('added_to_cart').trigger('wc_fragment_refresh');
                                }, 500);
                            } else {
                                console.log(response);
                            }
                        }

                    });
                });
            });

        </script> 

    <?php 
    
    echo ob_get_clean();

}


// ANCHOR ajax php

add_action("wp_ajax_variable", 'add_variable_product_to_cart');
add_action("wp_ajax_nopriv_variable", 'add_variable_product_to_cart');

function add_variable_product_to_cart() {

        $data = $_REQUEST['data'];

        WC()->cart->add_to_cart( $data['productid'], $data['quantity'], $data['variationid'], $data['attrs'] );


        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result = json_encode('success');
            echo $result;
        } else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }    
    die;
}