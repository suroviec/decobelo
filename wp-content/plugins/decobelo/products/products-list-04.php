<?php

add_action("wp_ajax_send_products", 'send_products');
add_action("wp_ajax_nopriv_send_products", 'send_products');

remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

function send_products() {
    
    if ( !wp_verify_nonce( $_REQUEST['nonce'], 'products')) {
        exit;
    } 

    $query = $_REQUEST['query'];

    // lista aktywnych filtrow

    $active_filters = array();

    if($query['attrs']) {
        foreach ($query['attrs'] as $term_slug => $term_data) {
            foreach ($term_data['values'] as $term_value) {
                $active_filters[] = array(
                    'term_slug'     => $term_slug,
                    'tax_name'     => $term_data['title'],
                    'term_value'    => $term_value
                );
            };
        };
    };

    $second_terms = array('product_cat', 'kolekcje');

    foreach($second_terms as $second_term) {

        if($query['secondTerm'][$second_term]) {
            foreach ($query['secondTerm'][$second_term]['values'] as $term) {
                $active_filters[] = array(
                    'term_slug'     => $query['secondTerm'][$second_term]['type'],
                    'tax_name'     => $query['secondTerm'][$second_term]['title'],
                    'term_value'    => $term['value'],
                    'term_name'     => $term['name']
                );
            };
        };
    }

    

    // lista produktow

    ob_start();
    ?>
        <?php products_list($query); ?>
        
    <?php
    
    $result['products'] = ob_get_clean();

    $updated_products = products_for_update($query);

    // TODO utworzenie listy filtrow z products;

    $result['available'] = json_encode(get_updated_filters($updated_products));

    $result['type'] = 'success';
    
    $result['active'] = json_encode($active_filters);
    $result = json_encode($result);
    echo $result;
    die;
}
 
function get_current_filters($args) {

    global $woocommerce;

    $current_tax = $args['current_tax'];
    $current_slug = $args['current_slug'];
    $search = $args['search'];
    
    if(!function_exists('wc_get_products')) {
		return;
	}

	$paged                   = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
	$ordering                = WC()->query->get_catalog_ordering_args();
	$ordering['orderby']     = array_shift(explode(' ', $ordering['orderby']));
	$ordering['orderby']     = stristr($ordering['orderby'], 'price') ? 'meta_value_num' : $ordering['orderby'];
	$products_per_page       = apply_filters('loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());

    $current_cat = get_term_by('slug', $current_slug, $current_tax);
    $current_id = $current_cat->term_id;

    if($current_tax) {

        $products       = wc_get_products(array(
            'status'    => 'publish',
            'limit'     => $products_per_page,
            'page'      => $paged,
            'paginate'  => true,
            'orderby'   => $ordering['orderby'],
            'order'     => $ordering['order'],
            'tax_query' => array(
                    array(
                        'taxonomy' 			=> $current_tax,
                        'field'				=> 'slug',
                        'terms'				=> $current_slug,
                        'include_children'	=> true	
                    )
            )
        ));

    } elseif($search) {

        $products       = wc_get_products(array(
            's'         => $search,
            'status'    => 'publish',
            'limit'     => $products_per_page,
            'page'      => $paged,
            'paginate'  => true,
            'orderby'   => $ordering['orderby'],
            'order'     => $ordering['order'],
        ));

    } else {

        $products       = wc_get_products(array(
            'status'    => 'publish',
            'limit'     => $products_per_page,
            'page'      => $paged,
            'paginate'  => true,
            'orderby'   => $ordering['orderby'],
            'order'     => $ordering['order'],
        ));

    }
    
	

    $filters = array();

    if($products) {  

        foreach($products->products as $product) {

            // atrybuty
            
            $attributes = $product->get_attributes();   
            foreach($attributes as $slug => $data) {
                $filters['attributes'][$slug] = array(
                    'name'  => get_taxonomy($slug)->labels->singular_name,
                    'slug'  => $slug,
                    'terms' => explode(', ', $product->get_attribute($slug))
                );
            }

            

            if ($current_tax) { // dla widoku katalogu

                if($current_tax == 'product_cat') {

                    $filters['second_term']['tax'] = 'kolekcje';
                    $filters['second_term']['title'] = 'Kolekcja';
                    $collection = wc_get_product_terms( $product->get_id(), 'kolekcje' );
                    
                    if($collection[0]->name) {
                        $filters['second_term']['terms'][$collection[0]->slug] = array(
                            'name' => $collection[0]->name,
                            'slug' => $collection[0]->slug,
                        );
                    }

                } else { 
    
                    $filters['second_term']['tax'] = 'product_cat';
                    $filters['second_term']['title'] = 'Produkt';
                    $categories = wc_get_product_terms( $product->get_id(), 'product_cat' );
    
                    $subcategories = array();
    
                    foreach($categories as $category) {
                        $filters['second_term']['terms'][$category->slug] = array(
                            'name'  => $category->name,
                            'slug'  => $category->slug
                        );
                    };
    
                   
                };
            
            } else { 

                $filters['first_term']['tax'] = 'product_cat';
                $filters['first_term']['title'] = 'Produkt';
                $categories = wc_get_product_terms( $product->get_id(), 'product_cat' );

                $subcategories = array();

                foreach($categories as $category) {
                    $filters['first_term']['terms'][$category->slug] = array(
                        'name'  => $category->name,
                        'slug'  => $category->slug
                    );
                };

                $filters['second_term']['tax'] = 'kolekcje';
                $filters['second_term']['title'] = 'Kolekcja';
                $collection = wc_get_product_terms( $product->get_id(), 'kolekcje' );
                
                if($collection[0]->name) {
                    $filters['second_term']['terms'][$collection[0]->slug] = array(
                        'name' => $collection[0]->name,
                        'slug' => $collection[0]->slug,
                    );
                }


            }

            

                    
            

            // child cats

            if($current_tax == 'product_cat') {

                if(!empty(get_term_children($current_id, $current_tax))) {
                    $category = wc_get_product_terms( $product->get_id(), 'product_cat' );

                    if (($category[0]->parent !== 0) && ($category[0]->parent !== $category[0]->term_id)) {
                        
                        $filters['child_terms'][$category[0]->term_id] = array(
                            'name' => $category[0]->name,
                            'slug' => $category[0]->slug
                        );
                    }    
                }       
            }
        }
        wp_reset_postdata();
    
    } else {
    
        $filters = 'aaa';
    
    }

    $filters['current'] = array(
        'type' => $current_tax,
        'term' => $current_slug,
        'search' => $search
    );

    return $filters;

}

function render_filters($filters=null) {

    ob_start(); ?>

    <?php   
        echo sprintf(
            '<div class="filters" data-nonce="%s" data-current_type="%s" data-current_term="%s" data-search="%s">',
            wp_create_nonce('products'),
            $filters['current']['type'],
            $filters['current']['term'],
            $filters['current']['search']
        );
    ?>

    <?php //var_dump($filters); ?>
    
    <?php if($filters['child_terms']) : ?> 
        <div class="upper-filters">
            <div class="child-terms">
                <?php

                    $url =  "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
                    $escaped_url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
                    $url_suff = explode('?', $escaped_url)[1];

                    foreach($filters['child_terms'] as $id => $data) {
                        echo sprintf(
                            '<a href="%s" class="filter">%s</a>',
                            $url_suff ? get_term_link($id, 'product_cat'). '?' . $url_suff : get_term_link($id, 'product_cat'),
                            $data['name']
                        );
                    };
                ?>
            </div>
        </div>
    
    <?php endif; ?>  

        <div class="lower-filters">
            
            <div class="switcher"><?php _e('Filtry', 'decobelo') ?></div>

            <div class="filters-list">

           

                    <?php if($filters['second_term']) : ?>

                        <div class="second-term">
                            
                            <?php 
                                echo wp_sprintf(
                                    '<div><span>%s</span>',
                                    $filters['second_term']['tax'] == 'kolekcje' ? __('Kolekcja', 'decobelo') : __('Produkt', 'decobelo')
                                );

                                    echo sprintf(
                                        '<ul data-filter="%s" data-title="%s">',
                                        $filters['second_term']['tax'],
                                        $filters['second_term']['tax'] == 'kolekcje' ? __('Kolekcja', 'decobelo') : __('Produkt', 'decobelo')
                                    );

                                    foreach ($filters['second_term']['terms'] as $term) {
                                        echo wp_sprintf(
                                            '<li>
                                                <a href="%s" data-value="%s" data-type="%s" class="filter">%s</a>',
                                            $filters['second_term']['tax'] == 'kolekcje' ? add_query_arg('kolekcja', $term['slug']) : add_query_arg('kategoria', $term['slug']),
                                            $term['slug'],
                                            $filters['second_term']['tax'],
                                            $term['name']
                                        );

                                        if($term['categories']) {
                                            
                                            echo '<ul>';

                                            foreach($term['categories'] as $subcat) {
                                                echo sprintf(
                                                    '<li><a href="%s" class="filter" data-type="%s" data-value="%s">%s</a></li>',
                                                    $filters['second_term']['tax'] == 'kolekcje' ? add_query_arg('kolekcja', $term['slug']) : add_query_arg('kategoria', $term['slug']),
                                                    $filters['second_term']['tax'],
                                                    $subcat['slug'],
                                                    $subcat['name']
                                                );
                                            };

                                            echo '</li></ul>';

                                        };

                                    };

                                    echo '</li></ul>';

                                echo '</div>';

                            ?>

                        </div>

                    <?php endif ; ?>

                    <?php if($filters['attributes']) : ?>

                        <div class="attrs">

                            <?php foreach($filters['attributes'] as $attribute) {

                                $get_parameter = preg_replace('/pa/','filter',$attribute['slug']);
                                $parameter_raw = sanitize_text_field($_GET[$get_parameter]);
                                $parameter_arr = array_unique(explode(',',$parameter_raw));
                                
                                echo wp_sprintf(
                                    '<div><span>%s</span>',
                                    $attribute['name']
                                );

                                    echo wp_sprintf(
                                        '<ul data-filter="%s" data-title="%s">',
                                        $attribute['slug'],
                                        $attribute['name']
                                    );

                                    foreach ($attribute['terms'] as $term) {

                                        $attr_arr = array_diff($parameter_arr, array($term));
                                        sort($attr_arr);
                                        $parameter = implode(',',$attr_arr);

                                        if(in_array($term, $parameter_arr)) {
                                            $url = add_query_arg($get_parameter, $parameter);
                                        } else {
                                            if($parameter !== "") {
                                                $url = add_query_arg($get_parameter, $parameter. ',' . $term);
                                            } else {
                                                $url = add_query_arg($get_parameter, $term);
                                            };
                                        }
                                     
                                        
                                        
                                        echo wp_sprintf(

                                            '<li>
                                                <a href="%s" data-value="%s" data-type="%s" class="filter">%s</a>
                                            </li>',
                                            $url,
                                            $term,
                                            $attribute['slug'],
                                            $term
                                        );
                                    };

                                    echo '</ul>';

                                echo '</div>';    

                            }; ?>

                        </div>

                    <?php endif ?>

            </div>

        </div>

        <div class="active-filters">
            <span><?php _e('Aktywne filtry', 'decobelo'); ?>:</span>
        </div>

    </div>

    <?php

    $render = ob_get_clean();

    echo $render;

}

function products_list($filters=null) {

    global $woocommerce;

    if(!function_exists('wc_get_products')) {
		return;
	}

	$paged                   = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
	$ordering                = WC()->query->get_catalog_ordering_args();
	$ordering['orderby']     = array_shift(explode(' ', $ordering['orderby']));
	$ordering['orderby']     = stristr($ordering['orderby'], 'price') ? 'meta_value_num' : $ordering['orderby'];
	$products_per_page       = apply_filters('loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());

    $tax_data = array();

    if($filters['firstTerm']['values'][0]) {

        $terms = array();
        
        foreach ($filters['firstTerm']['values'] as $term) {
            $terms[] = $term['value'];
        }
        
        $tax_data[] = array(
            'taxonomy' 			=> $filters['firstTerm']['type'],
            'field'				=> 'slug',
            'terms'				=> $terms,
            'include_children'	=> true	
        );    
        
    };

    $second_cats = array('product_cat', 'kolekcje');

    foreach($second_cats as $second_cat) {

        if($filters['secondTerm'][$second_cat]['values'][0]) {

            $terms = array();
            
            foreach ($filters['secondTerm'][$second_cat]['values'] as $term) {
                $terms[] = $term['value'];
            }
            
            $tax_data[] = array(
                'taxonomy' 			=> $filters['secondTerm'][$second_cat]['type'],
                'field'				=> 'slug',
                'terms'				=> $terms,
                'include_children'	=> true	
            );    
        };
    }

    if($filters['attrs']) {

        foreach ($filters['attrs'] as $attr_slug => $attr_data) {
            
            $terms = array();

            if($attr_data['values'][0]) {

                foreach ($attr_data['values'] as $attr_value) {
                    $terms[] = $attr_value;
                }
                
                $tax_data[] = array(
                    'taxonomy' 			=> $attr_slug,
                    'field'				=> 'slug',
                    'terms'				=> $terms,
                    'include_children'	=> true	
                );       
                
            }
        };
        
    };

    if($filters['firstTerm']['type']) {
        $tax_data[] = array(
            'taxonomy' 			=> $filters['firstTerm']['type'],
            'field'				=> 'slug',
            'terms'				=> $filters['firstTerm']['value'],
            'include_children'	=> true	
        ); 
    }

    $tax_query = array(
        'relation' => 'AND',
        array(
            $tax_data
        )
    );
    
    if(!empty($tax_data)) {
        $products       = wc_get_products(array(
            's'         => $filters['search'],
            'meta_key'  => '_price',
            'status'    => 'publish',
            'limit'     => $products_per_page,
            'page'      => $paged,
            'paginate'  => true,
            'return'    => 'ids',
            'orderby'   => $ordering['orderby'],
            'order'     => $ordering['order'],
            'tax_query' => $tax_query
        ));
    } else {
        $products       = wc_get_products(array(
            's'         => $filters['search'],
            'meta_key'  => '_price',
            'status'    => 'publish',
            'limit'     => $products_per_page,
            'page'      => $paged,
            'paginate'  => true,
            'return'    => 'ids',
            'orderby'   => $ordering['orderby'],
            'order'     => $ordering['order'],
        ));
    }


    if($products) {    

        var_dump($filters);
        
        foreach($products->products as $product) {
        setup_postdata($GLOBALS['post'] =& $product);
        wc_get_template_part('content', 'product');
        }
        wp_reset_postdata();
    
        
    
    } else {
    
        do_action('woocommerce_no_products_found');
    
    }
}

function products_for_update($args=null) {

    global $woocommerce;

    $current_tax = $args['current_tax'];
    $current_slug = $args['current_slug'];
    $search = $args['search'];
    
    if(!function_exists('wc_get_products')) {
		return;
	}

	$paged                   = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
	$ordering                = WC()->query->get_catalog_ordering_args();
	$ordering['orderby']     = array_shift(explode(' ', $ordering['orderby']));
	$ordering['orderby']     = stristr($ordering['orderby'], 'price') ? 'meta_value_num' : $ordering['orderby'];
	$products_per_page       = apply_filters('loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());

    $current_cat = get_term_by('slug', $current_slug, $current_tax);
    $current_id = $current_cat->term_id;

    if($current_tax) {

        $products       = wc_get_products(array(
            'status'    => 'publish',
            'limit'     => $products_per_page,
            'page'      => $paged,
            'paginate'  => true,
            'orderby'   => $ordering['orderby'],
            'order'     => $ordering['order'],
            'tax_query' => array(
                    array(
                        'taxonomy' 			=> $current_tax,
                        'field'				=> 'slug',
                        'terms'				=> $current_slug,
                        'include_children'	=> true	
                    )
            )
        ));

    } elseif($search) {

        $products       = wc_get_products(array(
            's'         => $search,
            'status'    => 'publish',
            'limit'     => $products_per_page,
            'page'      => $paged,
            'paginate'  => true,
            'orderby'   => $ordering['orderby'],
            'order'     => $ordering['order'],
        ));

    } else {

        $products       = wc_get_products(array(
            'status'    => 'publish',
            'limit'     => $products_per_page,
            'page'      => $paged,
            'paginate'  => true,
            'orderby'   => $ordering['orderby'],
            'order'     => $ordering['order'],
        ));

    }

    if($products) {    
        
        return $products;
    
    } else {
    
        return false;
    
    }
}

function get_updated_filters($products = null) {

    $filters = array();

    if($products) {  
        
        foreach($products->products as $product) {


            // atrybuty
            
            $attributes = $product->get_attributes();   
            foreach($attributes as $slug => $data) {
                $terms = explode(', ', $product->get_attribute($slug));
                foreach($terms as $term) {
                    $filters[] = array(
                        'type'  => $slug,
                        'value' => $term
                    );
                }
            }

            // kolekcja lub kategorie

            
                $collection = wc_get_product_terms( $product->get_id(), 'kolekcje' );
                if($collection[0]->name) {
                    $filters[] = array(
                        'type'  => 'kolekcje',
                        'value' => $collection[0]->slug
                    );
                }
            
                $categories = wc_get_product_terms( $product->get_id(), 'product_cat' );
                $terms = array();
                foreach ($categories as $category) {
                    $filters[] = array(
                        'type'  => 'product_cat',
                        'value' => $category->slug
                    );
                }; 
            
        };
        wp_reset_postdata();
    
    } else {
        $filters = 'empty';
    }

    return $filters;

}

