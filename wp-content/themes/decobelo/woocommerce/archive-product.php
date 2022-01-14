<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );

echo get_sidebar();

?>


<?php 
	global $post;
	if($post->ID == 233) : ?>

<header class="woocommerce-products-header">
	<h1 class="woocommerce-products-header__title page-title">Kategorie produktów</h1>
</header>

<?php custom_menu('product_cat', 'Produkty', true, true); ?>



<?php else : ?>


<header class="woocommerce-products-header">
	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
	<?php endif; ?>

	<?php
	/**
	 * Hook: woocommerce_archive_description.
	 *
	 * @hooked woocommerce_taxonomy_archive_description - 10
	 * @hooked woocommerce_product_archive_description - 10
	 */
	do_action( 'woocommerce_archive_description' );
	?>
</header>

	


	<?php 
		// pobranie akt kategorii / kolekcji

		global $wp_query;
		$query = $wp_query->query_vars;
		$current = array();
		$current_tax = $query['taxonomy'];
		$current_slug = $query['term'];


		$list_of_vars = list_of_vars($current_tax);

		$current_vars = array();	

		foreach($list_of_vars as $query_var => $query_var_data) {
			if(!empty(get_query_var($query_var))) {
				$current_vars[$query_var]['tax'] = $list_of_vars[$query_var]['tax'];
				$current_vars[$query_var]['values'] = explode(",", get_query_var($query_var));
			};
		};
	
		$args = array(
			'current_tax' 	=> $current_tax, 
			'current_slug' 	=> $current_slug,
			'search' 		=> $query['s'],
			'promotion'		=> 'yes' 
		);

		$current_filters = get_current_filters($args);

		render_filters($current_filters, $current_vars);		

	?>



<?php

// custom loop

if(!function_exists('wc_get_products')) {
    return;
  }

	$paged                   = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
	$ordering                = WC()->query->get_catalog_ordering_args();
	$ordering['orderby']     = array_shift(explode(' ', $ordering['orderby']));
	$ordering['orderby']     = stristr($ordering['orderby'], 'price') ? 'meta_value_num' : $ordering['orderby'];
	$products_per_page       = apply_filters('loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());

	$tax_data = array();

	$tax_data[] = array(
		array(
			'taxonomy' 			=> $current_tax,
			'field'				=> 'slug',
			'terms'				=> $current_slug,
			'include_children'	=> true
		)	
	);

	$args = array();

	if( !empty($current_vars) == true ) {

		foreach( $current_vars as $var => $data ) {


			if ($var == 'sortowanie') { // orderby

				if($data['values'][0] == 'cena-rosnaco') {
		
					$args['orderby'] = 'meta_value_num';
					$args['meta_key'] = '_price';
					$args['order'] = 'asc';
		
				} elseif($data['values'][0] == 'cena-malejaco') {
		
					$args['orderby'] = 'meta_value_num';
					$args['meta_key'] = '_price';
					$args['order'] = 'desc';
		
				} elseif($data['values'][0] == 'od-najnowszych') {
		
					$args['orderby'] = 'date';
					$args['order'] = 'desc';
		
				}
				
			} else if ($var == 'promocje') {

				if ($data['values'][0] == 'tak') {
					
					$args['include'] = wc_get_product_ids_on_sale();

				}

			} else { // taksonomie glowne i atrybuty

				$tax_data[] = array(
					array(
						'taxonomy' 			=> $data['tax'],	
						'field'				=> 'slug',
						'terms'				=> $data['values'],
						'include_children'	=> true
					)	
				);

			}

		};

	};

	$tax_query = array(
        'relation' => 'AND',
        array(
            $tax_data
        )
    );

	$args['meta_key'] 	= '_price';
	$args['status'] 	= 'publish';
	$args['limit']		= $products_per_page;
	$args['page'] 		= $paged;
	$args['paginate'] 	= true;
	$args['return'] 	= 'ids';
	



	if(!empty($tax_data)) {
        $args['tax_query'] = $tax_query;
    }


	$products = wc_get_products($args);

	if($products) {

		//do_action('woocommerce_before_shop_loop');

		woocommerce_product_loop_start();
			
		foreach($products->products as $product) {

			$post_object = get_post($product);
			setup_postdata($GLOBALS['post'] =& $post_object);
			wc_get_template_part('content', 'product');
		}

		wp_reset_postdata();

		woocommerce_product_loop_end();

		do_action('woocommerce_after_shop_loop');
	} else {
		do_action('woocommerce_no_products_found');
	}

endif;

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
//do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );
