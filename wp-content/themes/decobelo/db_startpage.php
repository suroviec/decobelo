<?php
/**
 * Template Name: Strona startowa
 *
 * @package Decobelo
 */

get_header();
?>

	<main id="primary" class="site-main">

	<?php $info = get_post_meta($post->ID, 'info')[0]; ?>

	<!--

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

				$slider = get_post_meta($post->ID, 'slides')[0][0];
				
				echo sprintf(
					'<div class="b2">
					<a href="%s" title="%s">
						%s
						%s
						<div class="slider-content">
							<span class="small-header">%s</span>
							<span class="text">%s</span>
							<button class="mainbtn">Więcej</button>
						</div>
					</a>
					</div>',
					$slider['url'],
					$slider['link_name'],
					wp_get_attachment_image($slider['img']['desktop'], 'desktop', false, array('class' => 'desktop')),
					wp_get_attachment_image($slider['img']['mobile'], 'mobile', false, array('class' => 'mobile')),
					$slider['title'],
					$slider['content']
				);		

			?>
		</div>	
	</section>

				$slider['title'],
				$slider['content'],
				$slider['url'],
				$slider['link_name'],
				$slider['link_name']

	-->

	<section id="promowane-kolekcje">
		<div id="start-boxes-1" class="g">
			<div class="b1">
				<?php 
					$s = get_post_meta($post->ID, 'slides')[0][0];
				?>
					<span class="title"><?php echo $s['title']; ?></span>
					<span class="content"><?php echo $s['content']; ?></span>
					<a href="<?php echo $s['url']; ?>" class="ar"><?php echo $s['link_name']; ?></a>
			</div>

			<?php

				$slider = get_post_meta($post->ID, 'slides')[0][0];
				
				echo sprintf(
					'<div class="b2">
					<a href="%s" title="%s">
						%s
						%s
						<div class="slider-content">
							<span class="small-header">%s</span>
							<span class="text">%s</span>
							<button class="mainbtn">Więcej</button>
						</div>
					</a>
					</div>',
					$slider['url'],
					$slider['link_name'],
					wp_get_attachment_image($slider['img']['desktop'], 'desktop', false, array('class' => 'desktop')),
					wp_get_attachment_image($slider['img']['mobile'], 'mobile', false, array('class' => 'mobile')),
					$slider['title'],
					$slider['content']
				);		

			?>
		</div>	
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

	

	<section id="bannery">

	<?php

		for($i = 1; $i < 3; $i++) {

			$slider = get_post_meta($post->ID, 'slides')[0][$i];

			echo sprintf(
				'<div class="">
				<div>
					%s
					%s
					<div class="slider-content">
						<span class="small-header">%s</span>
						<span class="text">%s</span>
						<a class="mainbtn" href="%s" title="%s">%s</a>
					</div>
				</div>
				</div>',
				wp_get_attachment_image($slider['img']['desktop'], 'desktop', false, array('class' => 'desktop')),
				wp_get_attachment_image($slider['img']['mobile'], 'mobile', false, array('class' => 'mobile')),
				$slider['title'],
				$slider['content'],
				$slider['url'],
				$slider['link_name'],
				$slider['link_name']
			);


		}		

	?>

	</section>

	<!--
	
	<section id="info-1" class="info-12">
		<span class="bigheader">
			<?php echo $info[0]['title']; ?></span>
		<span class="content">
			<span class="text"><?php echo $info[0]['content']; ?></span>
			<a href="<?php echo get_site_url(). '/' . $info[0]['url']; ?>" class="mainbtn"><?php echo $info[0]['linkname']; ?></a>
		</span>
	</section>

	-->

	<section id="promocje">
		<span class="header"><?php _e('Wybrane produkty w promocji', 'decobelo'); ?></span>
		<a href="<?php echo get_permalink(60); ?>" title="<?php _e('Zobacz wszystkie', 'decobelo'); ?>" class="smallbtn1 flr"><?php _e('Zobacz wszystkie promocje', 'decobelo'); ?></a>
		<?php echo do_shortcode('[sale_products limit="8"  orderby="rand" order="rand"]'); ?>
	</section>


	<!---

	<section id="kolekcje">

		<span class="header"><?php _e('Kolekcje produktów', 'decobelo'); ?></span>

		<?php custom_menu('kolekcje', 'Kolekcje', true, true); ?>
		
	</section>

	--->
	

	

	<section id="info-2" class="info-22">
		<div class="">
			<?php 
			$info_img_id = $info[1]['img'];
			echo wp_get_attachment_image($info_img_id, 'full'); ?>
		</div>
		<div class="dark">
			<span class="bigheader">
				<?php echo $info[1]['title']; ?></span>
			<span class="content">
				<span class="text"><?php echo $info[1]['content']; ?></span>
				<a href="<?php echo get_site_url(). '/' . $info[1]['url']; ?>" class="mainbtn"><?php echo $info[1]['linkname']; ?></a>
			</span>
		</div>
	</section>

	<section>
		<div class="desc">
			<?php the_content(); ?>
		</div>
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
