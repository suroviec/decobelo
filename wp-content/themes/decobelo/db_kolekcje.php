<?php
/**
 *
 * 	Template Name: Strona kolekcji (wszystkich) 
 * 
 */

get_header();
?>

	<main id="primary" class="site-main">

	<header class="woocommerce-products-header">
		<h1 class="woocommerce-products-header__title page-title"><?php _e('Kolekcje produktów', 'decobelo'); ?></h1>
	</header>

	<?php custom_menu('kolekcje', 'Kolekcje', true, true); ?>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();