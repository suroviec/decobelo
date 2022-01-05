<?php

// ANCHOR new vars

function vars( $vars ) {

    $list_of_vars = list_of_vars();

    // lista produktow z list_of_vars
    foreach($list_of_vars as $var_name => $var_data) {
        $vars[] = $var_name;
    }

    // produkty nie sa przekazywane z list of vars
    $vars[] = 'produkty';

	return $vars;
}
add_filter( 'query_vars', 'vars' );

function list_of_vars($current_tax=null) {

    $taxonomies = get_taxonomies();

		$list_of_vars = array();

		foreach($taxonomies as $taxonomy_name => $taxonomy_value) {
			if(strpos($taxonomy_name, 'pa_') !== false) {
				$list_of_vars[str_replace('pa_', '', $taxonomy_name)] = array(
					'tax' => $taxonomy_name,
				);
			}
		}
    	if($current_tax == 'kolekcje') {
			$list_of_vars['produkty'] = array(
                'tax' => 'product_cat'
            );
		} else {
			$list_of_vars['kolekcje'] = array(
                'tax' => 'kolekcje'
            );
		}

        return $list_of_vars;
}




add_action("wp_ajax_send_products", 'send_products');
add_action("wp_ajax_nopriv_send_products", 'send_products');

remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

// SECTION Ajax
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

    if($query['onsale']['value'] == 'tak') {
        $active_filters[] = array(
            'term_slug'     => $query['onsale']['type'],
            'tax_name'      => $query['onsale']['title'],
            'term_value'    => $query['onsale']['value'],
            'term_name'     => $query['onsale']['value'],
        );
    }

    if(!empty($query['orderby'])) {
        $active_filters[] = array(
            'term_slug'     => $query['orderby']['type'],
            'tax_name'      => $query['orderby']['title'],
            'term_value'    => $query['orderby']['value'],
            'term_name'     => $query['orderby']['value'],
        );
    }

    

    // lista produktow

    $newproducts = products_list($query);

    ob_start();
    
    echo $newproducts['products'];     
    
    // $updated_products = products_for_update($query);

    // $result['available'] = json_encode(get_updated_filters($updated_products));

    // $result['update'] = json_encode($updated_products);

    $result['type'] = 'success';

    $output = ob_get_clean();

    $noproducts = sprintf(
        '<div class="noproducts">
            <span>%s</span>
            <a href="" class="mainbtn">%s</a>
        </div>',
        __('Brak produktów dla wybranych filtrów', 'decobelo'),
        __('Resetuj filtry', 'decobelo')
    );

    if($output !== '') {
        $result['products'] = $output;    
    } else {
        $result['products'] = $noproducts; 
    }
    
    $result['active'] = json_encode($active_filters);

    $result['count'] = json_encode($newproducts['count']);

    $result = json_encode($result);
    
    echo $result;
    
    die;

}
// !SECTION

// SECTION Get filters

function get_current_filters($args) {

    global $woocommerce;

    $current_tax = $args['current_tax'];
    $current_slug = $args['current_slug'];
    $search = $args['search'];
    $promocje = $args['promocje'];
    $onsale = $args['onsale'];
    
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
    
    $args = array(
        'status'    => 'publish',
        'limit'     => -1,
        'page'      => 1,
        'paginate'  => true,
        'orderby'   => $ordering['orderby'],
        'order'     => $ordering['order'],
    );

    if($current_tax) {

        $args['tax_query'] = array(
            array(
                'taxonomy' 			=> $current_tax,
                'field'				=> 'slug',
                'terms'				=> $current_slug,
                'include_children'	=> true
            )	
        );

    }
    
    if($search) {

        $args['s'] = $search;

    } 

    if ($promocje == 'tak') {
        $args['include'] = wc_get_product_ids_on_sale();
    }

    $products = wc_get_products($args);   

    $filters = array();

    if($products) {  


        foreach($products->products as $product) {

            // atrybuty
            
            $attributes = $product->get_attributes();   

            $filters['test'] = $args;

            foreach($attributes as $slug => $data) {

                $terms[$slug] = $product->get_attribute($slug);

                $filters['attributes'][$slug]['name'] = get_taxonomy($slug)->labels->singular_name;
                $filters['attributes'][$slug]['slug'] = $slug;

                if(strpos($terms[$slug], ',')) {
                    
                    $terms_arr = explode(', ',$terms[$slug]);
                    foreach ($terms_arr as $term) {

                        if(!is_array($filters['attributes'][$slug]['terms'])) {
                            $filters['attributes'][$slug]['terms'][] = $term; 
                        } elseif(!in_array($term, $filters['attributes'][$slug]['terms'])) {
                            $filters['attributes'][$slug]['terms'][] = $term; 
                        }
                        
                    }

                } else {

                    if(!is_array($filters['attributes'][$slug]['terms'])) {
                        $filters['attributes'][$slug]['terms'][]= $terms[$slug]; 
                    } elseif(!in_array($terms[$slug], $filters['attributes'][$slug]['terms'])) {
                        $filters['attributes'][$slug]['terms'][]= $terms[$slug]; 
                    }
                }; 
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

            // zmienione przez wc_get_terms

            /**

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

             */
            
        }
        wp_reset_postdata();

        
    
    } else {
    
        $filters = '';
    
    }

    if($current_tax) {

        $filters['child_terms'] = array();

        $current_id = get_term_by('slug', $current_slug, $current_tax)->term_id;

        $args = array(
            'taxonomy'      => $current_tax,
            'hide_empty'    => true,
            'child_of'      => $current_id
        );

        $childs = get_terms($args);   

        foreach($childs as $child) {
            $filters['child_terms'][] = array (
                'name'  => $child->name,
                'url'   => get_term_link($child->term_id, $current_tax)
            );  
        };

    }

    $filters['current'] = array(
        'type' => $current_tax,
        'term' => $current_slug,
        'search' => $search,
        'promocje' => $promocje
    );

    return $filters;

}
// !SECTION

function catch_filters($current_vars, $term, $value) {

    $check = 'test';

    foreach($current_vars as $filter_name => $var_data) {
        if(($var_data['tax'] == $term) && in_array($value, $var_data['values'])) {
            $check = 'selected';
        }
    }

    return $check;

};

// SECTION Render filters
function render_filters($filters=null, $current_vars=null) {

    ob_start(); ?>

<?php if($filters['child_terms']) : ?> 
        <div class="upper-filters">
            <div class="child-terms">
                <?php
                    foreach($filters['child_terms'] as $child) {
                        echo sprintf(
                            '<a href="%s" title="%s">%s</a>',
                            $child['url'],
                            $child['name'] . __(' - zobacz produkty z kategorii', 'decobelo'),
                            $child['name']
                        );
                    };
                ?>
            </div>
        </div>
    
    <?php endif; ?>  

    <?php   
        echo sprintf(
            '<div class="filters" data-nonce="%s" data-current_type="%s" data-current_term="%s" data-search="%s" data-promocje="%s">',
            wp_create_nonce('products'),
            $filters['current']['type'],
            $filters['current']['term'],
            $filters['current']['search'],
            $filters['current']['promocje']
        );
    ?>

    <?php //var_dump($filters); ?>


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
                                                <a href="%s" data-value="%s" data-type="%s" class="filter %s">%s</a>',
                                            $filters['second_term']['tax'] == 'kolekcje' ? add_query_arg('kolekcja', $term['slug']) : add_query_arg('kategoria', $term['slug']),
                                            $term['slug'],
                                            $filters['second_term']['tax'],
                                            catch_filters($current_vars, $filters['second_term']['tax'], $term['slug'],),
                                            $term['name']
                                        );

                                        if($term['categories']) {
                                            
                                            echo '<ul>';

                                            foreach($term['categories'] as $subcat) {
                                                echo sprintf(
                                                    '<li><a href="%s" class="filter %s" data-type="%s" data-value="%s">%s</a></li>',
                                                    $filters['second_term']['tax'] == 'kolekcje' ? add_query_arg('kolekcja', $term['slug']) : add_query_arg('kategoria', $term['slug']),
                                                    catch_filters($current_vars, $filters['second_term']['tax'], $term['slug'],),
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

                                    sort($attribute['terms'], SORT_NATURAL | SORT_FLAG_CASE );

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
                                                <a href="%s" data-value="%s" data-type="%s" class="filter %s">%s</a>
                                            </li>',
                                            $url,
                                            $term,
                                            $attribute['slug'],
                                            catch_filters($current_vars, $attribute['slug'], $term),
                                            $term
                                            
                                        );
                                    };

                                    echo '</ul>';

                                echo '</div>';    

                            }; ?>

                        </div>

                    <?php endif ?>

                    <?php if($filters['current']['promocje'] !== 'tak') : ?>

                    <div class="onsale-filter">
                        <div>
                            <span>Promocje</span>
                            <ul data-filter="onsale" data-title="W promocji">
                                <li>
                                <?php 
                                    echo sprintf(
                                        '<a href="%s" data-value="%s" data-type="%s" class="filter">%s</a>',
                                        '',
                                        'tak',
                                        'onsale',
                                        'tak'
                                    )
                                ?>
                                </li>
                            </ul>
                        </div>
                    </div>    

                    <div class="order-filter">
                        <div>
                            <span>Sortowanie</span>
                            <ul data-filter="order" data-title="Sortowanie">

                            <?php

                                $orderbys = array(
                                        array(
                                            'title' => 'Sortowanie',
                                            'value' => 'price',
                                            'name'  => 'ceny rosnąco',
                                        ),
                                        array(
                                            'title' => 'Sortowanie',
                                            'value' => 'price-desc',
                                            'name'  => 'ceny malejąco',
                                        ),
                                        array(
                                            'title' => 'Sortowanie',
                                            'value' => 'date',
                                            'name'  => 'od najnowszych',
                                        )
                                );

                                foreach($orderbys as $orderby) {

                                    echo sprintf(
                                        '<li><a href="%s" data-value="%s" data-type="%s" class="filter">%s</a></li>',
                                        '',
                                        $orderby['value'],
                                        'orderby',
                                        $orderby['name']
                                    );
                                }
                            ?>

                            </ul>
                        </div>
                    </div>  
                        
                    <?php endif; ?>

            </div>

        </div>

        <div class="active-filters">
            <span><?php _e('Aktywne filtry', 'decobelo'); ?>:</span>
        </div>

        <pre>
            <?php var_dump($current_vars); ?>
        </pre>

    </div>

    <?php

    $render = ob_get_clean();

    echo $render;

}
// !SECTION

// SECTION Products list
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


    // TODO produkty w promocji
    // 'include'   => $products_on_sale,
    // $products_on_sale = wc_get_product_ids_on_sale();

    $args = array(
        'meta_key'  => '_price',
        'status'    => 'publish',
        'limit'     => $products_per_page,
        'return'    => 'ids',
        'paginate'  => true,
    );

    if(!empty($tax_data)) {
        $args['tax_query'] = $tax_query;
    }

    if($filters['search'] != '' ) {
        $args['s'] = $filters['search'];
    }
    
    if(($filters['onsale']) && ($filters['onsale']['value'] == 'tak')) {
        $args['include'] = wc_get_product_ids_on_sale();
    }

    if($filters['promocje'] == 'tak') {
        $args['include'] = wc_get_product_ids_on_sale();
    }

    if($filters['orderby']) {
        if($filters['orderby']['value'] == 'price') {

            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_price';
            $args['order'] = 'asc';

        } elseif($filters['orderby']['value'] == 'price-desc') {

            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_price';
            $args['order'] = 'desc';

        } elseif($filters['orderby']['value'] == 'date') {

            $args['orderby'] = 'date';
            $args['order'] = 'desc';

        }
    }


    if($filters['page']) {

        $page = (int)$filters['page'];
        $args['paged'] = $page;

    }
    

    $products = wc_get_products($args);

    $args['paginate'] = false;

    $test = wc_get_products($args);

    if($products) {    

        ob_start();
        
        foreach($products->products as $product) {
            setup_postdata($GLOBALS['post'] =& $product);
            wc_get_template_part('content', 'product');
        }
        
        wp_reset_postdata();

        $newproducts = ob_get_clean();

        $output['products'] = $newproducts;
        $output['count'] = $products->max_num_pages;

        return $output;
    
    } else {
    
        echo 'err';
        //do_action('woocommerce_no_products_found');
    }
}
// !SECTION

// REVIEW wygaszanie niedost opcji

/**

function products_for_update($filters=null) {

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

    $tax_data[] = array(
        'taxonomy' 			=> $filters['firstTerm']['type'],
        'field'				=> 'slug',
        'terms'				=> $filters['firstTerm']['value'],
        'include_children'	=> true	
    ); 

    $tax_query = array(
        'relation' => 'AND',
        array(
            $tax_data
        )
    );
   
	$products       = wc_get_products(array(
		'meta_key'  => '_price',
		'status'    => 'publish',
		'limit'     => $products_per_page,
		'page'      => $paged,
		'paginate'  => true,
		'orderby'   => $ordering['orderby'],
		'order'     => $ordering['order'],
		'tax_query' => $tax_query
	));

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

 */