<?PHP
/**
*
*   GALERIA PRODUKTOW
*
*/

add_action( 'after_setup_theme', 'remove_gallery', 11 ); 
function remove_gallery() {
    remove_theme_support( 'wc-product-gallery-zoom' );
    remove_theme_support( 'wc-product-gallery-lightbox' );
    remove_theme_support( 'wc-product-gallery-slider' );
};


add_action('after_setup_theme', function(){
    
    add_filter( 'woocommerce_gallery_thumbnail_size', function( $size ) {
        return 'woocommerce_gallery_image_size';
    } );

    add_filter( 'woocommerce_gallery_image_size', function( $size ) {
        return 'woocommerce_gallery_full_size';
    } );
});



// product header

add_action( 'after_setup_theme', 'custom_product_header' ); 

function custom_product_header() {
    add_action('woocommerce_single_product_summary', 'start_product_float', 1);
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
    add_action('woocommerce_single_product_summary', 'display_collection', 7);
    add_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 5);
    add_action('woocommerce_single_product_summary', 'availability_status', 50);
    add_action('woocommerce_single_product_summary', 'choinka', 60);
    add_action('woocommerce_single_product_summary', 'end_product_float', 100);
    
};


function choinka() {
    ob_start();
    ?>

        <div class="choinka">
            <span>Przesyłka już od <b>8,99zł</b></span>
            <span>Darmowa dostawa od <b>100 zł</b></span>
            <span>Zapłać wygodnie </span> <img src="<?php echo get_template_directory_uri(); ?>/style/i/payu_alt.svg" /><img src="<?php echo get_template_directory_uri(); ?>/style/i/blik.svg" />
        </div>

    <?php
    echo ob_get_clean();
}

function start_product_float() {
    echo '<div class="product-float">';
    add_to_list_btn();
}

function availability_status() {
    
    global $product;
    $str = get_the_terms($product->get_id(), 'pa_dostepnosc')[0]->name;
    $availability = strtoupper(substr($str,0,1)) . substr($str, 1);

    if($str) {

        echo sprintf(
            '<div class="availability-info">
                <span><img src="%s"/> %s</span>
            </div>',
            get_template_directory_uri().'/style/i/box.svg',
            $availability
        );

    }


};

function end_product_float() {
    
    echo '</div>';
}

function display_collection() {
    global $product;
    if(get_the_terms($product->get_id(), 'kolekcje')) {
        echo sprintf(
            '<div class="collection">%s: <a href="%s">%s</a></div>',
            __('Kolekcja', 'decobelo'),
            get_term_link(get_the_terms($product->get_id(), 'kolekcje')[0]->term_id, 'kolekcje'),
            get_the_terms($product->get_id(), 'kolekcje')[0]->name
        );
    }; 
};

// ANCHOR product bottom

add_action( 'after_setup_theme', 'custom_product_bottom' ); 

function custom_product_bottom() {
    add_filter( 'woocommerce_product_tabs', '__return_empty_array', 98 );
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
    //add_action('woocommerce_before_single_product_summary', 'woocommerce_output_product_data_tabs', 30 );
    add_action('woocommerce_after_single_product_summary', 'custom_product_hook', 10 );
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
    remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
    add_action('custom_related_products', 'woocommerce_upsell_display', 10 );
}

function custom_product_hook() {
    echo '<div class="product-bottom">';
        
        echo '<div class="product-data">';
        
            echo '<div class="desc">';
                wc_get_template( 'single-product/tabs/description.php' );
            echo '</div>';

            echo '<div class="dodatkowe">';
                render_dodatkowe();
            echo '</div>';

            echo '<div class="attrs">';
                echo '<h2>' . __('Cechy produktu', 'decobelo') . '</h2>';
                    echo '<div class="terms">';
                        product_attrs();
                    echo '</div>';
            echo '</div>';

            echo '<div class="ikony">';
                render_ikony();
            echo '</div>';
            
            comments_template();
            
            do_action('custom_related_products');
        
            echo '</div>';
        
    echo '</div>';
}

function render_ikony() {
    global $product;

    $ikony = get_post_meta($product->get_id(), 'ikony')[0];

    $options = get_option('ikony');

    if($ikony) {

        echo '<h2>' . __('Konserwacja', 'decobelo') . '</h2>';

        if(count($ikony) == 1) {

            foreach($ikony as $slug => $data) {

              foreach($data['values'] as $ikona) {

                echo sprintf(
                    '<img src="%s" title="%s">',
                    wp_get_attachment_image_url($options[$ikona]['img']),
                    $options[$ikona]['title']
                );

              } 

            };

        } else {

            echo '<div class="g">';

            foreach($ikony as $slug => $data) {

                echo '<div>';

                echo '<span><b>' . $data['title'] . '</b></span>';

                    foreach($data['values'] as $ikona) {
    
                        echo sprintf(
                            '<img src="%s" title="asdasd">',
                            wp_get_attachment_image_url($options[$ikona]['img']),
                            $options[$ikona]['title']
                        );
    
                    } ;

                echo '</div>';
  
              };

              echo '</div>';

        };

        foreach($ikony as $slug => $data) {

            $option_data = get_option('ikony')[$slug];

            echo sprintf(
                '<h2>%s</h2>%s',
                $option_data['title'],
                $option_data['content']
            );
        };
    };


}


function render_dodatkowe() {

    global $product;

    $dodatkowe = get_post_meta($product->get_id(), 'dodatkowe')[0];

    if($dodatkowe) {

        foreach($dodatkowe as $slug) {

            $option_data = get_option('dodatkowe')[$slug];

            echo sprintf(
                '<h2>%s</h2>%s',
                $option_data['title'],
                $option_data['content']
            );
        };
    };


}

function product_attrs() {
    global $product;
    $attrs = $product->get_attributes();

    if(!$attrs) {
        return;
    }

    foreach ($attrs as $attr_slug => $attr_data) {

        if($attr_data['visible'] == true) {

            $tax = $attr_data->get_taxonomy();

            $name = get_taxonomy($tax)->labels->singular_name;

            $r_terms = $attr_data->get_options();

            $terms = array();

            foreach ($r_terms as $term_id) {
                $terms[] = get_term($term_id, $tax)->name;
            }
            
            $terms_arr = implode(',',$terms);

            
            echo sprintf(
                '<span><b>%s: </b>%s</span>',
                $name,
                $terms_arr
            );
             

        };

    }
};

/// skrypty glightbox dodane w addons.php

add_action( 'wp_footer', 'converts_product_attributes_from_select_options_to_div' );
function converts_product_attributes_from_select_options_to_div() {

    ?>
        <script type="text/javascript">

            jQuery(function($){

                // clones select options for each product attribute
                var clone = $(".single-product div.product table.variations select").clone(true,true);

                // adds a "data-parent-id" attribute to each select option
                $(".single-product div.product table.variations select option").each(function(){
                    $(this).attr('data-parent-id',$(this).parent().attr('id'));
                });

                // converts select options to div
                $(".single-product div.product table.variations select option").unwrap().each(function(){
                    if ( $(this).val() == '' ) {
                        $(this).remove();
                        return true;
                    }
                    var option = $('<div class="custom_option is-visible" data-parent-id="'+$(this).data('parent-id')+'" data-value="'+$(this).val()+'">'+$(this).text()+'</div>');
                    $(this).replaceWith(option);
                });
                
                // reinsert the clone of the select options of the attributes in the page that were removed by "unwrap()"
                $(clone).insertBefore('.single-product div.product table.variations .reset_variations').hide();

                // when a user clicks on a div it adds the "selected" attribute to the respective select option
                $(document).on('click', '.custom_option', function(){
                    var parentID = $(this).data('parent-id');
                    if ( $(this).hasClass('on') ) {
                        $(this).removeClass('on');
                        $(".single-product div.product table.variations select#"+parentID).val('').trigger("change");
                    } else {
                        $('.custom_option[data-parent-id='+parentID+']').removeClass('on');
                        $(this).addClass('on');
                        $(".single-product div.product table.variations select#"+parentID).val($(this).data("value")).trigger("change");
                    }
                    
                });

                // if a select option is already selected, it adds the "on" attribute to the respective div
                $(".single-product div.product table.variations select").each(function(){
                    if ( $(this).find("option:selected").val() ) {
                        var id = $(this).attr('id');
                        $('.custom_option[data-parent-id='+id+']').removeClass('on');
                        var value = $(this).find("option:selected").val();
                        $('.custom_option[data-parent-id='+id+'][data-value='+value+']').addClass('on');
                    }
                });

                // when the select options change based on the ones selected, it shows or hides the respective divs
                $('body').on('check_variations', function(){
                    $('div.custom_option').removeClass('is-visible');
                    $('.single-product div.product table.variations select').each(function(){
                        var attrID = $(this).attr("id");
                        $(this).find('option').each(function(){
                            if ( $(this).val() == '' ) {
                                return;
                            }
                            $('div[data-parent-id="'+attrID+'"][data-value="'+$(this).val()+'"]').addClass('is-visible');
                        });
                    });
                });

            });

        </script>
    <?php

}