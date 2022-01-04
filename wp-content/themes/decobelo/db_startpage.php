<?php
/**
 * Template Name: Strona startowa
 *
 * @package Decobelo
 */

get_header();
?>

	<main id="primary" class="site-main">

	<section id="promowane-kolekcje">
		<div id="start-boxes-1" class="g">
			<div class="b1">
				<?php 
					$cta = get_post_meta($post->ID, 'startowa_cta')[0];
					
				?>
					<span class="title"><?php echo $cta[0]; ?></span>
					<span class="content"><?php echo $cta[1]; ?></span>
					<a href="<?php echo get_permalink(8); ?>" class="ar"><?php echo $cta[2]; ?></a>
			</div>

			<?php
				$collections = get_post_meta($post->ID, 'start_collections')[0];
				shuffle($collections);
				$i = 2;
				foreach(array_slice($collections, 0, 3) as $collection) {
					echo sprintf(
						'<div class="b%s">
						<a href="%s" title="%s">
						<img src="%s">
							<span class="name">%s</span>
						</a>
						</div>',
						$i,
						get_term_link(get_term($collection)->term_id),
						get_term($collection)->name . ' - '. __('Zobacz produkty z kolekcji', 'decobelo'),
						wp_get_attachment_url(get_term_meta($collection, 'img')[0]),
						get_term($collection)->name
					);		
					$i++;
				}
			?>
		</div>	
		<a href="" class="ar black right-b"><?php _e('Zobacz wszystkie kolekcje', 'decobelo'); ?></a>
	</section>
	
	<section id="info-1" class="info-12">
		<span class="bigheader">
			Bezpieczne produkty produkowane w Polsce</span>
		<span class="content">
			<span class="text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vulputate purus, sit amet hendrerit enim. Sed nec egestas felis. Duis sagittis rhoncus erat sollicitudin aliquam. In rhoncus nulla in dui iaculis fermentum. </span>
			<a href="" class="mainbtn">Zobacz jak pracujemy</a>
		</span>
	</section>

	<section id="kategorie">

		<span class="header"><?php _e('Kategorie produktów', 'decobelo'); ?></span>
		<?php start_products_block('product_cat', 'Produkty', true); ?>

		
		<!---
		
		<span class="header"><?php _e('Kategorie produktów', 'decobelo'); ?></span>
		<div id="start-boxes-2" class="g">
			<?php 
				$categories = get_terms(array(
						'taxonomy'  => 'product_cat',
						'exclude' 	=> array(15),
						'parent'    => 0,
						'hide_empty'=> false,
						'fields' 	=> 'id=>name',
						'offset'	=> 0
					)
				);

				foreach($categories as $id => $name) {
					/***
					echo $id .','. $name . '<br>';
					echo '<pre>';
					
					var_dump(get_term_meta($id, 'short_desc'));
					var_dump(get_term_link($id));
					echo '</pre>';
					*/
					echo sprintf(
						'<div>
							<a href="%s" title="%s">
								<img src="%s" title="%s"/>
								<span class="small-title">%s</span>
								<span class="short-desc">%s</span>
							</a>
						</div>',
						get_term_link($id),
						__('Zobacz produkty z kategorii','decobelo').' '.$name,
						wp_get_attachment_url(get_term_meta($id, 'thumbnail_id')[0]),
						$name,
						$name,
						get_term_meta($id, 'short_desc')[0]
					);
				}
			
			?>	
				
		</div>

		--->
	</section>

	<section id="info-2" class="info-22">
		<div class="">
			<?php echo wp_get_attachment_image(258, 'full'); ?>
		</div>
		<div class="dark">
			<span class="bigheader">
				Bezpieczne produkty produkowane w Polsce</span>
			<span class="content">
				<span class="text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut vulputate purus, sit amet hendrerit enim. Sed nec egestas felis. Duis sagittis rhoncus erat sollicitudin aliquam. In rhoncus nulla in dui iaculis fermentum. </span>
				<a href="" class="mainbtn">Zobacz jak pracujemy</a>
			</span>
		</div>
	</section>
	
	<section id="promocje">
		<span class="header"><?php _e('Wybrane produkty w promocji', 'decobelo'); ?></span>
		<a href="<?php echo get_permalink(60); ?>" title="<?php _e('Zobacz produkty w promocji', 'decobelo'); ?>" class="ar black right-t"><?php _e('Zobacz wszystkie produkty w promocji', 'decobelo'); ?></a>
		<?php echo do_shortcode('[sale_products limit="6"  orderby="rand" order="rand"]'); ?>
	</section>

	<!--

	<section id="polecane">
		<span class="header"><?php _e('Polecane produkty', 'decobelo'); ?></span>
		<a href="" class="ar black right-t"><?php _e('Zobacz wszystkie polecane produkty', 'decobelo'); ?></a>
		<?php echo do_shortcode('[featured_products limit="6"  orderby="rand" order="rand"]'); ?>
	</section>

	-->

	</main><!-- #main -->

<?php
//get_sidebar();
get_footer();
