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

		echo '<pre>';

		$list_of_vars = list_of_vars($current_tax);

		$current_vars = array();	

		foreach($list_of_vars as $query_var => $query_var_data) {
			if(!empty(get_query_var($query_var))) {
				$current_vars[$query_var]['tax'] = $list_of_vars[$query_var]['tax'];
				$current_vars[$query_var]['values'] = explode(",", get_query_var($query_var));
			};
		};
		

		//var_dump($list_of_vars);
		echo '---</br>';
		var_dump($current_vars);

		//var_dump($query);
		echo '</pre>';
	
		/**
		
		if(false == $current_filters = get_transient($current_tax . '_' . $current_slug . '_filters')) {
			$current_filters = get_current_filters($current_tax, $current_slug);
			set_transient($current_tax . '_' . $current_slug . '_filters', $current_filters, YEAR_IN_SECONDS );
			var_dump($current_filters);
		} else {
			var_dump($current_filters);
		}
		 */

		
		$args = array(
			'current_tax' 	=> $current_tax, 
			'current_slug' 	=> $current_slug,
			'search' 		=> $query['s'],
			'promotion'		=> 'yes' 
		);

		$current_filters = get_current_filters($args);

		/**
		echo '<pre>';
		var_dump($current_filters);s
		echo '</pre>';
		 */
		render_filters($current_filters);		
	?>



<?php

// default loop

/**

if ( woocommerce_product_loop() ) {

	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 * @hooked woocommerce_result_count - 20
	 * @hooked woocommerce_catalog_ordering - 30
	 */
	//do_action( 'woocommerce_before_shop_loop' );

	woocommerce_product_loop_start();

	

	if ( wc_get_loop_prop( 'total' ) ) {

		

		while ( have_posts() ) {

			

			the_post();

			/**
			 * Hook: woocommerce_shop_loop.
			 */
			

			do_action( 'woocommerce_shop_loop' );

			wc_get_template_part( 'content', 'product' );
		}
	}

	woocommerce_product_loop_end();

	/**
	 * Hook: woocommerce_after_shop_loop.
	 *
	 * @hooked woocommerce_pagination - 10
	 */
	do_action( 'woocommerce_after_shop_loop' );
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
