<?php
/**
 * Template Name: Strona kontaktu
 *
 * @package Decobelo
 */

get_header();
?>

	<main id="primary" class="site-main">

		<h1>asdasd</h1>
		<div class="contact-data">
			<ul>
				<?php 
					$data = get_post_meta($post->ID, 'contact_data')[0];
				?>
				<li>
					Zadzwoń do nas
					<?php 
						echo sprintf(
							'<a href="tel:+48%s">%s</a>',
							$data[0],
							$data[0]
						)
					?>
				</li>	
				<li>
					Wyślij e-mail:
					<?php 
						echo sprintf(
							'<a href="mailto:%s">%s</a>',
							$data[1],
							$data[1]
						)
					?>
				</li>	
				<li>
					Odwiedź nas:
					<?php 
						echo wpautop($data[2]); 
						echo '<a target="_blank" title="'. __('Nawigacja Google Maps','decobelo').'" href="https://www.google.com/maps/dir//Inventini+Sp.+z+o.+o.,+Towarowa+14,+44-338+Jastrz%C4%99bie-Zdr%C3%B3j/@49.939918,18.5598193,17z/data=!4m9!4m8!1m0!1m5!1m1!1s0x47114fb6087e3339:0x4acc073e5bfc1584!2m2!1d18.562008!2d49.939918!3e0">'.__('Wskazówki dojazdu','decobelo').'</a>';
					?>					
				</li>
			</ul>
		</div>

		<?php var_dump(get_post_meta($post->ID, 'contact_data')); ?>
	</main><!-- #main -->

<?php
//get_sidebar();
get_footer();