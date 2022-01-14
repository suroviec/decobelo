<?php
/**
 * 	Template Name: Strona promocji
 *	pochodna archive-product.ph
 * 
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
<header class="woocommerce-products-header">
	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h1 class="woocommerce-products-header__title page-title"><?php _e('Produkty w promocji', 'decobelo')?></h1>
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
			'promocje'		=> 'tak'
		);

		$current_filters = get_current_filters($args);

		render_filters($current_filters, $current_vars);

	?>


<?php
if ( woocommerce_product_loop() ) {

	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 * @hooked woocommerce_result_count - 20
	 * @hooked woocommerce_catalog_ordering - 30
	 */
	do_action( 'woocommerce_before_shop_loop' );

	woocommerce_product_loop_start();

	if(!function_exists('wc_get_products')) {
		return;
	}

	$paged                   = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
	$ordering                = WC()->query->get_catalog_ordering_args();
	$ordering['orderby']     = array_shift(explode(' ', $ordering['orderby']));
	$ordering['orderby']     = stristr($ordering['orderby'], 'price') ? 'meta_value_num' : $ordering['orderby'];
	$products_per_page       = apply_filters('loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());

	$args = array(
        'meta_key'  => '_price',
        'status'    => 'publish',
        'limit'     => $products_per_page,
        'return'    => 'ids',
        'paginate'  => true,
		'include' 	=> wc_get_product_ids_on_sale()
    );
	
	$products = wc_get_products($args);

	if($products) {    
        
        foreach($products->products as $product) {
            setup_postdata($GLOBALS['post'] =& $product);
            wc_get_template_part('content', 'product');
        }
        
        wp_reset_postdata();
    
    } else {
    
        do_action('woocommerce_no_products_found');
    }

	woocommerce_product_loop_end();


	$total = $products->total;
	$products_per_page = apply_filters('loop_shop_per_page', wc_get_default_products_per_row() * wc_get_default_product_rows_per_page());

	if($products_per_page <= $total) {

		ob_start();

		?>
		
		<div id="load-more">
			<button class="mainbtn">Więcej produktów</button>
		</div>

		<?php

		echo ob_get_clean();

	}

	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	//do_action( 'woocommerce_after_shop_loop' );
} else {
	/**
	 * Hook: woocommerce_no_products_found.
	 *
	 * @hooked wc_no_products_found - 10
	 */
	do_action( 'woocommerce_no_products_found' );
}

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
